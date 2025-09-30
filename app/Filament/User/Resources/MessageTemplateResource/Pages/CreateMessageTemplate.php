<?php

namespace App\Filament\User\Resources\MessageTemplateResource\Pages;

use App\Filament\User\Resources\MessageTemplateResource;
use App\Models\MessageTemplate;
use Filament\Resources\Pages\CreateRecord;

class CreateMessageTemplate extends CreateRecord
{
    protected static string $resource = MessageTemplateResource::class;

    protected function handleRecordCreation(array $data): MessageTemplate
    {
        // Non-admin safeguard
        if (!isset($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }

        return MessageTemplate::create($data);
    }
}
