<?php

namespace App\Filament\User\Resources\SendMediaMessageResource\Pages;

use App\Filament\User\Resources\SendMediaMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSendMediaMessages extends ListRecords
{
    protected static string $resource = SendMediaMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
