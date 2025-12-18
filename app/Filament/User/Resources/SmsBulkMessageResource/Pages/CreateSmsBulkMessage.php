<?php

// namespace App\Filament\User\Resources\SmsBulkMessageResource\Pages;

// use App\Filament\User\Resources\SmsBulkMessageResource;
// use Filament\Resources\Pages\CreateRecord;
// use App\Services\SmsSenderService;
// use Illuminate\Support\Facades\DB;


// class CreateSmsBulkMessage extends CreateRecord
// {
//      protected static string $resource = SmsBulkMessageResource::class;

//     protected function handleRecordCreation(array $data): \App\Models\SmsBulkMessage
//     {
//         return DB::transaction(function () use ($data) {

//             // Calculate totals
//             $numbers = collect($data['recipients'])
//                 ->pluck('number')
//                 ->filter()
//                 ->values()
//                 ->toArray();

//             $data['total_recipients'] = count($numbers);
//             $data['status'] = 'processing';

//             // Create record
//             $record = static::getModel()::create($data);

//             try {
//                 // Get vendor credentials dynamically
//                 $vendor = $record->vendorConfiguration;

//                 $service = new SmsSenderService();

//                 $responses = $service->sendBulk(
//                     $vendor->api_key,
//                     $vendor->secret_key,
//                     $record->service,
//                     $numbers,
//                     $record->content
//                 );

//                 // Analyze response
//                 $success = $responses['success'] ?? 0;
//                 $failed = $responses['failed'] ?? 0;

//                 $record->update([
//                     'success_count' => $success,
//                     'failed_count' => $failed,
//                     'status' => $failed > 0 ? 'partial' : 'sent',
//                     'sent_at' => now(),
//                     'response' => $responses,
//                 ]);

//             } catch (\Throwable $e) {

//                 $record->update([
//                     'status' => 'failed',
//                     'failed_count' => count($numbers),
//                     'response' => ['error' => $e->getMessage()],
//                 ]);
//             }

//             return $record;
//         });
//     }
// }



namespace App\Filament\User\Resources\SmsBulkMessageResource\Pages;

use App\Filament\User\Resources\SmsBulkMessageResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\ClientConfiguration;
use App\Models\VendorConfiguration;
use App\Jobs\SmsBulkSendJob;

class CreateSmsBulkMessage extends CreateRecord
{
    protected static string $resource = SmsBulkMessageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Resolve client
        $client = ClientConfiguration::where('user_id', auth()->id())
            ->where('is_active', true)
            ->firstOrFail();

        // Resolve vendor
        $vendor = VendorConfiguration::where('service_id', $data['service_id'])
            ->whereJsonContains('sender_ids', $data['sender_id'])
            ->where('is_active', true)
            ->firstOrFail();

        // Calculate totals
        $numbers = collect($data['recipients'])
            ->pluck('number')
            ->filter()
            ->values()
            ->toArray();

        $data['client_configuration_id'] = $client->id;
        $data['vendor_configuration_id'] = $vendor->id;
        $data['total_recipients'] = count($numbers);
        $data['cost'] = count($numbers) * $client->rate_per_sms;
        $data['status'] = 'pending';

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;

        // Dispatch job for async sending
        SmsBulkSendJob::dispatch($record);
    }
}
