<?php

namespace App\Filament\Admin\Resources\ContactInformationResource\Pages;

use App\Filament\Admin\Resources\ContactInformationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContactInformation extends EditRecord
{
    protected static string $resource = ContactInformationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
