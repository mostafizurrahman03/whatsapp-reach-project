<?php

namespace App\Filament\User\Resources\BulkMediaMessageResource\Pages;

use App\Filament\User\Resources\BulkMediaMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBulkMediaMessage extends EditRecord
{
    protected static string $resource = BulkMediaMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
