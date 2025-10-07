<?php

namespace App\Filament\User\Resources\BulkSendMessageRecipientResource\Pages;

use App\Filament\User\Resources\BulkSendMessageRecipientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBulkSendMessageRecipients extends ListRecords
{
    protected static string $resource = BulkSendMessageRecipientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
