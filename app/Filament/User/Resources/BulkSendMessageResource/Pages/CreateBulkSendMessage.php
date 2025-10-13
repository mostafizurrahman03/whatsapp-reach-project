<?php

namespace App\Filament\User\Resources\BulkSendMessageResource\Pages;

use App\Filament\User\Resources\BulkSendMessageResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\BulkSendMessage;
use App\Models\BulkSendMessageRecipient;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;

class CreateBulkSendMessage extends CreateRecord
{
    protected static string $resource = BulkSendMessageResource::class;

    protected function handleRecordCreation(array $data): BulkSendMessage
    {
        // 1. Save main message
        $message = BulkSendMessage::create([
            'user_id'   => auth()->id(),
            'device_id' => $data['device_id'],
            'message'   => $data['message'] ?? '',
            'is_sent'   => false,
            'total_recipients' => 0,
            'sent_count' => 0,
            'failed_count' => 0,
            'status' => 'pending',
        ]);

        // Helper: normalize numbers
        $normalizeNumber = function ($number) {
            $number = preg_replace('/\D/', '', $number);
            if (str_starts_with($number, '0')) {
                $number = '88' . $number;
            } elseif (!str_starts_with($number, '88')) {
                $number = '88' . $number;
            }
            return $number;
        };

        // Helper: split and validate numbers
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

        // 2. Recipients from TagsInput
        $recipients = collect($data['recipients_list'] ?? [])
            ->flatMap(fn($n) => $splitNumbers($n))
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // 3. Recipients from CSV if uploaded
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
                $recipients = array_unique(array_merge($recipients, $csvNumbers));
            }
        }

        // Validate recipients
        $recipients = array_filter($recipients, fn($number) => strlen($number) === 13);

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
            BulkSendMessageRecipient::create([
                'bulk_send_message_id' => $message->id,
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
                $info = $response->json() ?? [];
                $deviceConnected = (
                    ($info['state'] ?? '') === 'CONNECTED' ||
                    ($info['status'] ?? '') === 'connected' ||
                    ($info['connected'] ?? false) === true
                );
            }
        } catch (\Exception $e) {
            \Log::error('Device status check failed', ['error' => $e->getMessage()]);
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

        // 6. Send bulk messages using API
        $successCount = 0;
        $failCount = 0;

        try {
            $bulkItems = [];
            foreach ($recipients as $number) {
                $bulkItems[] = [
                    'number' => $number,
                    'message' => $data['message'],
                ];
            }

            $payload = [
                'delayMs' => 400, // 0.4 sec delay
                'items' => $bulkItems,
            ];

            $bulkResponse = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post("http://43.231.78.204:3333/api/device/{$deviceId}/send-batch", $payload);

            if ($bulkResponse->successful()) {
                $successCount = count($recipients);
                foreach ($recipients as $number) {
                    BulkSendMessageRecipient::where('bulk_send_message_id', $message->id)
                        ->where('number', $number)
                        ->update(['is_sent' => true, 'sent_at' => now()]);
                }

            } else {
                $failCount = count($recipients);
                $responseData = $bulkResponse->json() ?? [];
                \Log::error("Bulk send failed", is_array($responseData) ? $responseData : ['response' => $responseData]);
            }
        } catch (\Exception $e) {
            $successCount = 0;
            $failCount = count($recipients);
            \Log::error("Bulk send exception", ['error' => $e->getMessage()]);
        }

        // 7. Update message status
        $finalStatus = $successCount > 0
            ? ($successCount === count($recipients) ? 'completed' : 'partial')
            : 'failed';

        $message->update([
            'is_sent' => $successCount > 0,
            'sent_count' => $successCount,
            'failed_count' => $failCount,
            'status' => $finalStatus
        ]);

        // 8. Notify user
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
}
 