<?php

namespace App\Filament\User\Resources\BulkMediaMessageResource\Pages;

use App\Filament\User\Resources\BulkMediaMessageResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\BulkMediaMessage;
use App\Models\BulkMediaMessageRecipient;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;

class CreateBulkMediaMessage extends CreateRecord
{
    protected static string $resource = BulkMediaMessageResource::class;

    protected function handleRecordCreation(array $data): BulkMediaMessage
    {
        // 1. Save main message
        $message = BulkMediaMessage::create([
            'user_id'   => auth()->id(),
            'device_id' => $data['device_id'],
            'message'   => $data['message'] ?? '',
            'caption'   => $data['caption'] ?? '',
            'media_url' => $data['media_url'] ?? null,
            'is_sent'   => false,
        ]);

        // 2. Prepare recipients from Repeater
        $recipients = collect($data['recipients'] ?? [])->pluck('number')->toArray();

        // 3. Prepare recipients from CSV if uploaded
        if (!empty($data['recipients_csv'])) {
            $csvPath = storage_path('app/public/recipients/' . basename($data['recipients_csv']));
            if (file_exists($csvPath)) {
                $csvNumbers = array_map('trim', array_map('str_getcsv', file($csvPath)));
                // Flatten 2D array and remove empty values
                $csvNumbers = array_filter(array_merge(...$csvNumbers));
                $recipients = array_merge($recipients, $csvNumbers);
            }
        }

        // 4. Save recipients to DB
        foreach ($recipients as $number) {
            BulkMediaMessageRecipient::create([
                'bulk_media_message_id' => $message->id,
                'number' => $number,
                'is_sent' => false,
            ]);
        }

        // 5. Send messages to API
        try {
            $http = Http::withHeaders(['Accept' => 'application/json']);

            $items = [];
            foreach ($recipients as $number) {
                $item = ['number' => $number, 'message' => $data['message'] ?? ''];
                if (!empty($data['media_url'])) {
                    $item['mediaUrl'] = asset('storage/messages/' . basename($data['media_url']));
                    if (!empty($data['caption'])) {
                        $item['caption'] = $data['caption'];
                    }
                }
                $items[] = $item;
            }

            $payload = ['delayMs' => 300, 'items' => $items];

            $response = $http->post("http://43.231.78.204:3333/api/device/{$data['device_id']}/send-batch", $payload);

            if ($response->successful()) {
                // Mark all recipients as sent
                BulkMediaMessageRecipient::where('bulk_media_message_id', $message->id)
                    ->update(['is_sent' => true]);
                $message->update(['is_sent' => true]);

                Notification::make()
                    ->title('Messages sent successfully')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Failed to send messages')
                    ->danger()
                    ->body('WhatsApp API error')
                    ->send();
            }
        } catch (\Exception $e) {
            \Log::error('WhatsApp API Error: ' . $e->getMessage());
            Notification::make()
                ->title('Error Sending Messages')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }

        return $message;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
