<?php

namespace App\Filament\User\Resources\BulkSendMessageResource\Pages;

use App\Filament\User\Resources\BulkSendMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBulkSendMessage extends EditRecord
{
    protected static string $resource = BulkSendMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
