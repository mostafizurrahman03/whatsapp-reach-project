<?php

namespace App\Filament\Admin\Resources\ClientConfigurationResource\Pages;

use App\Filament\Admin\Resources\ClientConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateClientConfiguration extends CreateRecord
{
    protected static string $resource = ClientConfigurationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
