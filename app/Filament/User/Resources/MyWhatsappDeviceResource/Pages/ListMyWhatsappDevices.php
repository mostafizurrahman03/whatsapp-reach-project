<?php

namespace App\Filament\User\Resources\MyWhatsappDeviceResource\Pages;

use App\Filament\User\Resources\MyWhatsappDeviceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

// Import widget
use App\Filament\Widgets\MyWhatsappDeviceStats;

class ListMyWhatsappDevices extends ListRecords
{
    protected static string $resource = MyWhatsappDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MyWhatsappDeviceStats::class, // Widget properly registered
        ];
    }
}
