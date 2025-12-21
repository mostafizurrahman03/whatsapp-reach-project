<?php

// namespace App\Jobs;

// use App\Models\SmsBulkMessage;
// use App\Services\SmsSenderService;
// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Queue\SerializesModels;

// class SmsBulkSendJob implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//     protected SmsBulkMessage $message;

//     public function __construct(SmsBulkMessage $message)
//     {
//         $this->message = $message;
//     }

//     public function handle(): void
//     {
//         $record = $this->message;

//         $numbers = collect($record->recipients)
//             ->pluck('number')
//             ->filter()
//             ->values()
//             ->toArray();

//         $vendor = $record->vendorConfiguration;
//         $service = new SmsSenderService();

//         try {
//             $responses = $service->sendBulk(
//                 $vendor->api_key,
//                 $vendor->secret_key,
//                 $record->service,
//                 $numbers,
//                 $record->content
//             );

//             $success = $responses['success'] ?? 0;
//             $failed  = $responses['failed'] ?? 0;

//             $record->update([
//                 'success_count' => $success,
//                 'failed_count' => $failed,
//                 'status' => $failed > 0 ? 'partial' : 'sent',
//                 'sent_at' => now(),
//                 'response' => $responses,
//             ]);

//         } catch (\Throwable $e) {
//             $record->update([
//                 'status' => 'failed',
//                 'failed_count' => count($numbers),
//                 'response' => ['error' => $e->getMessage()],
//             ]);
//         }
//     }
// }




namespace App\Jobs;

use App\Models\SmsBulkMessage;
use App\Services\SmsSenderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SmsBulkSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;

    public function __construct(
        public SmsBulkMessage $message
    ) {}

    public function handle(): void
    {
        $vendor = $this->message->vendorConfiguration;

        $numbers = collect($this->message->recipients)
            ->pluck('number')
            ->filter()
            ->values();

        $service = new SmsSenderService($vendor);

        $success = 0;
        $failed  = 0;
        $logs    = [];

        collect($numbers)->chunk($vendor->tps)->each(function ($chunk) use (
            &$success,
            &$failed,
            &$logs,
            $service
        ) {
            foreach ($chunk as $number) {
                try {
                    $sent = $service->send(
                        $this->message->sender_id,
                        $number,
                        $this->message->content
                    );

                    $sent ? $success++ : $failed++;

                    $logs[] = [
                        'number' => $number,
                        'status' => $sent ? 'sent' : 'failed',
                    ];
                } catch (\Throwable $e) {
                    $failed++;
                    $logs[] = [
                        'number' => $number,
                        'status' => 'error',
                        'error'  => $e->getMessage(),
                    ];
                }
            }

            sleep(1); // TPS control
        });

        $this->message->update([
            'success_count' => $success,
            'failed_count'  => $failed,
            'status'        => $failed > 0 ? 'partial' : 'sent',
            'sent_at'       => now(),
            'response'      => $logs,
        ]);
    }

    public function failed(\Throwable $e): void
    {
        $this->message->update([
            'status'   => 'failed',
            'response' => ['error' => $e->getMessage()],
        ]);
    }
}

