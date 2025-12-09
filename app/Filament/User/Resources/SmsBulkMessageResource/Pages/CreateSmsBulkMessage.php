<?php

namespace App\Filament\User\Resources\SmsBulkMessageResource\Pages;

use App\Filament\User\Resources\SmsBulkMessageResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\SmsSenderService;

class CreateSmsBulkMessage extends CreateRecord
{
    protected static string $resource = SmsBulkMessageResource::class;

    protected function afterCreate(): void
    {
        $message = $this->record->content;

        // Extract only numbers from repeater field
        $numbers = collect($this->record->recipients)
            ->pluck('number')
            ->filter() // remove empty items
            ->values()
            ->toArray();

        $service = new SmsSenderService();

        // TODO: Replace with real client credentials (dynamic)
        $clientApiKey = "client_api_key_here";
        $clientSecret = "client_secret_here";

        try {

            // Send bulk via service
            $responses = $service->sendBulk(
                $clientApiKey,
                $clientSecret,
                $this->record->service,
                $numbers,
                $message
            );

            // Save result
            $this->record->update([
                'status' => 'sent',
                'response' => $responses,
            ]);

        } catch (\Exception $e) {

            // Save failure response
            $this->record->update([
                'status' => 'failed',
                'response' => ['error' => $e->getMessage()],
            ]);
        }
    }
}
