<?php

namespace App\Filament\User\Resources\SmsBulkMessageResource\Pages;

use App\Filament\User\Resources\SmsBulkMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSmsBulkMessages extends ListRecords
{
    protected static string $resource = SmsBulkMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
