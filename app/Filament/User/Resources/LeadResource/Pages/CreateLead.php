<?php

namespace App\Filament\User\Resources\LeadResource\Pages;

use App\Filament\User\Resources\LeadResource;
use App\Models\Lead; 
use Filament\Resources\Pages\CreateRecord;

class CreateLead extends CreateRecord
{
    protected static string $resource = LeadResource::class;
    
    protected function handleRecordCreation(array $data): Lead
    {
        // Non-admin safeguard
        if (!isset($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }

        return Lead::create($data);
    }
}
