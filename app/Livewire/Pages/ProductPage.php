<?php

namespace App\Livewire\Pages;

use app\Helpers\CartManagement;
use App\Enums\ProductStatus;
use App\Livewire\Partials\Navbar;
use App\Models\Product;
use Illuminate\View\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Livewire;

#[Title('Product Details')]
class ProductPage extends Component
{
    // use LivewireAlert;
    public $slug;
    public $quantity = 1;
    public $product;

    public function mount($slug)
    {
        $this->product = Product::where('slug', $slug)->first();
    }

    public function increaseQty()
    {
        $this->quantity++;
    }

    public function decreaseQty()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    // Add product to cart method
    public function addToCart($product_id)
    {
        $total_count = CartManagement::addItemToCartWithQty($product_id, $this->quantity);
        $this->dispatch('update-cart-count', total_count: $total_count)->to(Navbar::class);
        $this->alert('success', 'Product added to the cart successfully!', [
            'position' => 'bottom-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    #[Layout('components.layouts.app')]
    public function render(): View
    {
        return view('livewire.pages.product-page', [
            'product' => Product::where('slug', $this->slug)->firstOrFail()
        ]);
    }
}
