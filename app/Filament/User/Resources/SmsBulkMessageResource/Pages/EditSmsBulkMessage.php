<?php

namespace App\Filament\User\Resources\SmsBulkMessageResource\Pages;

use App\Filament\User\Resources\SmsBulkMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSmsBulkMessage extends EditRecord
{
    protected static string $resource = SmsBulkMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
