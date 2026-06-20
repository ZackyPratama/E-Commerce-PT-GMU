<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MidtransController;
use App\Livewire\CartPage;
use App\Livewire\CheckoutPage;
use App\Livewire\Customer\Dashboard;
use App\Livewire\Customer\OrderDetails;
use App\Livewire\Customer\Profile;
use App\Livewire\Customer\RFQDetail;
use App\Livewire\Customer\RFQList;
use App\Livewire\Homepage;
use App\Livewire\Customer\Orders;
use App\Livewire\ProductDetails;
use App\Livewire\ProductListing;
use Illuminate\Support\Facades\Route;

Route::get('/', Homepage::class)->name('home');

Route::get('products', ProductListing::class)->name('products.index');

Route::get('products/{slug}', ProductDetails::class)->name('products.show');

Route::get('/cart', CartPage::class)->name('cart.index');

// Midtrans webhook route - must be accessible without authentication
Route::post('/webhook/midtrans', [MidtransController::class, 'webhook'])->name('webhook.midtrans');

// protected routes for customer dashboard, profile, etc. can be added here
Route::middleware(['auth:customer'])->group(function () {
    Route::get('/my-account', Dashboard::class)->name('customer.dashboard');
    Route::get('/checkout', CheckoutPage::class)->name('checkout');

    Route::get('/my-account/orders', Orders::class)->name('customer.orders');
    Route::get('/my-account/orders/{id}', OrderDetails::class)->name('customer.orders.show');
    Route::get('/my-account/profile', Profile::class)->name('customer.profile');

    // RFQ Routes
    Route::get('/my-account/rfqs', RFQList::class)->name('customer.rfqs.index');
    Route::get('/my-account/rfqs/{id}', RFQDetail::class)->name('customer.rfqs.show');

    // Invoice download
    Route::get('/my-account/orders/{order}/invoice', [\App\Http\Controllers\InvoiceController::class, 'download'])->name('customer.orders.invoice');

    // Checkout payment routes
    Route::get('/checkout/payment/{order}', [CheckoutController::class, 'showPayment'])->name('checkout.payment');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/pending/{order}', [CheckoutController::class, 'pending'])->name('checkout.pending');
    Route::get('/checkout/error/{order}', [CheckoutController::class, 'error'])->name('checkout.error');

    //logout route
    Route::post('/logout', function () {
        auth()->guard('customer')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});
