<?php

namespace App\Filament\User\Resources\SendMediaMessageResource\Pages;

use App\Filament\User\Resources\SendMediaMessageResource;
use App\Models\SendMediaMessage;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;

class CreateSendMediaMessage extends CreateRecord
{
    protected static string $resource = SendMediaMessageResource::class;

    protected function handleRecordCreation(array $data): SendMediaMessage
    {
        // 1. Save to local DB
        $message = SendMediaMessage::create([
            'number'    => $data['number'],
            'message'   => $data['message'],
            'media_url' => $data['attachments'][0] ?? null, // first file for DB reference
            'is_sent'   => false,
        ]);

        try {
            // 2. Prepare attachments for API (multipart)
            $multipart = [];
            if (!empty($data['attachments'])) {
                foreach ($data['attachments'] as $file) {
                    $filePath = storage_path('app/public/messages/' . $file);
                    if (file_exists($filePath)) {
                        $multipart[] = Http::attach('file[]', file_get_contents($filePath), $file);
                    }
                }
            }

            // 3. Send API request
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->attach(...$multipart)
              ->post("http://43.231.78.204:3333/api/device/{$data['device_id']}/send-media", [
                  'number'  => $data['number'],
                  'message' => $data['message'] ?? '',
              ]);

            // 4. Handle response
            if ($response->successful()) {
                $message->update(['is_sent' => true]);

                Notification::make()
                    ->title('Message Sent Successfully')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Failed to Send Message')
                    ->danger()
                    ->body('WhatsApp server error: ' . $response->body())
                    ->send();
            }
        } catch (\Exception $e) {
            \Log::error('WhatsApp API Error: ' . $e->getMessage());

            Notification::make()
                ->title('Error Sending Message')
                ->danger()
                ->send();
        }

        return $message;
    }
}
