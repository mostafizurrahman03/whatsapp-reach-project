<?php

namespace App\Filament\User\Resources\SendMediaMessageResource\Pages;

use App\Filament\User\Resources\SendMediaMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSendMediaMessage extends EditRecord
{
    protected static string $resource = SendMediaMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
