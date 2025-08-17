<?php

namespace App\Filament\User\Resources\MyWhatsappDeviceResource\Pages;

use App\Filament\User\Resources\MyWhatsappDeviceResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Http;

class CreateMyWhatsappDevice extends CreateRecord
{
    protected static string $resource = MyWhatsappDeviceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // clientId safe করা
        $safeDeviceId = preg_replace('/[^a-zA-Z0-9_-]/', '_', $data['device_id']);

        // API তে পাঠানো
        $response = Http::post('http://43.231.78.204:3333/api/device', [
            'deviceId' => $safeDeviceId,
        ]);

        if ($response->failed()) {
            throw new \Exception('Device create failed: ' . $response->body());
        }

        // API response থেকে status merge করা
        // $data['device_id'] = $safeDeviceId;
        // $data['status'] = $response->json()['status'] ?? 'pending';

        return $data; // Filament নিজেই DB তে save করবে
    }
}
