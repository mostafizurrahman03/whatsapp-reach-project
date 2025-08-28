<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;
use App\Filament\User\Widgets\MyWhatsappDeviceStats;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.user.pages.dashboard';

    // Add this method inside the class
    public function getWidgets(): array
    {
        return [
            MyWhatsappDeviceStats::class,
            // other widgets...
        ];
    }
    
}
