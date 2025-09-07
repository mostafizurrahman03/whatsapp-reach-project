<?php

namespace App\Filament\User\Resources\BulkSendMessageResource\Pages;

use App\Filament\User\Resources\BulkSendMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBulkSendMessages extends ListRecords
{
    protected static string $resource = BulkSendMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
