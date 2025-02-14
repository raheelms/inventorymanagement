<div class="max-w-[85rem] min-h-[75rem] mx-auto py-6 px-4 sm:px-6 lg:px-8">

    <section>
        <div class="w-full">

        <section class="w-full relative overflow-hidden bg-white dark:bg-gray-800 lg:py-28 py-16">
            <div class="px-6 xl:px-20">
                <div class="flex flex-col md:flex-row w-full gap-8">
                    <div class="w-full md:max-w-[176px] md:border-r md:border-gray-200">
                        <ul class="tab-nav flex flex-col md:items-start items-center lg:gap-4 gap-4">
                            @foreach ($article->categories as $category)
                                <livewire:components.tag-component :category="$category" /> 
                            @endforeach

                            @foreach ($articles as $article)
                                <li>
                                    <a wire:navigate href="{{ route('article.show', $article) }}" class="font-medium text-base leading-4 text-emerald-600">                                    
                                        {{ $article->title }} Terms of Use
                                    </a>
                                </li>        
                            @endforeach
                            <li>
                            <a href="#" class="font-medium text-base leading-4 text-emerald-600">Terms of Use</a>
                            </li>
                            <li>
                            <a href="#" class="font-medium text-base leading-4 text-gray-500 hover:text-emerald-600">Privacy Policy</a>
                            </li>
                            <li>
                            <a href="#" class="font-medium text-base leading-4 text-gray-500 hover:text-emerald-600">Customer Conduct</a>
                            </li>
                            <li>
                            <a href="#" class="font-medium text-base leading-4 text-gray-500 hover:text-emerald-600">General Terms</a>
                            </li>
                            <li>
                            <a href="#" class="font-medium text-base leading-4 text-gray-500 hover:text-emerald-600">Services & Account</a>
                            </li>
                        </ul>
                    </div>
                    <div class="w-full tab-panel max-md:px-4">

                        <h2 class="font-manrope font-bold lg:text-3xl text-3xl text-gray-900 mb-2">
                            {{ $article->title }}
                        </h2>

                        <div class="flex items-center gap-2 lg:mb-6 mb-6">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.0054 8V12.5322C12.0054 12.8286 12.1369 13.1098 12.3645 13.2998L15 15.5M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22Z" stroke="#4F46E5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <p class="font-medium text-md leading-5 text-indigo-600">
                                Last updated: {{ \Carbon\Carbon::parse($article->updated_at)->format('F j, Y') }}
                            </p>                            
                        </div>

                        <div>
                            @if ($article->image)
                                <img src="/{{ $article->image->path }}" alt="{{ $article->image->alt_text }}" class="rounded-sm">
                            @endif
                        </div>
                        
                        @if ($article->content)
                            <p class="font-normal text-md leading-4 text-gray-500 lg:mb-4 mb-4">
                                {!! tiptap_converter()->asHTML($article->content ?? '', toc: true, maxDepth: 4) !!}
                            </p>
                        @endif

                    </div>
                </div>
            </div>
        </section>

        </div>
    </section>

</div>