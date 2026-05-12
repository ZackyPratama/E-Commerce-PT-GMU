<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// protected routes for customer dashboard, profile, etc. can be added here
Route::middleware(['auth:customer'])->group(function(){
    Route::post('/logout', function(){
        auth()->guard('customer')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});