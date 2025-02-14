<?php

namespace App\Livewire\Components;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Product as ProductModel;
use Illuminate\View\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\Attributes\On;

class ProductCard extends Component
{
    // use LivewireAlert;

    public ProductModel $product;

    // Add product to cart method

    #[On('addToCart')]
    public function addToCart($product_id)
    {

        $total_count = CartManagement::addItemToCart($product_id);
        $this->dispatch('update-cart-count', total_count: $total_count)->to(Navbar::class);
        // $this->alert('success', 'Product added to the cart successfully!', [
        //    'position' => 'bottom-end',
        //    'timer' => 3000,
        //    'toast' => true,
        //]);
    }

    public function render(): View
    {
        return view('livewire.components.product-card');
    }
}
