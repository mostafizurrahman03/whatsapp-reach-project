<?php

namespace App\Filament\User\Resources\ProfileResource\Pages;

use App\Filament\User\Resources\ProfileResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewProfile extends ViewRecord
{
    protected static string $resource = ProfileResource::class;

    // Header actions (Edit button)
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
