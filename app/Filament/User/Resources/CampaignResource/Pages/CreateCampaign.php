<?php

namespace App\Filament\User\Resources\CampaignResource\Pages;

use App\Filament\User\Resources\CampaignResource;
use Filament\Actions;
use App\Models\Campaign;
use Filament\Resources\Pages\CreateRecord;

class CreateCampaign extends CreateRecord
{
    protected static string $resource = CampaignResource::class;

    protected function handleRecordCreation(array $data): Campaign
    {
        // Non-admin safeguard
        if (!isset($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }

        return Campaign::create($data);
    }
}
