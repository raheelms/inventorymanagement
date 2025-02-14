<?php

namespace App\Livewire\Components;

use App\Models\Product as ProductModel;
use Illuminate\View\View;
use Livewire\Component;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProductGrid extends Component
{
    public Collection $products;
    public int $limit;
    public $collection;
    public $sort_by;
    public bool $show_load_more = false;
    protected $listeners = ['statusUpdated' => 'loadProducts'];  // Listen for the statusUpdated event

    public function mount(): void
    {
        $this->loadMore();
    }

    // Method to reload products when statusUpdated event is emitted
    public function loadProducts()
    {
        $this->loadMore();  // Reload the products when the event is triggered
    }

    public function loadMore(): void
    {   
        $offset = 0;
        if (isset($this->products)) {
            $offset = $this->products->count();
        }

        $newProducts = $this->sort_by === 'popular'
            ? $this->getProductsByViews($offset)
            : $this->getProductsBySortOrder($offset);

        if (isset($this->products)) {
            $this->products = $this->products->merge($newProducts);
        } else {
            $this->products = $newProducts;
        }

        $this->show_load_more = $newProducts->count() >= $this->limit;
    }

    private function getBaseQuery(): Builder
    {
        return ProductModel::with('collections')
            ->isPublished()
            ->whereHas('collections', function ($query) {
                $query->whereIn('collections.id', (array) $this->collection);
            });
    }

    private function getProductsByViews(int $offset = 0): Collection
    {
        return $this->getBaseQuery()
        ->orderByViews()
        ->skip($offset)
        ->limit($this->limit)
        ->get();
    }

    private function getProductsBySortOrder(int $offset = 0): Collection
    {    
        $validColumns = ['created_at', 'updated_at'];
        $sortBy = in_array($this->sort_by, $validColumns) ? $this->sort_by : 'created_at';

            return $this->getBaseQuery()
                ->orderBy($sortBy, 'desc')
                ->skip($offset)
                ->limit($this->limit)
                ->get();
    }    

    public function render(): View
    {
        return view('livewire.components.product-grid');
    }
}
