
<div class="w-full max-w-[85rem] py-6 px-4 sm:px-6 lg:px-8 mx-auto">
    <section class="py-6 border border-gray-300 bg-gray-50 dark:bg-gray-800 rounded-lg">
        <div class="max-w-7xl mx-auto p-4 lg:py-6 md:px-6">
            
        <!-- Breadcrumb Section -->
        <div class="p-4 border border-gray-200 mb-12 grid grid-cols-1 gap-2">
            <nav class="flex text-sm font-normal text-gray-900 dark:text-gray-400 dark:bg-gray-600 space-x-2">
                <a wire:navigate href="/" class="hover:text-indigo-500">Home</a>
                <span>&gt;</span>
                <a href="/category" class="hover:text-indigo-500">Category</a>
                <span>&gt;</span>
                <a href="/product" class="hover:text-indigo-500">Product Name</a>
            </nav>
        </div>        
                    

            <!-- Product Display Container -->
            <div class="flex flex-wrap md:flex-nowrap border border-gray-300">
                <!-- 1/2 Column (Image) -->
                <div class="w-full mb-4 md:w-1/2 md:mb-0 border border-gray-300" x-data="{ 
                    mainImage: '{{ url('storage', $product->images[0]) }}', 
                    start: 0, 
                    images: {{ json_encode($product->images) }},
                    visibleCount: 5,
                }">
                    <div class="sticky top-0 z-50 overflow-hidden">
                        <div class="grid gap-4">
                            <!-- Main Image -->
                            <div class="flex justify-center mb-4">
                                <img x-bind:src="mainImage" alt="{{ $product->name }}" 
                                     class="object-contain w-[400px] h-[400px] border border-gray-300 transition-all duration-300 hover:border-indigo-600 rounded-2xl">
                            </div>
                    
                            <!-- Thumbnail Images Slider -->
                            <div class="relative flex items-center justify-center p-2 rounded-lg w-[500px] mx-auto">
                                <!-- Left Button -->
                                <template x-if="images.length > 1">
                                    <button 
                                        x-on:click="start = (start - 1 + images.length) % images.length; mainImage = '{{ url('storage') }}/' + images[start];"
                                        class="absolute left-[-10px] p-2 text-white bg-indigo-600 rounded-full hover:bg-indigo-500 z-10">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                </template>
                    
                                <!-- Thumbnails Container -->
                                <div class="flex overflow-x-auto space-x-1 mx-2">
                                    <template x-for="(image, index) in [...images.slice(start), ...images.slice(0, start)].slice(0, visibleCount)" :key="index">
                                        <div x-on:click="mainImage = '{{ url('storage') }}/' + image"
                                             class="w-20 h-20 overflow-hidden cursor-pointer rounded-lg aspect-square 
                                                    hover:border-2 hover:border-indigo-600"
                                             :class="{ 'border-indigo-600': mainImage === '{{ url('storage') }}/' + image }">
                                            <img :src="'{{ url('storage') }}/' + image" alt="{{ $product->name }}"
                                                 class="object-cover w-full h-full border border-gray-300 rounded-lg">
                                        </div>
                                    </template>
                                </div>
                    
                                <!-- Right Button -->
                                <template x-if="images.length > 1">
                                    <button 
                                        x-on:click="start = (start + 1) % images.length; mainImage = '{{ url('storage') }}/' + images[start];"
                                        class="absolute right-[-10px] p-2 text-white bg-indigo-600 rounded-full hover:bg-indigo-500 z-10">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            
                <!-- 1/2 Column (Product Info) -->
                <div class="w-full md:w-1/2 md:pl-10 md:py-6 mt-6 md:mt-0 border border-gray-300">
                    <h2 class="text-sm title-font text-gray-500 tracking-widest">BRAND NAME</h2>
                    <h1 class="text-gray-900 text-3xl title-font font-medium mb-1">The Catcher in the Rye</h1>
    
                        <div class="flex mb-4">
                          <span class="flex items-center">
                            <svg fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 text-indigo-500" viewBox="0 0 24 24">
                              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                            </svg>
                            <svg fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 text-indigo-500" viewBox="0 0 24 24">
                              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                            </svg>
                            <svg fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 text-indigo-500" viewBox="0 0 24 24">
                              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                            </svg>
                            <svg fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 text-indigo-500" viewBox="0 0 24 24">
                              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                            </svg>
                            <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 text-indigo-500" viewBox="0 0 24 24">
                              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                            </svg>
                            <span class="text-gray-600 ml-3">4 Reviews</span>
                          </span>
                          <span class="flex ml-3 pl-3 py-2 border-l-2 border-gray-200 space-x-2s">
                            <a class="text-gray-500">
                              <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                                <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path>
                              </svg>
                            </a>
                            <a class="text-gray-500">
                              <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                                <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path>
                              </svg>
                            </a>
                            <a class="text-gray-500">
                              <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                                <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"></path>
                              </svg>
                            </a>
                          </span>
                        </div>

                    <!-- Price Section -->
                    <div class="mb-2">
                        <h6 class="font-semibold text-md text-gray-900 dark:text-white">
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
                        </h6>
                    </div>

                    <!-- Description Section -->
                    <div>
                        <p class="font-medium text-sm leading-6 text-gray-500 dark:text-white mb-1">
                            {{ Str::limit(tiptap_converter()->asText($product->description), 100) }}
                        </p>
                    </div>
    
                    <!-- Color and Size Options -->
                    <div class="flex mt-6 items-center pb-5 border-b-2 border-gray-100 mb-5">
                        <div class="flex">
                            <span class="mr-3">Color</span>
                            <button class="border-2 border-gray-300 rounded-full w-6 h-6 focus:outline-none"></button>
                            <button class="border-2 border-gray-300 ml-1 bg-gray-700 rounded-full w-6 h-6 focus:outline-none"></button>
                            <button class="border-2 border-gray-300 ml-1 bg-indigo-500 rounded-full w-6 h-6 focus:outline-none"></button>
                        </div>
                        <div class="flex ml-6 items-center">
                            <span class="mr-3">Size</span>
                            <div class="relative">
                                <select class="rounded border border-gray-300 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                    <option>SM</option>
                                    <option>M</option>
                                    <option>L</option>
                                    <option>XL</option>
                                </select>
                            </div>
                        </div>
                    </div>
    
                    <!-- Quantity Input Field and Add to Cart Button -->
                    <div class="flex items-center space-x-2 mb-4">
                        <div x-data="{ quantity: 1 }" class="flex items-center">
                            <button wire:click="decreaseQty"
                                class="w-10 text-gray-500 px-3 py-1 focus:outline-none border border-gray-300">
                                -
                            </button>
                            <input wire:model="quantity" type="number" readonly min="1"
                                   class="w-12 text-center px-2 py-1 border-gray-300">
                            <button wire:click="increaseQty"
                                class="w-10 text-gray-500 px-3 py-1 focus:outline-none border border-gray-300">
                                +
                            </button>
                        </div>
                        <button wire:click="addToCart({{ $product->id }})" class="w-32 text-white bg-gray-900 hover:bg-gray-800 py-2 px-6 rounded-lg focus:outline-none focus:ring-2">
                            <span wire:loading.remove wire:target="addToCart({{ $product->id }})"> Add to cart </span>
                            <span wire:loading wire:target="addToCart({{ $product->id }})" role='status' aria-label='loading'>
                                <svg class='w-4 h-4 stroke-white animate-spin' viewBox='0 0 24 24'
                                    fill='none' xmlns='http://www.w3.org/2000/svg'>
                                    <g clip-path='url(#clip0_9023_61563)'>
                                        <path
                                            d='M14.6437 2.05426C11.9803 1.2966 9.01686 1.64245 6.50315 3.25548C1.85499 6.23817 0.504864 12.4242 3.48756 17.0724C6.47025 21.7205 12.6563 23.0706 17.3044 20.088C20.4971 18.0393 22.1338 14.4793 21.8792 10.9444'
                                            stroke='stroke-current' stroke-width='1.4' stroke-linecap='round'
                                            class='my-path'></path>
                                    </g>
                                    <defs>
                                        <clipPath id='clip0_9023_61563'>
                                            <rect width='24' height='24' fill='white'></rect>
                                        </clipPath>
                                    </defs>
                                </svg>
                                <span class='sr-only'>Loading...</span>
                            </span>
                        </button>

                        <!-- Wishlist Button -->
                        <button class="rounded-full w-10 h-10 bg-gray-200 p-0 border-0 inline-flex items-center justify-center text-gray-500 ml-4">
                            <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"></path>
                            </svg>
                        </button>
                    </div>

                </div>
            </div>           
        </div>
    </section>
</div>
