<?php

namespace App\Filament\Admin\Resources\ClientConfigurationResource\Pages;

use App\Filament\Admin\Resources\ClientConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClientConfiguration extends EditRecord
{
    protected static string $resource = ClientConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
