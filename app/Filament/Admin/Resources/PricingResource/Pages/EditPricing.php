<?php

namespace App\Filament\Admin\Resources\PricingResource\Pages;

use App\Filament\Admin\Resources\PricingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPricing extends EditRecord
{
    protected static string $resource = PricingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
