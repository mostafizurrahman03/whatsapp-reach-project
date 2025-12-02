<?php

namespace App\Filament\User\Resources\CampaignLeadResource\Pages;

use App\Filament\User\Resources\CampaignLeadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCampaignLead extends EditRecord
{
    protected static string $resource = CampaignLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        
        return $this->getResource()::getUrl('index');
    }
}
