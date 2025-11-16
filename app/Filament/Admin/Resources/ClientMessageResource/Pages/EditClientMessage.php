<?php

namespace App\Filament\Admin\Resources\ClientMessageResource\Pages;

use App\Filament\Admin\Resources\ClientMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClientMessage extends EditRecord
{
    protected static string $resource = ClientMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
