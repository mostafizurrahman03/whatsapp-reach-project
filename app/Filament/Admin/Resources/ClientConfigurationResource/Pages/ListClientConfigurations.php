<?php

namespace App\Filament\Admin\Resources\ClientConfigurationResource\Pages;

use App\Filament\Admin\Resources\ClientConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClientConfigurations extends ListRecords
{
    protected static string $resource = ClientConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
