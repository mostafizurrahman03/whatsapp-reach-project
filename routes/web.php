<?php

// use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
// Route::get('/user', function () {
//     return redirect()->route('filament.user.auth.login');
// });
// Route::get('/', function () {
//     return redirect()->route('filament.admin.auth.login');
// });



use Illuminate\Support\Facades\Route;

use App\Http\Livewire\HomePage;
use App\Http\Livewire\FeaturesPage;
use App\Http\Livewire\PricingPage;
use App\Http\Livewire\ContactPage;
use App\Http\Livewire\WhatsAppIntegrationPage;
use App\Http\Controllers\ContactInformationController;
use App\Http\Controllers\ClientMessageController;

Route::get('/user', function () {
    return redirect()->route('filament.user.auth.login');
});
Route::get('/admin', function () {
    return redirect()->route('filament.admin.auth.login');
});


// Livewire routes
Route::get('/', HomePage::class)->name('home');
Route::get('/features', FeaturesPage::class)->name('features');
Route::get('/pricing', PricingPage::class)->name('pricing');
Route::get('/contact', ContactPage::class)->name('contact');
Route::get('/whats-app-integration', WhatsAppIntegrationPage::class)->name('whats-app-integration');
    
// Information for the website
Route::get('/contact-information', [ContactInformationController::class, 'index'])->name('contact-information');
Route::post('/client-message', [ClientMessageController::class, 'submit'])->name('client-message');