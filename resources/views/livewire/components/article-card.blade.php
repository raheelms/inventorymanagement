<div>
    <section class="py-6">
        <div class="bg-white border border-gray-300 rounded-2xl p-5 transition-all duration-300 hover:border-indigo-600">
            <div class="flex items-center justify-center mb-4">
                <!-- Article Image -->
                <img 
                    src="{{ asset('storage/' . $article->images[0]) }}" 
                    alt="{{ $article->title }}" 
                    class="w-80 h-48 object-cover rounded-lg"
                />
            </div>
            
            <h4 class="font-normal text-md leading-8 text-black transition-all duration-500 group-hover:text-teal-700">
                {{ $article->title }}
            </h4>
            <div class="h-12 font-light text-sm leading-6 text-gray-500 mb-1">
                @if ($article->content)
                    <p>
                        {{ Str::limit(tiptap_converter()->asText($article->content), 50) }}
                    </p>
                @endif
            </div>
            
            <div class="flex items-center justify-between font-medium mb-4">
                <h6 class="text-sm text-gray-500">By {{ $article->user->name }}</h6>
                <span class="text-sm text-indigo-600">{{ __('Last updated: ') }}{{ \Carbon\Carbon::parse($article->updated_at)->format('F j, Y') }}</span>
            </div>

            <!-- 'View All' button centered below articles -->
            <a wire:navigate href="{{ route('article.show', ['article' => $article->slug]) }}" 
                class="bg-gray-900 hover:bg-gray-800 text-white font-semibold p-2 w-40 flex justify-center items-center mx-auto cursor-pointer border border-gray-300 shadow-sm rounded transition-all duration-300 ">
                {{ __('Read More') }}
            </a>            

        </div>
    </section> 

</div>

