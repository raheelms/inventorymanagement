
<div class="w-full max-w-[85rem] py-6 px-4 sm:px-6 lg:px-8 mx-auto">
  <section class="py-6 border border-gray-300 bg-gray-50 dark:bg-gray-800 rounded-lg">
    <div class="max-w-7xl mx-auto px-4 lg:py-2 md:px-6">
        
      <!-- Breadcrumb Section -->
      <div class="p-4 border border-gray-200 mb-6 grid grid-cols-1 gap-2">
        <nav class="flex text-sm font-normal text-gray-900 dark:text-gray-400 dark:bg-gray-600 space-x-2">
            <a wire:navigate href="/" class="hover:text-indigo-500">Home</a>
            <span>&gt;</span>
            <a href="/category" class="hover:text-indigo-500">Category</a>
            <span>&gt;</span>
            <a href="/product" class="hover:text-indigo-500">Product Name</a>
        </nav>
      </div>
      
      <div class="flex flex-wrap mb-24 -mx-3">

        <div class="w-full pr-2 lg:w-1/4 lg:block">
          <div class="px-4 pb-4 mb-5 bg-white border border-gray-200 dark:border-gray-900 dark:bg-gray-900">
            <h2 class="text-md font-semibold text-white dark:text-gray-400 bg-[#003953] rounded p-2 mb-2">{{ __('Collections') }}</h2>

            <ul x-show="open" x-transition class="space-y-2">
              @forelse ($collections as $collection)
                <li class="mb-2" wire:key = "{{ $collection->id }}">
                  <label for="{{ $collection->slug }}" class="flex items-center text-gray-600 dark:text-gray-400 ">
                    <input type="checkbox" wire:model.live="selected_collections"
                      id="{{ $collection->slug }}" value="{{ $collection->id }}" class="w-4 h-4 mr-3 rounded border-gray-400 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-600 dark:text-gray-400"> {{ $collection->name }} </span>
                  </label>
                </li>
              @empty
              @endforelse
            </ul>

          </div>

          <div class="p-4 mb-5 bg-white border border-gray-200 dark:bg-gray-900 dark:border-gray-900">
            <h2 class="text-md font-semibold text-white dark:text-gray-400 bg-[#003953] rounded p-2 mb-2">{{ __('Product Status') }}</h2>

            <ul>
              <li class="mb-2">
                <label for="featured" class="flex items-center dark:text-gray-300">
                  <input id="featured" wire:model.live = "featured" type="checkbox" value="1"
                    class="w-4 h-4 mr-3 rounded border-gray-400 text-indigo-600 focus:ring-indigo-500">
                  <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Featured Products') }}</span>
                </label>
              </li>
              <li class="mb-2">
                <label for="onsale" class="flex items-center dark:text-gray-300">
                  <input id ="onsale" wire:model.live="onsale" value="1" type="checkbox"
                    class="w-4 h-4 mr-3 rounded border-gray-400 text-indigo-600 focus:ring-indigo-500">
                  <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('On Sale') }}</span>
                </label>
              </li>
            </ul>
            
          </div>

          <div class="p-4 mb-5 bg-white border border-gray-200 dark:bg-gray-900 dark:border-gray-900">
            <h2 class="text-md font-semibold text-white dark:text-gray-400 bg-[#003953] rounded p-2 mb-2">{{ __('Price') }}</h2>
        
            <!-- Display selected price range -->
            <div>
      
              <!-- Price range slider -->
              <input type="range" wire:model.live="price_range"
                  class="w-full h-1 mb-4 bg-gray-200 hover:bg-indigo-500 rounded appearance-none cursor-pointer custom-range-slider"
                  max="2000" value="{{ (int) $price_range }}" step="50">
          
              <!-- Manual Input for Min/Max Price -->
              <div class="flex justify-between items-center mt-4">
                <!-- Min price input -->
                <div class="flex flex-col">
                  <label for="min_price" class="text-sm font-normal">{{ __('Min Price') }}</label>
                  <input type="number" id="min_price" wire:model.live="min_price"
                      class="block w-24 px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-0 focus:ring-indigo-500"
                      min="0" max="{{ (int) $price_range }}">
                </div>
          
                <!-- Max price input -->
                <div class="flex flex-col">
                  <label for="max_price" class="text-sm font-normal">{{ __('Max Price') }}</label>
                  <input type="number" id="max_price" wire:model.live="price_range"
                      class="block w-24 px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-0 focus:ring-indigo-500"
                      min="0" max="2000">
                </div>
              </div>
            </div>

          </div>               

        </div>

        <div class="w-full px-3 lg:w-3/4">
          <div class="mb-4">
            <div class="items-center justify-between hidden p-3 md:flex bg-[#003953] rounded dark:bg-gray-900">
              <div class="flex w-full justify-start space-x-2">

                <div class="flex items-center font-medium text-white dark:text-gray-400 mr-4">
                  {{ __('Sort as') }}
                </div>

                <select wire:model.live="sort"
                    class="block border border-gray-300 py-0 w-50 text-md cursor-pointer text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-900">
                    <option value="az">{{ __('Sort A to Z') }}</option>
                    <option value="za">{{ __('Sort Z to A') }}</option>
                    <option value="latest">{{ __('Newest to Oldest') }}</option>
                    <option value="oldest">{{ __('Oldest to Newest') }}</option>
                    <option value="price_low_to_high">{{ __('Price: Low to High') }}</option>
                    <option value="price_high_to_low">{{ __('Price: High to Low') }}</option>
                </select>

              </div>
              <div class="flex w-full justify-end space-x-2">
                <div class="flex items-center">
                  <!-- "View as" Label -->
                  <div class="flex items-center font-medium text-white dark:text-gray-400 mr-4">
                    {{ __('View as') }}
                  </div>
                
                  <!-- Button with icons -->
                  <button type="button" class="flex items-center space-x-3 ">
                
                    <!-- Grid Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                    </svg>
                
                    <!-- List Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                  </button>
                </div>

              </div>             
              
            </div>
          </div>
        
          <div class="flex flex-wrap items-center ">
            @forelse ($products as $product)
            <div class="w-full px-3 mb-6 sm:w-1/2 md:w-1/4" wire:key = "{{ $product->id }}">
              <livewire:components.product-card :product="$product" :key="$product->id" />
            </div>
            @empty
                <h3>{{ __('No Products Found') }}</h3>
            @endforelse
          </div>

          <!-- pagination start -->
          <div class="flex justify-end mt-6">
            {{ $products->links() }}
          </div>
          <!-- pagination end -->

        </div>

      </div>

    </div>
  </section>

</div>