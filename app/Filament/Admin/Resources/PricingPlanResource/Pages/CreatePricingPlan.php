<?php

namespace App\Filament\Admin\Resources\PricingPlanResource\Pages;

use App\Filament\Admin\Resources\PricingPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePricingPlan extends CreateRecord
{
    protected static string $resource = PricingPlanResource::class;
    /**
     * Redirect to list page after create.
     */
    protected function getRedirectUrl(): string
    {
        // Redirect to the PricingPlanResource index page
        return $this->getResource()::getUrl('index');
    }
}
