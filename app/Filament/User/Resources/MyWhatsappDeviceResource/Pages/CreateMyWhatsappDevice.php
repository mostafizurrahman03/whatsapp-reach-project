<?php

namespace App\Filament\User\Resources\MyWhatsappDeviceResource\Pages;

use App\Filament\User\Resources\MyWhatsappDeviceResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Http;

class CreateMyWhatsappDevice extends CreateRecord
{
    protected static string $resource = MyWhatsappDeviceResource::class;

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     // clientId safe করা
    //     $safeDeviceId = preg_replace('/[^a-zA-Z0-9_-]/', '_', $data['device_id']);

    //     // API তে পাঠানো
    //     $response = Http::post('http://43.231.78.204:3333/api/device', [
    //         'deviceId' => $safeDeviceId,
    //     ]);

    //     if ($response->failed()) {
    //         throw new \Exception('Device create failed: ' . $response->body());
    //     }

    //     // API response থেকে status merge করা
    //     // $data['device_id'] = $safeDeviceId;
    //     // $data['status'] = $response->json()['status'] ?? 'pending';

    //     return $data; // Filament নিজেই DB তে save করবে
    // }
// protected function mutateFormDataBeforeCreate(array $data): array
// {
//     // Device ID safe
//     $safeDeviceId = preg_replace('/[^a-zA-Z0-9_-]/', '_', $data['device_id']);

//     try {
//         $response = Http::post('http://43.231.78.204:3333/api/device', [
//             'deviceId' => $safeDeviceId,
//         ]);

//         if ($response->failed()) {
//             // Optional: log error
//             \Log::error('API device create failed', [
//                 'deviceId' => $safeDeviceId,
//                 'response' => $response->body(),
//             ]);

//             throw new \Exception('Device create failed on remote server.');
//         }
//     } catch (\Exception $e) {
//         // Filament exception → form will show error
//         throw new \Exception('Device create failed: ' . $e->getMessage());
//     }

//     // Normal Filament create → local DB তেও save হবে
//     return $data;
// }

protected function mutateFormDataBeforeCreate(array $data): array
{
    // Device ID safe
    $safeDeviceId = preg_replace('/[^a-zA-Z0-9_-]/', '_', $data['device_id']);

    try {
        // API call
        $response = Http::post('http://43.231.78.204:3333/api/device', [
            'deviceId' => $safeDeviceId,
        ]);

        if ($response->failed()) {
            // API fail হলে log
            \Log::error('API device create failed', [
                'deviceId' => $safeDeviceId,
                'response' => $response->body(),
            ]);

            throw new \Exception('Device create failed on remote server.');
        }
    } catch (\Exception $e) {
        // ফর্মে error দেখাবে
        throw new \Exception('Device create failed: ' . $e->getMessage());
    }

    // Local DB save এর আগে data modify
    $data['user_id'] = auth()->id(); // logged in user
    $data['status'] = 'pending';     // initial status

    // Filament normal create → local DB তেও save হবে
    return $data;
}
protected function getRedirectUrl(): string
{
    // Redirect to the index (list) page instead of edit
    return MyWhatsappDeviceResource::getUrl('index');
}



}
