<?php

namespace App\Filament\Admin\Resources\FeatureResource\Pages;

use App\Filament\Admin\Resources\FeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFeature extends CreateRecord
{
    protected static string $resource = FeatureResource::class;

    /**
     * Redirect to list page after create.
     */
    protected function getRedirectUrl(): string
    {
        // Redirect to the FeatureResource index page
        return $this->getResource()::getUrl('index');
    }
}

