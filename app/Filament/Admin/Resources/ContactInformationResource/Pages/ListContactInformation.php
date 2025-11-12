<?php

namespace App\Filament\Admin\Resources\ContactInformationResource\Pages;

use App\Filament\Admin\Resources\ContactInformationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContactInformation extends ListRecords
{
    protected static string $resource = ContactInformationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
