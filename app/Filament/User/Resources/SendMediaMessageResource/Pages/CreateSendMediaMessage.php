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
    $messageData = [
        'number'    => $data['number'],
        'message'   => $data['message'] ?? '',
        'media_url' => $data['media_url'] ?? null, // single file
        'is_sent'   => false,
    ];

    $message = SendMediaMessage::create($messageData);

    try {
        $request = Http::withHeaders([
            'Accept' => 'application/json',
        ]);

        $url = "http://43.231.78.204:3333/api/device/{$data['device_id']}/send-media";

        // 2. Attach file if exists
        if (!empty($data['media_url'])) {
            $filePath = storage_path('app/public/messages/' . basename($data['media_url']));

            if (file_exists($filePath)) {
                $response = $request
                    ->attach(
                        'file',
                        fopen($filePath, 'r'),
                        basename($data['media_url'])
                    )
                    ->post($url, [
                        'number'  => $data['number'],
                        'caption' => $data['message'] ?? '',
                    ]);
            } else {
                throw new \Exception("File not found: {$filePath}");
            }
        } else {
            //  if file does not have, then send text
            $response = $request->post($url, [
                'number'  => $data['number'],
                'caption' => $data['message'] ?? '',
            ]);
        }

        // 3. Handle response
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
