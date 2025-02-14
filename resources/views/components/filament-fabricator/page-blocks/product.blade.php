@aware(['page'])
@props(['heading','description', 'collection', 'limit', 'sort_by', 'show_load_more'])

{{-- THIS IS THE PRODUCT BLOCK --}}
<div class="p-6 bg-stone-200">
    <div class="max-w-[85rem] dark:bg-gray-800 mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto mb-4 sm:px-6 lg:mb-8 lg:px-8">
            
            <h2 class="font-semibold text-lg md:text-3xl md:leading-tight text-gray-900 dark:text-white text-left">
                {{ $heading }}
            </h2>
            <div class="text-left">
                @if(tiptap_converter()->asText($description))
                    <p>
                        {{ tiptap_converter()->asText($description ?? '', ['toc' => true, 'maxDepth' => 4]) }}
                    </p>
                @endif
            </div>

            <livewire:components.product-grid :limit="$limit" :collection="$collection" :sort_by="$sort_by" :show_load_more="$show_load_more" />

        </div>
    </div>    
</div>