<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', function () {
    return redirect()->route('filament.admin.auth.login');
});


Route::get('/user', function () {
    return redirect()->route('filament.user.auth.login');
});
