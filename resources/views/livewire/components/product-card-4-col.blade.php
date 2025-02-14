<div>
    <section class="py-6">
        <div class="bg-white border border-gray-300 rounded-2xl p-5 transition-all duration-300 hover:border-indigo-600">
            <div class="mx-auto max-w-7xl px-2 sm:px-2 lg:px-4">
                <div class="grid grid-cols-1 gap-8">
                    <a href="javascript:;" class="mx-auto sm:mr-0 group cursor-pointer lg:mx-auto bg-white transition-all duration-500 rounded-2xl shadow hover:shadow-lg">
                    
                        <div class="overflow-hidden rounded-2xl relative">
                            <!-- Wishlist Start -->
                            <div class="absolute left-5 top-6 z-10 whitespace-nowrap bg-yellow-400 px-3 py-1.5 text-xs font-medium text-center">
                                <span>{{ __('New') }}</span>
                            </div>
                            <button class="absolute right-5 top-6 z-10 rounded-full bg-white p-1.5 text-gray-900 transition hover:text-gray-900/75 hover:scale-105">
                                <span class="sr-only">{{ __('Wishlist') }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                                </svg>
                            </button>
                            <!-- Wishlist End -->
                            
                            <!-- Product Image -->
                            <img src="{{ asset('storage/' . $product->images[0]) }}" alt="{{ $product->name }}" class="w-full aspect-square object-cover">
                        </div>                        
            
                        <!-- Product Details -->
                        <div class="relative pb-8"> <!-- Add padding to avoid overlap -->
                            <!-- Product Name -->
                            <h6 class="font-normal text-md leading-8 text-black transition-all duration-500 group-hover:text-teal-700">
                                {{ $product->name }}
                            </h6>
            
                            <!-- Product Description -->
                            <p class="h-12 font-light text-sm leading-6 text-gray-500 mb-1">
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
            
                                <!-- Add to Cart Button (Right) -->
                                <div class="absolute right-0 flex items-center justify-center">
                                    <a wire:navigate href="{{ route('product-page.show', ['slug' => $product->slug]) }}" 
                                       class="flex items-center space-x-2 text-sm font-medium rounded-full bg-gray-900 hover:bg-gray-800 dark:bg-gray-800 dark:hover:bg-gray-700 transition text-white dark:text-gray-400 hover:text-white p-2 ">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
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


