<div>
    <div class="w-full max-w-[85rem] min-h-[75rem] py-6 px-4 sm:px-6 lg:px-8 mx-auto">
        <div class="py-6 border border-gray-300 bg-gray-50 dark:bg-gray-800 rounded-lg">

            <div class="max-w-7xl mx-auto px-4 lg:py-2 md:px-6">
                <!-- Breadcrumb Section -->
                <div class="p-4 border border-gray-200 mb-2 grid grid-cols-1 gap-2">
                    <nav class="flex text-sm font-normal text-gray-900 dark:text-gray-400 dark:bg-gray-600 space-x-2">
                        <a wire:navigate href="/" class="hover:text-indigo-500">Home</a>
                        <span>&gt;</span>
                        <a href="/category" class="hover:text-indigo-500">Category</a>
                        <span>&gt;</span>
                        <a href="/product" class="hover:text-indigo-500">Product Name</a>
                    </nav>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-4">

                <!-- Big Devices - Cart Items Section -->
                <div class="hidden lg:block lg:w-2/3">
                    <div class="bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white overflow-x-auto px-8 mb-4">
                        <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-600">
                            <h2 class="font-bold text-2xl leading-10">{{ __('Shopping Cart') }}</h2>
                            <h2 class="font-bold text-xl leading-8">{{ count($cart_items) }} Items</h2>
                        </div>
                
                        <!-- Cart Items List -->
                        <div class="space-y-4">
                
                            <!-- Product, Quantity, and Total Headings -->
                            <div class="flex items-center justify-start font-semibold text-lg pb-2 border-b border-gray-600">
                                <span class="w-3/5 border">{{ __('Product Details') }}</span>
                                <span class="w-1/4 border">{{ __('Quantity') }}</span>
                                <span class="w-1/6 border">{{ __('Total') }}</span>
                                <span class="w-1/6 border">{{ __('') }}</span>
                            </div>
                
                            @forelse ($cart_items as $item)
                                <div class="flex flex-col md:flex-row items-center justify-start border-b border-gray-600 py-4" wire:key="{{ $item['product_id'] }}">
                                    <!-- Product Details -->
                                    <div class="flex items-center md:w-3/5">
                                        <img class="w-36 mr-4 rounded-lg" src="{{ url('storage', $item['image']) }}" alt="{{ $item['name'] }}">
                                        <div class="flex flex-col">
                                            <h6 class="font-semibold text-base leading-7">{{ $item['name'] }}</h6>
                                            <h6 class="font-normal text-base leading-7 text-gray-500 dark:text-gray-200">
                                                {{ $products[$item['product_id']]->collections->first()->name}}
                                            </h6>
                                            <h6 class="font-semibold text-md leading-7 text-gray-900 hover:text-indigo-600 dark:text-white">
                                                {{ Number::currency($item['unit_amount'], 'EUR') }}
                                            </h6>
                                        </div>
                                    </div>
                
                                    <!-- Quantity Section -->
                                    <div class="md:w-1/4 flex items-center justify-start mb-4 md:mb-0">
                                        <button wire:click="decreaseQty({{ $item['product_id'] }})" class="p-2 mr-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                            </svg>                                              
                                        </button>
                                        <input type="number" min="0" max="100" step="1" value="{{ $item['quantity'] }}" class="w-11 text-center bg-transparent border border-gray-300 rounded-full outline-none">
                                        <button wire:click="increaseQty({{ $item['product_id'] }})" class="p-2 ml-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>                                              
                                        </button>
                                    </div>

                                    <!-- Total Price Section -->
                                    <div class="md:w-1/6 text-lg font-medium text-gray-900 hover:text-indigo-600 dark:text-white">
                                        {{ Number::currency($item['unit_amount'] * $item['quantity'], 'EUR') }}
                                    </div>

                
                                    <!-- Remove Item Button -->
                                    <div class="md:w-1/6 mt-4 md:mt-0 flex justify-center">
                                        <button wire:click="removeItem({{ $item['product_id'] }})" wire:confirm="Are you sure you want to delete this post?" class="rounded-lg px-3 py-3 hover:bg-red-500 hover:text-white hover:border-red-700">
                                            <span wire:loading.remove wire:target="removeItem({{ $item['product_id'] }})">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            </span>
                                            <span wire:loading wire:target="removeItem({{ $item['product_id'] }})" role='status' aria-label='loading'>
                                                <svg class='w-6 h-6 stroke-indigo-600 animate-spin ' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                    <g clip-path='url(#clip0_9023_61563)'>
                                                        <path d='M14.6437 2.05426C11.9803 1.2966 9.01686 1.64245 6.50315 3.25548C1.85499 6.23817 0.504864 12.4242 3.48756 17.0724C6.47025 21.7205 12.6563 23.0706 17.3044 20.088C20.4971 18.0393 22.1338 14.4793 21.8792 10.9444' stroke='stroke-current' stroke-width='1.4' stroke-linecap='round' class='my-path'></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id='clip0_9023_61563'>
                                                            <rect width='24' height='24' fill='white'></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-3xl font-semibold">
                                    {{ __('Add some items to your cart :)') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>               

                <!-- Small Devices - Cart Items Section -->
                <div class="md:w-2/3 lg:hidden">
                    <div class="bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white overflow-x-auto px-8 mb-4">
                        <div class="flex items-center justify-between pb-4 border-b border-gray-600">
                            <h2 class="font-manrope font-bold text-2xl leading-10">{{ __('Shopping Cart') }}</h2>
                            <h2 class="font-manrope font-bold text-xl leading-8">{{ count($cart_items) }} Items</h2>
                        </div>
    
                        <!-- Cart Items List (Converted from Table to Divs) -->
                        <div class="space-y-4">
    
                            <!-- Product, Quantity and Total Headings -->
                            <div class="hidden flex items-center justify-between font-semibold text-lg pb-2 border-b border-gray-300">
                                <span class="w-2/5">{{ __('Product Details') }}</span>
                                <span class="w-1/4">{{ __('Quantity') }}</span>
                                <span class="w-1/4">{{ __('Total') }}</span>
                            </div>
    
                            @forelse ($cart_items as $item)
                                <div class="flex flex-wrap items-center justify-between border-b border-gray-300 py-4" wire:key="{{ $item['product_id'] }}">
                                    <!-- Product Details (Image, Name, Category, Unit Price) -->
                                    <div class="flex items-center space-x-4">
                                        <!-- Product Image -->
                                        <img class="w-24 h-24 rounded-lg" src="{{ url('storage', $item['image']) }}" alt="{{ $item['name'] }}">
                                        
                                        <!-- Product Info -->
                                        <div>
                                            <h6 class="font-semibold text-sm">{{ $item['name'] }}</h6>
                                            <h6 class="text-sm text-gray-500 text-gray-500 dark:text-gray-200">
                                                {{ $products[$item['product_id']]->collections->first()->name}}
                                            </h6>
                                            <h6 class="text-sm font-semibold hover:text-indigo-600">
                                                {{ Number::currency($item['unit_amount'], 'EUR') }}
                                            </h6>
                                        </div>
                                    </div>
                                
                                    <!-- Quantity, Total Price, and Remove Button -->
                                    <div class="flex items-center space-x-6 mt-4 md:mt-0">
                                        <!-- Quantity -->
                                        <div class="flex items-center space-x-2">
                                            <button wire:click="decreaseQty({{ $item['product_id'] }})" class="p-2 mr-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                                </svg>                                              
                                            </button>
                                            <input type="number" min="0" max="100" step="1" value="{{ $item['quantity'] }}" class="w-11 text-center bg-transparent border border-gray-300 rounded-full outline-none">
                                            <button wire:click="increaseQty({{ $item['product_id'] }})" class="p-2 ml-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                                </svg>                                              
                                            </button>
                                        </div>
                                
                                        <!-- Total Price -->
                                        <div class="text-md font-semibold hover:text-indigo-600">
                                            {{ Number::currency($item['unit_amount'] * $item['quantity'], 'EUR') }}
                                        </div>
                                
                                        <!-- Remove Button -->
                                        <div>
                                            <button wire:click="removeItem({{ $item['product_id'] }})" wire:confirm="{{ __('Are you sure you want to delete this post?') }}" class="rounded-lg px-2 py-2 hover:bg-red-500 hover:text-white">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>                          

                            @empty
                                <div class="text-center py-4 text-3xl font-semibold">
                                    {{ __('Add some items to your cart :)') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
    
                <!-- Order Summary Section -->
                <div class="md:w-1/3">
                    <div class="bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg px-6">
                        <h2 class="font-bold text-2xl leading-10 pb-4 border-b border-gray-600">
                            {{ __('Order Summary') }}
                        </h2>
    
                        <div class="flex items-center justify-between pb-6">
                            <p class="font-normal text-lg leading-8">{{ count($cart_items) }} {{ __('Items') }}</p>
                            <p class="font-medium text-lg leading-8">{{ Number::currency($grand_total, 'EUR') }}</p>
                        </div>
    
                        <div class="flex justify-between mb-2">
                            <span>{{ __('Subtotal') }}</span>                             
                            <span>{{ Number::currency($grand_total, 'EUR') }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span>Taxes</span>
                            <span><span>{{ Number::currency($tax_amount, 'EUR') }}</span></span>
                        </div>
                        <div class="flex justify-between pb-4 mb-2 border-b border-gray-600">
                            <span>{{ __('Shipping') }}</span>
                            <span>{{ __('Free Shipping') }}</span>
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between mb-2">
                            <span class="font-semibold text-lg">{{ __('Grand Total') }}</span>
                            <span class="font-semibold text-lg">{{ Number::currency($grand_total, 'EUR') }}</span>
                        </div>
                        @if ($cart_items)
                            {{-- <a wire:navigate href="{{ route('checkout') }}" class="w-full block bg-indigo-600 transition-all duration-500 hover:bg-indigo-700 text-center font-semibold text-white py-3 px-6 rounded-lg mt-4">
                                {{ __('Proceed to Checkout') }}
                            </a> --}}
                            
                            <a wire:navigate href="{{ route('checkout') }}" class="w-full block bg-indigo-600 transition-all duration-500 hover:bg-indigo-700 text-center font-semibold text-white py-3 px-6 rounded-lg mt-4">
                                {{ __('Proceed to Checkout') }}
                            </a>

                            <a href="{{ route('checkout') }}" 
                                onclick="console.log('Checkout button clicked', {
                                    href: this.href, 
                                    currentUser: '{{ auth('customer')->id() }}',
                                    timestamp: new Date().toISOString()
                                })" 
                                class="w-full block bg-rose-600 transition-all duration-500 hover:bg-indigo-700 text-center font-semibold text-white py-3 px-6 rounded-lg mt-4">
                                {{ __('Proceed to Checkout') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    
    

</div>