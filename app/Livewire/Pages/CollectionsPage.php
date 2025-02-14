<?php

namespace App\Livewire\Pages;

use App\Models\Collection;
use App\Models\Product;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Products')]
class CollectionsPage extends Component
{
    use WithPagination;
    #[Url]
    public $selected_collections = [];

    #[Url]
    public $featured;

    #[Url]
    public $onsale;

    #[Url]
    public $price_range = 2000;
    public $min_price = 0;      // Manual minimum price input
    public $max_price = 2000;   // Manual maximum price input

    #[Url]
    public $sort = "latest";

    public function render(): View
    {
        $productQuery = Product::query()->where('is_visible', 1);

        if (!empty($this->selected_collections)) {
            $productQuery->whereHas('collections', function ($query) {
                $query->whereIn('collections.id', $this->selected_collections);
            });
        }

        if ($this->featured) {
            $productQuery->where('is_featured', $this->featured);
        }

        if ($this->onsale) {
            $productQuery->where('on_sale', $this->onsale);
        }

        if ($this->price_range) {
            $productQuery->whereBetween('price', [$this->min_price, $this->price_range]);
        }

        // Apply sorting
        if ($this->sort === "az") {
            $productQuery->orderBy('name', 'asc');
        } elseif ($this->sort === "za") {
            $productQuery->orderBy('name', 'desc');
        } elseif ($this->sort === "latest") {
            $productQuery->orderBy('created_at', 'desc');
        } elseif ($this->sort === "oldest") {
            $productQuery->orderBy('created_at', 'asc');
        } elseif ($this->sort === "price_low_to_high") {
            $productQuery->orderBy('price', 'asc');
        } elseif ($this->sort === "price_high_to_low") {
            $productQuery->orderBy('price', 'desc');
        }

        if ($this->sort == "price") {
            $productQuery->orderBy('price');
        }

        return view(
            'livewire.pages.collections-page',
            [
                'products'    => $productQuery->paginate(9),
                'collections'  => Collection::where('is_visible', 1)->get(),
            ]
        );
    }
}
