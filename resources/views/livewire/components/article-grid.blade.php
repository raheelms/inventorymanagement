<div>
    @if($this->articles->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($this->articles as $article)
                <livewire:components.article-card :article="$article" :key="$article->id" />
            @endforeach
        </div>
        @if($this->show_load_more)
            <div>
                <div class="text-center">
                    <button wire:click="loadMore" class="bg-gray-900 hover:bg-gray-800 text-white font-semibold rounded px-4 py-2">
                        {{ __('Load More') }}
                    </button>
                </div>
            </div>
        @endif
    @endif
</div>



