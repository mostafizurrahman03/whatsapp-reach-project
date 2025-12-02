<?php

namespace App\Filament\User\Resources\ProfileResource\Pages;

use App\Filament\User\Resources\ProfileResource;
use App\Models\User;
use Filament\Resources\Pages\EditRecord;

class EditProfile extends EditRecord
{
    protected static string $resource = ProfileResource::class;

    /**
     * Form load করার আগে users table থেকে name & email নিয়ে আসা
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = User::find($data['user_id'] ?? null);

        if ($user) {
            $data['name']  = $user->name;
            $data['email'] = $user->email;
        }

        return $data;
    }

    /**
     * Form save করার সময় users table update
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = User::find($data['user_id'] ?? null);

        if ($user) {
            $user->update([
                'name'  => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
            ]);
        }

        unset($data['name'], $data['email']); // profile table এ save না করা

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        // Save করার পরও Profile View page এ redirect
        return $this->getResource()::getUrl('view', ['record' => $this->record->id]);
    }
}

