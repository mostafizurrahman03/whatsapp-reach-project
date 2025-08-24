<?php

namespace App\Filament\User\Resources\SendMessageResource\Pages;

use App\Filament\User\Resources\SendMessageResource;
use App\Models\SendMessage;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Http;

class CreateSendMessage extends CreateRecord
{
    // ✅ Must define the resource
    protected static string $resource = SendMessageResource::class;

    // Change the create/save button text in form
    // protected function getFormActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make()
    //             ->label('Send Message'), // Custom button text
    //     ];
    // }
    protected function handleRecordCreation(array $data): SendMessage
    {
        // 1. প্রথমে DB তে save
        $message = SendMessage::create($data);

        // 2. API call (WhatsApp server এ পাঠানো)
        try {
            $response = Http::post("http://43.231.78.204:3333/api/device/{$data['device_id']}/send", [
                'number' => $data['number'],
                'message' => $data['message'],
                'attachments' => $data['attachments'] ?? [],
            ]);

            if ($response->successful()) {
                $message->update(['is_sent' => true]);
            }
        } catch (\Exception $e) {
            // error হলে কিছু log করতে পারেন
        }

        return $message;
    }
}

