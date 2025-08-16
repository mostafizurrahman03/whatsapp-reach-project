<?php

namespace App\Filament\User\Resources\MyWhatsappDeviceResource\Pages;

use App\Filament\User\Resources\MyWhatsappDeviceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyWhatsappDevice extends EditRecord
{
    protected static string $resource = MyWhatsappDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
