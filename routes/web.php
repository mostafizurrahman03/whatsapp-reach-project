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

use App\Livewire\HomePage;
use App\Livewire\FeaturesPage;
use App\Livewire\PricingPage;
use App\Livewire\ContactPage;
use App\Livewire\WhatsAppIntegrationPage;
use App\Http\Controllers\ContactInformationController;
use App\Http\Controllers\ClientMessageController;
use App\Http\Controllers\FeatureController;

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
// Route::get('/contact-information', [ContactInformationController::class, 'index'])->name('contact-information');
// Route::post('/client-message', [ClientMessageController::class, 'submit'])->name('client-message');

Route::get('/contact', ContactPage::class)->name('contact');
// Route::get('/features', [FeatureController::class, 'index'])->name('features');

