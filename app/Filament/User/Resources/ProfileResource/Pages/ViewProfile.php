<?php

namespace App\Filament\User\Resources\ProfileResource\Pages;

use App\Filament\User\Resources\ProfileResource;
use App\Models\Profile;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;

class ViewProfile extends ViewRecord
{
    protected static string $resource = ProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    // Automatically resolve the current user's profile
    protected function resolveRecord($key): Model
    {
        // If we have a key (from URL), use it
        if ($key) {
            return parent::resolveRecord($key);
        }

        // Otherwise, get the current user's profile
        return Profile::where('user_id', auth()->id())->firstOrFail();
    }

    // Override to handle direct access without record ID
    public function mount($record = null): void
    {
        if (!$record) {
            // Get current user's profile ID
            $profile = Profile::where('user_id', auth()->id())->first();
            if ($profile) {
                $record = $profile->id;
                // Redirect to the proper URL with record ID
                $this->redirect(static::getResource()::getUrl('view', ['record' => $record]));
                return;
            } else {
                // Redirect to create page if no profile exists
                $this->redirect(static::getResource()::getUrl('create'));
                return;
            }
        }

        parent::mount($record);
    }
}