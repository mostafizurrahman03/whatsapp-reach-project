<?php

namespace App\Filament\Admin\Resources\VendorConfigurationResource\Pages;

use App\Filament\Admin\Resources\VendorConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVendorConfiguration extends CreateRecord
{
    protected static string $resource = VendorConfigurationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
