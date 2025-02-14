<div>
    <section class="py-6">
        <div class="bg-white border border-gray-300 p-5 rounded-2xl transition-all duration-300 hover:border-indigo-600">
            <div class="mx-auto max-w-7xl">
                <div class="grid grid-cols-1 gap-8">
                    <a wire:navigate href="{{ route('product-page.show', ['slug' => $product->slug]) }}" 
                        class="mx-auto sm:mr-0 group cursor-pointer lg:mx-auto bg-white transition-all duration-500 rounded-2xl shadow hover:shadow-lg">
                    
                        <div class="overflow-hidden relative">
                            <!-- Wishlist Start -->
                            <div class="absolute left-3 top-4 z-10 whitespace-nowrap bg-yellow-400 px-3 py-1 text-xs font-medium text-center">
                                <span>{{ __('New') }}</span>
                            </div>
                            <button class="absolute right-3 top-3 z-10 rounded-full bg-white p-1 text-gray-900 transition hover:text-gray-900/75 hover:scale-105">
                                <span class="sr-only">{{ __('Wishlist') }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                  </svg>                                  
                            </button>
                            <!-- Wishlist End -->
                            
                            <!-- Product Image -->
                            <img src="{{ asset('storage/' . $product->images[0]) }}" alt="{{ $product->name }}" class="w-full aspect-square rounded-2xl object-cover">
                        </div>                        
            
                        <!-- Product Details -->
                        <div class="relative pb-8"> <!-- Add padding to avoid overlap -->
                            <!-- Product Name -->
                            <h6 class="h-6 font-normal text-md leading-5 text-black transition-all duration-500 group-hover:text-teal-700 mb-4">
                                {{ $product->name }}
                            </h6>                            
            
                            <!-- Product Description -->
                            <p class="h-12 font-light text-sm leading-5 text-gray-500 mb-2">
                                {{ Str::limit(tiptap_converter()->asText($product->description), 50) }}
                            </p>
                            
                            <!-- Price and Add to Cart Button Container -->
                            <div class="relative flex items-center justify-between w-full mt-6"> <!-- Flex container for price and button -->

                                <!-- Price Display (Left) -->
                                <div class="absolute left-0 flex items-center justify-center">
                                    <h6 class="font-semibold text-md text-gray-900 dark:text-white">

                                        <div class="mb-2">
                                            @if(isset($product->discount_price) && $product->discount_price > 0)
                                                <span class="text-red-600 dark:text-red-500">
                                                    {{ Number::currency($product->discount_price, 'EUR') }}
                                                </span>
                                                <span class="line-through text-sm ml-1 text-gray-500">
                                                    {{ Number::currency($product->price, 'EUR') }}
                                                </span>
                                            @else
                                                <span class="text-gray-900 dark:text-white">
                                                    {{ Number::currency($product->price, 'EUR') }}
                                                </span>
                                            @endif
                                        </div>                                       

                                    </h6>
                                </div>
            
                                <!-- Add to Cart Button -->
                                <div class="absolute right-0 flex items-center justify-center">
                                    <a wire:click.prevent='addToCart({{ $product->id }})' href="javascript:void(0);"
                                        class="flex items-center p-2 space-x-2 text-sm font-medium text-white dark:text-gray-400 hover:text-white rounded-full 
                                        bg-gray-900 hover:bg-gray-800 dark:bg-gray-800 dark:hover:bg-gray-700 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        <span wire:loading.remove wire:target="addToCart({{ $product->id }})" class="flex items-center pr-2 text-white dark:text-gray-400 hover:text-white">Add</span>
                                     
                                        <span wire:loading wire:target="addToCart({{ $product->id }})" role='status' aria-label='loading'>
                                            <svg class='w-6 h-6 stroke-indigo-600 animate-spin' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                <g clip-path='url(#clip0_9023_61563)'>
                                                    <path d='M14.6437 2.05426C11.9803 1.2966 9.01686 1.64245 6.50315 3.25548C1.85499 6.23817 0.504864 12.4242 3.48756 17.0724C6.47025 21.7205 12.6563 23.0706 17.3044 20.088C20.4971 18.0393 22.1338 14.4793 21.8792 10.9444' stroke='stroke-current' stroke-width='1.4' stroke-linecap='round' class='my-path'></path>
                                                </g>
                                                <defs>
                                                    <clipPath id='clip0_9023_61563'>
                                                        <rect width='24' height='24' fill='white'></rect>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                            <span class='sr-only'>Loading...</span>
                                        </span>
                                    </a>
                                     
                                </div>
                            </div>
                        </div>
                        
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>


