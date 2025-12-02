<?php

namespace App\Filament\User\Resources\CampaignLeadResource\Pages;

use App\Filament\User\Resources\CampaignLeadResource;
use App\Models\Campaign;
use App\Models\CampaignLead;
use Filament\Resources\Pages\CreateRecord;

class CreateCampaignLead extends CreateRecord
{
    protected static string $resource = CampaignLeadResource::class;

    protected function handleRecordCreation(array $data): CampaignLead
    {
        // Non-admin safeguard: campaign_id assign করা
        if (auth()->user()->role !== 'admin') {
            if (!isset($data['campaign_id'])) {
                $data['campaign_id'] = Campaign::where('user_id', auth()->id())->first()?->id;
            }
        }

        return CampaignLead::create($data);
    }

    protected function getRedirectUrl(): string
    {
        
        return $this->getResource()::getUrl('index');
    }
}
