<?php

namespace App\Filament\User\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\Auth;
use App\Models\MyWhatsappDevice;

class MyWhatsappDeviceStats extends BaseWidget
{
    protected function getCards(): array
    {
        $userId = Auth::id();

        return [
            Card::make('Total Devices', MyWhatsappDevice::where('user_id', $userId)->count())
                ->description('All devices registered')
                ->color('primary')
                ->icon('heroicon-o-device-phone-mobile'),

            Card::make('Connected Devices', MyWhatsappDevice::where('user_id', $userId)->where('status', 'connected')->count())
                ->description('Devices currently connected')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Card::make('Pending Devices', MyWhatsappDevice::where('user_id', $userId)->where('status', 'pending')->count())
                ->description('Devices awaiting connection')
                ->color('warning')
                ->icon('heroicon-o-clock'),

            Card::make('Disconnected Devices', MyWhatsappDevice::where('user_id', $userId)->where('status', 'disconnected')->count())
                ->description('Devices disconnected')
                ->color('danger')
                ->icon('heroicon-o-x-circle'),
        ];
    }

    public static function canView(): bool
    {
        // Show this widget on the dashboard for all users
        return true;
    }
}
