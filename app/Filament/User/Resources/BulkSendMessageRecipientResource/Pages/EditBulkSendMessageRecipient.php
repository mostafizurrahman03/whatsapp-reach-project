<?php

namespace App\Filament\User\Resources\BulkSendMessageRecipientResource\Pages;

use App\Filament\User\Resources\BulkSendMessageRecipientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBulkSendMessageRecipient extends EditRecord
{
    protected static string $resource = BulkSendMessageRecipientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
