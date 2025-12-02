<?php

namespace App\Filament\Admin\Resources\VendorConfigurationResource\Pages;

use App\Filament\Admin\Resources\VendorConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVendorConfigurations extends ListRecords
{
    protected static string $resource = VendorConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
