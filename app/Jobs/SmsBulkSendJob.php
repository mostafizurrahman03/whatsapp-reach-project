<?php

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

    protected SmsBulkMessage $message;

    public function __construct(SmsBulkMessage $message)
    {
        $this->message = $message;
    }

    public function handle(): void
    {
        $record = $this->message;

        $numbers = collect($record->recipients)
            ->pluck('number')
            ->filter()
            ->values()
            ->toArray();

        $vendor = $record->vendorConfiguration;
        $service = new SmsSenderService();

        try {
            $responses = $service->sendBulk(
                $vendor->api_key,
                $vendor->secret_key,
                $record->service,
                $numbers,
                $record->content
            );

            $success = $responses['success'] ?? 0;
            $failed  = $responses['failed'] ?? 0;

            $record->update([
                'success_count' => $success,
                'failed_count' => $failed,
                'status' => $failed > 0 ? 'partial' : 'sent',
                'sent_at' => now(),
                'response' => $responses,
            ]);

        } catch (\Throwable $e) {
            $record->update([
                'status' => 'failed',
                'failed_count' => count($numbers),
                'response' => ['error' => $e->getMessage()],
            ]);
        }
    }
}
