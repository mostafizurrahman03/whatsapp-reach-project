<?php

namespace App\Filament\User\Resources\MessageTemplateResource\Pages;

use App\Filament\User\Resources\MessageTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMessageTemplate extends EditRecord
{
    protected static string $resource = MessageTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
