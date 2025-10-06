<?php

namespace App\Filament\User\Resources\BulkMediaMessageRecipientResource\Pages;

use App\Filament\User\Resources\BulkMediaMessageRecipientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBulkMediaMessageRecipient extends EditRecord
{
    protected static string $resource = BulkMediaMessageRecipientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
