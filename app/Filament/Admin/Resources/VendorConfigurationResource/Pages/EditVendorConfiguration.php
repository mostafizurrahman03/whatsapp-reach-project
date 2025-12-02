<?php

namespace App\Filament\Admin\Resources\VendorConfigurationResource\Pages;

use App\Filament\Admin\Resources\VendorConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVendorConfiguration extends EditRecord
{
    protected static string $resource = VendorConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
