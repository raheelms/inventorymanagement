<?php

use App\Http\Controllers\InvoiceController;
use App\Livewire\Pages\ArticlePage;
use App\Livewire\Pages\CancelPage;
use App\Livewire\Pages\CartPage;
use App\Livewire\Pages\CategoriesPage;
use App\Livewire\Pages\CheckoutPage;
use App\Livewire\Pages\CollectionPage;
use App\Livewire\Pages\CollectionsPage;
use App\Livewire\Pages\Page;
use App\Livewire\Pages\ProductPage;
use App\Livewire\Pages\SuccessPage;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('/articles/{article:slug}', ArticlePage::class)->name('article.show');
Route::get('/categories/{slug}', CategoriesPage::class)->name('category.show');
Route::get('/products/{slug}', ProductPage::class)->name('product-page.show');
Route::get('/collections', CollectionsPage::class);
Route::get('/collections/{slug}', CollectionPage::class)->name("collections.show");
Route::get('/cart', CartPage::class)->name('cart');

Route::middleware(['auth:customer'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('profile', 'profile')->name('profile');
    Route::get('/checkout', CheckoutPage::class)->name('checkout');
    Route::get('/payment/success', SuccessPage::class)->name('payment.success');
    Route::get('/payment/cancel', CancelPage::class)->name('payment.cancel');
    Route::post('/webhook', function(Request $request) {
        return app(PaymentService::class)->handleWebhook($request);
     })->name('stripe.webhook');

    // Route::view('my-orders', 'my-orders')->name('my-orders');
    // Route::get('/my-order/{order}', OrderDetailPage::class)->name('orderDetail');
    // Route::view('my-wishlist', 'my-wishlist')->name('my-wishlist');  
});

Route::get('/print/inventory-invoice/{id}', [InvoiceController::class, 'printInventoryInvoice'])->name('print.inventory_invoice');
Route::get('/download/inventory-invoice/{id}', [InvoiceController::class, 'downloadInventoryInvoice'])->name('download.inventory_invoice');

Route::get('pages/{page:slug}', Page::class)->name('page.show');