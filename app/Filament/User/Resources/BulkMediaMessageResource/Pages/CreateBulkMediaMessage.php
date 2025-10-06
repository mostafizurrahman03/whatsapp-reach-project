<?php

namespace App\Filament\User\Resources\BulkMediaMessageResource\Pages;

use App\Filament\User\Resources\BulkMediaMessageResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\BulkMediaMessage;
use App\Models\BulkMediaMessageRecipient;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class CreateBulkMediaMessage extends CreateRecord
{
    protected static string $resource = BulkMediaMessageResource::class;

    protected function handleRecordCreation(array $data): BulkMediaMessage
    {
        // 1. Save main message with is_sent=false
        $message = BulkMediaMessage::create([
            'user_id'   => auth()->id(),
            'device_id' => $data['device_id'],
            'message'   => $data['message'] ?? '',
            'caption'   => $data['caption'] ?? '',
            'media_url' => $data['media_url'] ?? null,
            'is_sent'   => false,
            'total_recipients' => 0,
            'sent_count' => 0,
            'failed_count' => 0,
        ]);

        // Normalize number helper
        $normalizeNumber = function ($number) {
            $number = preg_replace('/\D/', '', $number);
            if (str_starts_with($number, '0')) {
                $number = '88' . $number;
            } elseif (!str_starts_with($number, '88')) {
                $number = '88' . $number;
            }
            return $number;
        };

        // Split merged numbers
        $splitNumbers = function ($string) use ($normalizeNumber) {
            $numbers = [];
            preg_match_all('/(?:\+88|88)?(01[3-9]\d{8})/', $string, $matches);
            foreach ($matches[1] as $num) {
                $normalized = $normalizeNumber($num);
                if (strlen($normalized) === 13) {
                    $numbers[] = $normalized;
                }
            }
            return array_unique($numbers);
        };

        // 2. Prepare recipients from TagsInput
        $recipients = collect($data['recipients'] ?? [])
            ->flatMap(function ($n) use ($splitNumbers) {
                return $splitNumbers($n);
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // 3. Prepare recipients from CSV if uploaded
        if (!empty($data['recipients_csv'])) {
            $csvPath = storage_path('app/public/' . $data['recipients_csv']);
            if (file_exists($csvPath)) {
                $csvData = array_map('str_getcsv', file($csvPath));
                $csvNumbers = [];
                foreach ($csvData as $row) {
                    foreach ($row as $col) {
                        $col = trim($col);
                        if (!empty($col)) {
                            $csvNumbers = array_merge($csvNumbers, $splitNumbers($col));
                        }
                    }
                }
                $recipients = array_merge($recipients, $csvNumbers);
                $recipients = array_unique($recipients);
            }
        }

        // Validate recipients
        $recipients = array_filter($recipients, function ($number) {
            return strlen($number) === 13;
        });

        if (empty($recipients)) {
            Notification::make()
                ->title('No valid recipients found')
                ->danger()
                ->body('Please provide valid Bangladeshi phone numbers (01XXXXXXXXX format)')
                ->send();
            return $message;
        }

        // 4. Save recipients to DB
        foreach ($recipients as $number) {
            BulkMediaMessageRecipient::create([
                'bulk_media_message_id' => $message->id,
                'number' => $number,
                'is_sent' => false,
            ]);
        }

        $message->update(['total_recipients' => count($recipients)]);

        // 5. Check device status
        $deviceId = $data['device_id'];
        $deviceConnected = false;

        try {
            $response = Http::timeout(25)
                ->get("http://43.231.78.204:3333/api/device/{$deviceId}/status");
            
            if ($response->successful()) {
                $deviceStatusInfo = $response->json();
                
                $deviceConnected = (
                    (isset($deviceStatusInfo['state']) && $deviceStatusInfo['state'] === 'CONNECTED') ||
                    (isset($deviceStatusInfo['status']) && $deviceStatusInfo['status'] === 'connected') ||
                    (isset($deviceStatusInfo['connected']) && $deviceStatusInfo['connected'] === true)
                );
            }
        } catch (\Exception $e) {
            \Log::error('Device status check failed: ' . $e->getMessage());
            Notification::make()
                ->title('Device Connection Error')
                ->danger()
                ->body('Could not connect to device server: ' . $e->getMessage())
                ->send();
            return $message;
        }

        if (!$deviceConnected) {
            Notification::make()
                ->title('Device Not Ready')
                ->danger()
                ->body('WhatsApp device is not connected. Please check device connection.')
                ->send();
            return $message;
        }

        // 6. Prepare media file path
        $mediaFilePath = null;
        if (!empty($data['media_url'])) {
            $mediaPath = $data['media_url'];
            
            if (Storage::disk('public')->exists($mediaPath)) {
                $mediaFilePath = storage_path('app/public/' . $mediaPath);
                \Log::info('Media file found:', ['path' => $mediaFilePath]);
            } else {
                \Log::error('Media file not found:', ['path' => $mediaPath]);
                Notification::make()
                    ->title('Media File Not Found')
                    ->danger()
                    ->body('The media file could not be found in storage.')
                    ->send();
                return $message;
            }
        }

        // 7. Send messages using the WORKING approach from SendMediaMessage
        $successCount = 0;
        $failCount = 0;

        foreach ($recipients as $index => $number) {
            try {
                $http = Http::withHeaders(['Accept' => 'application/json']);
                
                // Send media if exists (using the WORKING approach)
                if (!empty($mediaFilePath)) {
                    $mediaResponse = $http
                        ->attach('file', fopen($mediaFilePath, 'r'), basename($mediaFilePath))
                        ->post("http://43.231.78.204:3333/api/device/{$deviceId}/send-media", [
                            'number'  => $number,
                            'caption' => $data['caption'] ?? '',
                        ]);

                    \Log::info("Media response for {$number}:", $mediaResponse->json());
                }

                // Send text message separately (using the WORKING approach)
                if (!empty($data['message'])) {
                    $textResponse = Http::withHeaders([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ])->post("http://43.231.78.204:3333/api/device/{$deviceId}/send", [
                        'number'  => $number,
                        'message' => $data['message'],
                    ]);

                    \Log::info("Text response for {$number}:", $textResponse->json());
                }

                // Consider successful if at least one request worked
                $mediaSuccess = empty($mediaFilePath) || ($mediaResponse->successful() ?? true);
                $textSuccess = empty($data['message']) || ($textResponse->successful() ?? true);

                if ($mediaSuccess && $textSuccess) {
                    $successCount++;
                    BulkMediaMessageRecipient::where('bulk_media_message_id', $message->id)
                        ->where('number', $number)
                        ->update(['is_sent' => true, 'sent_at' => now()]);
                } else {
                    $failCount++;
                    \Log::error("Message to {$number} failed");
                }

                // Progress update
                if (($index + 1) % 5 === 0) {
                    $message->update([
                        'sent_count' => $successCount,
                        'failed_count' => $failCount
                    ]);
                }
                
                // Delay between messages
                usleep(3000000); // 3 second delay
                
            } catch (\Exception $e) {
                $failCount++;
                \Log::error("Message to {$number} exception: " . $e->getMessage());
            }
        }

        // 8. Update final status
        $finalStatus = $successCount > 0 ? ($successCount === count($recipients) ? 'completed' : 'partial') : 'failed';
        
        $message->update([
            'is_sent' => $successCount > 0,
            'sent_count' => $successCount,
            'failed_count' => $failCount,
            'status' => $finalStatus
        ]);

        // 9. Send notification with results
        if ($successCount > 0) {
            Notification::make()
                ->title('Messages Sent Successfully')
                ->success()
                ->body("Successfully sent to $successCount recipients. Failed: $failCount")
                ->send();
        } else {
            Notification::make()
                ->title('All Messages Failed')
                ->danger()
                ->body("No messages could be sent. Please check the logs for details.")
                ->send();
        }

        return $message;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // public function mutateFormDataBeforeCreate(array $data): array
    // {
        
    //     dd($data); // dumps like API response
    //     return $data;
    // }

}