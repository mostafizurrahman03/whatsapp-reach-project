<?php

namespace App\Filament\User\Resources\SendMessageResource\Pages;

use App\Filament\User\Resources\SendMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSendMessages extends ListRecords
{
    protected static string $resource = SendMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            
        ];
    }
    
}
