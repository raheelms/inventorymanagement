<div class="max-w-[85rem] min-h-[75rem] bg-white dark:bg-gray-800 mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        @if ($this->category->articles->count() > 0)
            <h2 class="font-manrope text-4xl font-bold text-gray-900 text-center mb-14">{{ $category->name }} Our popular blogs</h2>
            
            <!-- Grid container for 3 columns -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($category->articles as $article)
                    <livewire:components.article-card :article="$article" :key="$article->id" />
                @endforeach
            </div>
        @endif

    </div>
</div>
