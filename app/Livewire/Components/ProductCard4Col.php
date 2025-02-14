<?php

namespace App\Livewire\Components;

use App\Models\Product as ProductModel;
use Illuminate\View\View;
use Livewire\Component;

class ProductCard4Col extends Component
{
    public ProductModel $product;

    public function render(): View
    {
        return view('livewire.components.product-card-4-col');
    }
}