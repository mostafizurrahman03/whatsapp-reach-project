<?php

namespace App\Filament\User\Resources\SendMessageResource\Pages;

use App\Filament\User\Resources\SendMessageResource;
use App\Models\SendMessage;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;

class CreateSendMessage extends CreateRecord
{
    protected static string $resource = SendMessageResource::class;

    protected function handleRecordCreation(array $data): SendMessage
    {
        // 1. Logged-in user অনুযায়ী message save
        $message = SendMessage::create([
            'user_id'   => auth()->id(),
            'device_id' => $data['device_id'],
            'number'    => $data['number'],
            'message'   => $data['message'] ?? '',
            'is_sent'   => false,
        ]);

        // 2. API call (WhatsApp server)
        try {
            $response = Http::post("http://43.231.78.204:3333/api/device/{$data['device_id']}/send", [
                'number' => $data['number'],
                'message' => $data['message'] ?? '',
                'attachments' => $data['attachments'] ?? [],
            ]);

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
                    ->body('WhatsApp server error')
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
