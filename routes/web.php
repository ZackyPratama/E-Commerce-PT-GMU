<?php

use App\Livewire\Customer\Dashboard;
use App\Livewire\Customer\OrderDetails;
use App\Livewire\Customer\Profile;
use App\Livewire\Homepage;
use App\Livewire\Orders;
use App\Livewire\ProductDetails;
use App\Livewire\ProductListing;
use Illuminate\Support\Facades\Route;

Route::get('/', Homepage::class)->name('home');

Route::get('products',ProductListing::class)->name('products.index');

Route::get('products/{slug}',ProductDetails::class)->name('products.show');


// protected routes for customer dashboard, profile, etc. can be added here
Route::middleware(['auth:customer'])->group(function(){
    Route::get('/my-account',Dashboard::class)->name('customer.dashboard');

    Route::get('/my-account/orders',Orders::class)->name('customer.orders');
    Route::get('/my-account/orders/{id}',OrderDetails::class)->name('customer.orders.show');
    Route::get('/my-account/profile',Profile::class)->name('customer.profile');
    //logout route
    Route::post('/logout', function(){
        auth()->guard('customer')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});
