<?php

namespace App\Filament\User\Resources\BulkMediaMessageRecipientResource\Pages;

use App\Filament\User\Resources\BulkMediaMessageRecipientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBulkMediaMessageRecipients extends ListRecords
{
    protected static string $resource = BulkMediaMessageRecipientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
