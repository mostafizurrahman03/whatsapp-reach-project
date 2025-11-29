<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// use Illuminate\View\View;
use App\Models\ContactInformation;
use Illuminate\Support\Facades\View; // <- এইটা খুব গুরুত্বপূর্ণ


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.layouts.app', function ($view) {
        $data = ContactInformation::latest()->first();
        $view->with([
            'email' => $data->email ?? null,
            'phone' => $data->phone ?? null,
            'address' => $data->address ?? null,
        ]);
    });
    }
}
