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
        $messageData = [
            'user_id'   => auth()->id(),
            'device_id' => $data['device_id'],
            'number'    => $data['number'],
            'message'   => $data['message'] ?? '',
            'caption'   => $data['caption'] ?? '',
            'media_url' => $data['media_url'] ?? null,
            'is_sent'   => false,
        ];

        $message = SendMediaMessage::create($messageData);

        try {
            $deviceId    = $data['device_id'];
            $number      = $data['number'];
            $messageText = $data['message'] ?? '';
            $caption     = $data['caption'] ?? '';

            $http = Http::withHeaders([
                'Accept' => 'application/json',
            ]);

            //  Step 1: Handle media file correctly
            $mediaResponse = null;

            if (!empty($data['media_url'])) {
                // remove possible array wrapping
                $mediaPath = is_array($data['media_url']) ? $data['media_url'][0] : $data['media_url'];

                //  detect folder name dynamically
                if (str_contains($mediaPath, 'templates/')) {
                    $folder = 'templates';
                } else {
                    $folder = 'messages';
                }

                $fileName = basename($mediaPath);
                $filePath = storage_path("app/public/{$folder}/{$fileName}");

                if (!file_exists($filePath)) {
                    throw new \Exception("File not found: {$filePath}");
                }

                $mediaResponse = $http
                    ->attach('file', fopen($filePath, 'r'), $fileName)
                    ->post("http://43.231.78.204:3333/api/device/{$deviceId}/send-media", [
                        'number'  => $number,
                        'caption' => $caption,
                    ]);
            }

            //  Step 2: Send text message separately
            $textResponse = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->post("http://43.231.78.204:3333/api/device/{$deviceId}/send", [
                    'number'  => $number,
                    'message' => $messageText,
                ]);

            //  Step 3: Success or Fail notification
            if (($mediaResponse?->successful() ?? true) && $textResponse->successful()) {
                $message->update(['is_sent' => true]);

                Notification::make()
                    ->title(' Message Sent Successfully')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title(' Failed to Send Message')
                    ->danger()
                    ->body('WhatsApp server error')
                    ->send();
            }

        } catch (\Exception $e) {
            \Log::error('WhatsApp API Error: ' . $e->getMessage());

            Notification::make()
                ->title(' Error Sending Message')
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
