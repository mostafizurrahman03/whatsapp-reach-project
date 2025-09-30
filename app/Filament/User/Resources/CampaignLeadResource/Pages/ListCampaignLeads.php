<?php

namespace App\Filament\User\Resources\CampaignLeadResource\Pages;

use App\Filament\User\Resources\CampaignLeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCampaignLeads extends ListRecords
{
    protected static string $resource = CampaignLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
