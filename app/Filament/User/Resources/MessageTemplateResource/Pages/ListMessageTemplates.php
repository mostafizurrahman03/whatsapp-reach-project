<?php

namespace App\Filament\User\Resources\MessageTemplateResource\Pages;

use App\Filament\User\Resources\MessageTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMessageTemplates extends ListRecords
{
    protected static string $resource = MessageTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
