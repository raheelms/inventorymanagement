<?php

namespace App\Livewire\Pages;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Cart')]
class CartPage extends Component
{
    public $cart_items = [];
    public $grand_total;
    public $tax_rate = 0.21; // 21% tax
    public $tax_amount;
    public $products;

    public function mount()
    {
        $this->cart_items  = CartManagement::getCartItemsFromCookie();        
        $this->grand_total = CartManagement::calculateGrandTotal($this->cart_items);

        // Calculate tax based on the grand total
        $this->tax_amount = $this->grand_total * $this->tax_rate;

        // Get all product IDs from cart items
        $productIds = collect($this->cart_items)->pluck('product_id');

        // Eager load products with their collections
        $this->products = Product::with('collections')->whereIn('id', $productIds)->get()
            ->keyBy('id');
    }

    public function removeItem($product_id)
    {
        $this->cart_items  = CartManagement::removeCarItem($product_id);
        $this->grand_total = CartManagement::calculateGrandTotal($this->cart_items);
        // Recalculate the tax amount based on the updated grand total
        $this->tax_amount = $this->grand_total * $this->tax_rate;
        $this->dispatch('update-cart-count', total_count: count($this->cart_items))->to(Navbar::class);
    }

    public function increaseQty($product_id)
    {
        $this->cart_items  = CartManagement::incrementQuantityToCartItem($product_id);
        $this->grand_total = CartManagement::calculateGrandTotal($this->cart_items);
        // Recalculate the tax amount based on the updated grand total
        $this->tax_amount = $this->grand_total * $this->tax_rate;
    }

    public function decreaseQty($product_id)
    {
        $this->cart_items  = CartManagement::decrementQuantityToCartItem($product_id);
        $this->grand_total = CartManagement::calculateGrandTotal($this->cart_items);
        // Recalculate the tax amount based on the updated grand total
        $this->tax_amount = $this->grand_total * $this->tax_rate;
    }

    public function render()
    {
        return view('livewire.pages.cart-page');
    }
}
