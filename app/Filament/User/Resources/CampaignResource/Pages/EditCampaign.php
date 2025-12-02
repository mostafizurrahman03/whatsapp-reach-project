<?php

namespace App\Filament\User\Resources\CampaignResource\Pages;

use App\Filament\User\Resources\CampaignResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCampaign extends EditRecord
{
    protected static string $resource = CampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    /**
     * Redirect to list page after create.
     */
    protected function getRedirectUrl(): string
    {
        // Redirect to the CampaignResource index page
        return $this->getResource()::getUrl('index');
    }
}
