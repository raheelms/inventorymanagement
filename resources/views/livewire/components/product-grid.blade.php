<div>
    @if($this->products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
            @foreach ($this->products as $product)
                <livewire:components.product-card :product="$product" :key="$product->id" />
            @endforeach
        </div>
        @if($this->show_load_more === 'true')
            <div>
                <div class="text-center mt-1">
                    <button wire:click="loadMore" class="bg-blue-600 text-white font-semibold rounded px-4 py-2">
                        {{ __('Load More') }}
                    </button>
                </div>
            </div>
        @endif
    @endif
</div>


