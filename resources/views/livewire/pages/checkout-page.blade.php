<div>
    <div class="w-full max-w-[85rem] min-h-[75rem] py-6 px-4 sm:px-6 lg:px-8 mx-auto">
        <div class="py-6 border border-gray-300 bg-white dark:bg-gray-800 rounded-md">
            <!-- Keeping your existing breadcrumb section -->
            <div class="max-w-7xl mx-auto px-4 lg:py-2 md:px-6">
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

            <div class="flex flex-col md:flex-row gap-8">
                <!-- Left Section - Customer Details -->
                <div class="hidden lg:block lg:w-1/2">
                    <div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white overflow-x-auto pr-2 pl-12 mb-4">
                        <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-300">
                            <h2 class="font-bold text-2xl leading-10">{{ __('Checkout') }}</h2>
                        </div>

                        <!-- Your existing form with modified payment handling -->
                        <form id="payment-form">                        
                            <!-- Card -->
                            <div class="bg-white dark:bg-slate-900">
                                <!-- Shipping Address -->
                                <div class="mb-6">
                                    <h2 class="text-md font-semibold text-gray-700 dark:text-white pb-2 mb-2">
                                        {{ __('Personal Details') }}
                                    </h2>

                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                                        <!-- Full Name -->
                                        <div class="{{ $name ? '' : 'hidden' }}">
                                            <input wire:model="name"
                                                class="w-full bg-sky-100 border-none rounded-md py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none
                                                @if($errors->has('name')) border-red-500 bg-red-100 
                                                @elseif(strlen($name) > 0) ring-1 ring-blue-500 bg-blue-100
                                                @else border-sky-900 @endif"
                                                id="card-holder-name" type="text" placeholder="{{ __('Enter your full name') }}" disabled>
                                            </input>
                                            @error('name')
                                                <div class="text-red-500 text-sm"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    
                                        <!-- Email -->
                                        <div>
                                            <input wire:model="email" 
                                                class="w-full rounded-md border border-sky-900 py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none 
                                                placeholder-gray-400 focus:outline-none focus:ring-1 transition-all focus:ring-blue-500
                                                @if($errors->has('email')) border-rose-500 @elseif(filter_var($email, FILTER_VALIDATE_EMAIL)) border-green-500 bg-green-100 @endif"
                                                id="email" type="text" placeholder="{{ __('Enter your email address') }}" disabled>
                                            </input>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                        <!-- First Name -->
                                        <div>
                                            <input wire:model="first_name"
                                                class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none 
                                                placeholder-gray-400 focus:outline-none focus:ring-1 transition-all focus:ring-blue-500
                                                @if($errors->has('first_name')) border-red-500 bg-red-100 
                                                @elseif(strlen($first_name) > 0) border-green-500 bg-green-100 
                                                @else border-sky-900 @endif"
                                                id="first_name" type="text" placeholder="{{ __('Enter your first name') }}">
                                            </input>
                                            @error('first_name')
                                                <div class="text-red-500 text-sm"> {{ $message }} </div>
                                            @enderror
                                        </div>

                                        <!-- Last Name -->
                                        <div>
                                            <input wire:model="last_name"
                                                class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none 
                                                placeholder-gray-400 focus:outline-none focus:ring-1 transition-all focus:ring-blue-500
                                                @if($errors->has('last_name')) border-red-500 bg-red-100 
                                                @elseif(strlen($last_name) > 0) border-green-500 bg-green-100 
                                                @else border-sky-900 @endif"
                                                id="last_name" type="text" placeholder="{{ __('Enter your last name') }}">
                                            </input>
                                            @error('last_name')
                                                <div class="text-red-500 text-sm"> {{ $message }} </div>
                                            @enderror
                                        </div>

                                        <!-- Phone Number -->
                                        <div class="mb-1">
                                            <input wire:model="phone_number"
                                                class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none 
                                                placeholder-gray-400 focus:outline-none focus:ring-1 transition-all focus:ring-blue-500"
                                                id="phone_number" type="text" placeholder="{{ __('Enter your phone number') }}">
                                            </input>
                                        </div>

                                        <!-- Company Name -->
                                        <div class="mb-1">
                                            <input wire:model="company_name"
                                                class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none 
                                                placeholder-gray-400 focus:outline-none focus:ring-1 transition-all focus:ring-blue-500"
                                                id="company_name" type="text" placeholder="{{ __('Enter your company name') }}">
                                            </input>
                                        </div>
                                    </div>                                    

                                    <h2 class="text-md font-semibold text-gray-700 dark:text-white pb-2 mt-2 mb-2">
                                        {{ __('Shipping Address') }}
                                    </h2>

                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                        <!-- Shipping Address -->
                                        <div class="{{ $shipping_address ? '' : 'hidden' }} mb-1">
                                            <input wire:model="shipping_address"
                                                class="w-full bg-sky-100 border-none rounded-lg py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none 
                                                @if($errors->has('shipping_address')) border-red-500 bg-red-100 
                                                @elseif(strlen($shipping_address) > 0) ring-1 ring-blue-500 bg-blue-100
                                                @else border-sky-900 @endif"
                                                id="shipping_address" type="text" placeholder="{{ __('Enter your street address') }}" disabled>
                                            </input>
                                            @error('shipping_address')
                                                <div class="text-red-500 text-sm"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    
                                        <!-- Shipping Street Name -->
                                        <div class="mb-1">
                                            <input wire:model="shipping_street_name"
                                                class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none 
                                                @if($errors->has('shipping_street_name')) border-red-500 bg-red-100 
                                                @elseif(strlen($shipping_street_name) > 0) ring-1 ring-blue-500 bg-blue-100
                                                @else border-sky-900 @endif"
                                                id="shipping_street_name" type="text" placeholder="{{ __('Enter your street name') }}">
                                            </input>
                                            @error('shipping_street_name')
                                                <div class="text-red-500 text-sm"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    
                                        <!-- Shipping House Number -->
                                        <div class="mb-1">
                                            <input wire:model="shipping_house_number"
                                                class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none 
                                                @if($errors->has('shipping_house_number')) border-red-500 bg-red-100 
                                                @elseif(strlen($shipping_house_number) > 0) ring-1 ring-blue-500 bg-blue-100
                                                @else border-sky-900 @endif"
                                                id="shipping_house_number" type="text" placeholder="{{ __('Enter your house number') }}">
                                            </input>
                                            @error('shipping_house_number')
                                                <div class="text-red-500 text-sm"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    
                                        <!-- Shipping Zip Code -->
                                        <div class="mb-1">
                                            <input wire:model="shipping_zipcode"
                                                class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none 
                                                @if($errors->has('shipping_zipcode')) border-red-500 bg-red-100 
                                                @elseif(strlen($shipping_zipcode) > 0) ring-1 ring-blue-500 bg-blue-100
                                                @else border-sky-900 @endif"
                                                id="shipping_zipcode" type="text" placeholder="{{ __('Enter your postal code') }}">
                                            </input>
                                            @error('shipping_zipcode')
                                                <div class="text-red-500 text-sm"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    
                                        <!-- Shipping City -->
                                        <div class="mb-1">
                                            <input wire:model="shipping_city"
                                                class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none 
                                                @if($errors->has('shipping_city')) border-red-500 bg-red-100 
                                                @elseif(strlen($shipping_city) > 0) ring-1 ring-blue-500 bg-blue-100
                                                @else border-sky-900 @endif"
                                                id="shipping_city" type="text" placeholder="{{ __('Enter your city') }}">
                                            </input>
                                            @error('shipping_city')
                                                <div class="text-red-500 text-sm"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    
                                        <!-- Shipping Country -->
                                        <div class="mb-4">
                                            <input wire:model="shipping_country"
                                                class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none 
                                                @if($errors->has('shipping_country')) border-red-500 bg-red-100 
                                                @elseif(strlen($shipping_country) > 0) ring-1 ring-blue-500 bg-blue-100
                                                @else border-sky-900 @endif"
                                                id="shipping_country" type="text" placeholder="{{ __('Enter your country') }}">
                                            </input>
                                            @error('shipping_country')
                                                <div class="text-red-500 text-sm"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Use Shipping Address for Billing -->
                                    <div class="flex items-center mb-4">
                                        <input wire:model="use_shipping_address"
                                            class="h-5 w-5 text-sky-900 border border-sky-900  rounded focus:ring-blue-500 dark:bg-gray-700 dark:text-white dark:border-none"
                                            id="use_shipping_address" type="checkbox">
                                        <label for="use_shipping_address" class="ml-2 text-gray-700 dark:text-white">
                                            {{ __('Use the shipping address as the billing address') }}
                                        </label>
                                    </div>       
                        
                                    <!-- Billing Address Section -->
                                    <div x-data="{ useShipping: $wire.entangle('use_shipping_address') }" x-show="!useShipping" class="mt-4">
                        
                                        <h2 class="text-md font-semibold text-gray-700 dark:text-white pb-2 mb-2">
                                            {{ __('Billing Address') }}
                                        </h2>
                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- Billing Address -->
                                            <div class="{{ $billing_address ? '' : 'hidden' }} mb-1">
                                                <input wire:model="billing_address"
                                                    class="w-full bg-sky-100 border-none rounded-lg py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none 
                                                    @if($errors->has('billing_address')) border-red-500 bg-red-100 
                                                    @elseif(strlen($billing_address) > 0) ring-1 ring-blue-500 bg-blue-100
                                                    @else border-sky-900 @endif"
                                                    id="billing_address" type="text" placeholder="{{ __('Enter your billing address') }}" disabled>
                                                </input>
                                                @error('billing_address')
                                                    <div class="text-red-500 text-sm"> {{ $message }} </div>
                                                @enderror
                                            </div>
                                        
                                            <!-- Billing Street Name -->
                                            <div class="mb-1">
                                                <input wire:model="billing_street_name"
                                                    class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none 
                                                    @if($errors->has('billing_street_name')) border-red-500 bg-red-100 
                                                    @elseif(strlen($billing_street_name) > 0) ring-1 ring-blue-500 bg-blue-100
                                                    @else border-sky-900 @endif"
                                                    id="billing_street_name" type="text" placeholder="{{ __('Enter your street name') }}">
                                                </input>
                                                @error('billing_street_name')
                                                    <div class="text-red-500 text-sm"> {{ $message }} </div>
                                                @enderror
                                            </div>
                                        
                                            <!-- Billing House Number -->
                                            <div class="mb-1">
                                                <input wire:model="billing_house_number"
                                                    class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none 
                                                    @if($errors->has('billing_house_number')) border-red-500 bg-red-100 
                                                    @elseif(strlen($billing_house_number) > 0) ring-1 ring-blue-500 bg-blue-100
                                                    @else border-sky-900 @endif"
                                                    id="billing_house_number" type="text" placeholder="{{ __('Enter your house number') }}">
                                                </input>
                                                @error('billing_house_number')
                                                    <div class="text-red-500 text-sm"> {{ $message }} </div>
                                                @enderror
                                            </div>
                                        
                                            <!-- Billing ZIP Code -->
                                            <div class="mb-1">
                                                <input wire:model="billing_zipcode"
                                                    class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none 
                                                    @if($errors->has('billing_zipcode')) border-red-500 bg-red-100 
                                                    @elseif(strlen($billing_zipcode) > 0) ring-1 ring-blue-500 bg-blue-100
                                                    @else border-sky-900 @endif"
                                                    id="billing_zipcode" type="text" placeholder="{{ __('Enter your postal code') }}">
                                                </input>
                                                @error('billing_zipcode')
                                                    <div class="text-red-500 text-sm"> {{ $message }} </div>
                                                @enderror
                                            </div>
                                        
                                            <!-- Billing City -->
                                            <div class="mb-1">
                                                <input wire:model="billing_city"
                                                    class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none 
                                                    @if($errors->has('billing_city')) border-red-500 bg-red-100 
                                                    @elseif(strlen($billing_city) > 0) ring-1 ring-blue-500 bg-blue-100
                                                    @else border-sky-900 @endif"
                                                    id="billing_city" type="text" placeholder="{{ __('Enter your city') }}">
                                                </input>
                                                @error('billing_city')
                                                    <div class="text-red-500 text-sm"> {{ $message }} </div>
                                                @enderror
                                            </div>
                                        
                                            <!-- Billing Country -->
                                            <div class="mb-4">
                                                <input wire:model="billing_country"
                                                    class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none 
                                                    @if($errors->has('billing_country')) border-red-500 bg-red-100 
                                                    @elseif(strlen($billing_country) > 0) ring-1 ring-blue-500 bg-blue-100
                                                    @else border-sky-900 @endif"
                                                    id="billing_country" type="text" placeholder="{{ __('Enter your country') }}">
                                                </input>
                                                @error('billing_country')
                                                    <div class="text-red-500 text-sm"> {{ $message }} </div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                    </div>
                        
                                    <!-- Aditional Notes -->
                                    <div class="grid grid-cols-1 mt-4">
                                        <textarea wire:model="notes"
                                            class="w-full rounded-lg border border-sky-900 py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none @error('notes') border-red-500 @enderror"
                                            id="notes" rows="4"  placeholder="{{ __('Enter your additional notes') }}"></textarea>
                                        @error('notes')
                                            <div class="text-red-500 text-sm"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>        
                                
                            </div>
                            <!-- End Card -->

                        </form>
                    </div>
                </div>

                <!-- Right Section - Order Summary -->
                <div class="md:w-1/2">
                    <div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg pl-2 pr-12">
                        <!-- Keep your existing order summary section -->
                        <h2 class="font-bold text-2xl leading-10 pb-4 border-b border-gray-300">
                            {{ __('Order Summary') }}
                        </h2>
    
                        <div class="flex items-center justify-between pb-6 mt-4">
                            <p class="font-normal text-lg leading-8">
                                {{ count($cart_items ?? []) }} {{ __('Items') }}
                            </p>
                            <p class="font-medium text-lg leading-8">{{ Number::currency($grand_total, 'EUR') }}</p>
                        </div>

                        <div class="flex justify-between mb-2">
                            <span>{{ __('Subtotal') }}</span>                             
                            <span>{{ Number::currency($grand_total, 'EUR') }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span>Taxes</span>
                            <span><span>{{ Number::currency(0, 'EUR') }}</span></span>
                        </div>
                        <div class="flex justify-between pb-4 mb-2">
                            <span>{{ __('Shipping') }}</span>
                            <span>{{ __('Free Shipping') }}</span>
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between mb-2">
                            <span class="font-semibold text-lg">{{ __('Grand Total') }}</span>
                            <span class="font-semibold text-lg">{{ Number::currency($grand_total, 'EUR') }}</span>
                        </div>

                        <!-- ... Your existing order summary content ... -->

                        <!-- Payment Section -->
                        <div class="mt-6 pt-6">
                            <h2 class="text-md font-semibold text-gray-700 dark:text-white pb-2 mt-2 mb-2">
                                {{ __('Payment Details') }}
                            </h2>

                            <div class="bg-[#212D63] rounded-lg">

                                <!-- Stripe Elements Section -->
                                <div wire:ignore class="p-4 rounded-lg bg-[#212D63]">
                                    <!-- Payment Element will be mounted here -->
                                    <div id="payment-element" class="mt-2 mb-2"></div>
                            
                                    <!-- Error messages -->
                                    <div id="payment-errors" class="text-rose-500 mt-2 mb-4 hidden"></div>
                            
                                    <!-- Payment processing message -->
                                    <div id="payment-processing" class="text-[#ABB2BF] mt-2 mb-4 hidden">
                                        {{ __('Processing your payment...') }}
                                    </div>
                                </div>
                            
                                <div class="p-4 mb-2">
                                    <button type="submit" form="payment-form" id="submit-payment" 
                                        class="w-full py-3 px-4 text-white bg-green-600 hover:bg-[#1A1B25] rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                        <div class="spinner hidden" id="spinner"></div>
                                        <span >{{ __('Complete Your Order') }}</span>
                                    </button>
                                </div>

                            </div>                       

                        </div>                        

                        <!-- Keep your existing cart summary section -->
                        <div class="bg-white mt-4 py-4 sm:py-7 dark:bg-slate-900">
                            <div class="text-md font-semibold text-gray-700 dark:text-white pb-2 mt-2 mb-2">
                                {{ __('Cart Summary') }}
                            </div>
                            <ul class="divide-y divide-gray-200 dark:divide-gray-700" role="list">
                                @forelse ($cart_items as $item)
                                    <li class="py-3 sm:py-4" wire:key = "{{ $item['product_id'] }}">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <img alt="{{ $item['name'] }}" class="w-12 h-12 rounded-full"
                                                    src="{{ url('storage', $item['image']) }}">
                                                </img>
                                            </div>
                                            <div class="flex-1 min-w-0 ms-4">
                                                <p class="text-md font-normal text-gray-900 truncate dark:text-white">
                                                    {{ $item['name'] }}
                                                </p>
                                                <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                                    Qty: {{ $item['quantity'] }}
                                                </p>
                                            </div>
                                            <div
                                                class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                                {{ Number::currency($item['total_amount'], 'EUR') }}
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                @endforelse

                            </ul>
                        
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@assets
<script src="https://js.stripe.com/v3/"></script>
@endassets

@script
<script>
    document.addEventListener('livewire:initialized', () => {
        // 1. Initialize application state and DOM element references
        const state = {
            isProcessing: false,
            stripeInstance: null,
            elements: {
                form: document.getElementById('payment-form'),
                submitButton: document.getElementById('submit-payment'),
                paymentErrors: document.getElementById('payment-errors'),
                processingMessage: document.getElementById('payment-processing'),
                paymentElement: document.getElementById('payment-element')
            }
        };

        // 2. Create UI Management Utilities
        const UIManager = {
            // 2.1 Display payment-related errors
            showError(message) {
                const { paymentErrors, processingMessage, submitButton } = state.elements;
                paymentErrors.innerHTML = `<p class="text-red-500 mb-2">${message}</p>`;
                paymentErrors.classList.remove('hidden');
                processingMessage.classList.add('hidden');
                submitButton.disabled = false;
            },

            // 2.2 Display form validation errors from Livewire
            displayLivewireErrors(errors) {
                // 2.2.1 Remove existing error messages
                document.querySelectorAll('.field-error').forEach(el => el.remove());
                
                // 2.2.2 Map error fields to Livewire model attributes
                const errorMapping = {
                    'shipping_street_name': '[wire\\:model="shipping_street_name"]',
                    'shipping_house_number': '[wire\\:model="shipping_house_number"]',
                    'shipping_zipcode': '[wire\\:model="shipping_zipcode"]',
                    'shipping_city': '[wire\\:model="shipping_city"]',
                    'shipping_country': '[wire\\:model="shipping_country"]',
                    'billing_street_name': '[wire\\:model="billing_street_name"]',
                    'billing_house_number': '[wire\\:model="billing_house_number"]',
                    'billing_zipcode': '[wire\\:model="billing_zipcode"]',
                    'billing_city': '[wire\\:model="billing_city"]',
                    'billing_country': '[wire\\:model="billing_country"]'
                };

                // 2.2.3 Inject error messages next to corresponding input fields
                Object.entries(errors).forEach(([key, messages]) => {
                    if (!messages) return;
                    const selector = errorMapping[key];
                    const inputElement = document.querySelector(selector);
                    if (inputElement) {
                        const errorContainer = document.createElement('div');
                        errorContainer.textContent = messages[0];
                        errorContainer.classList.add('text-rose-500', 'text-sm', 'field-error', 'mt-1');
                        inputElement.parentNode.insertBefore(errorContainer, inputElement.nextSibling);
                    }
                });
            },

            // 2.3 Manage processing state of payment form
            setProcessing(isProcessing) {
                state.isProcessing = isProcessing;
                state.elements.processingMessage.classList.toggle('hidden', !isProcessing);
                state.elements.submitButton.disabled = isProcessing;
            }
        };

        // 3. Create Payment Handling Utilities
        const PaymentHandler = {
            // 3.1 Initialize Stripe payment elements
            async initialize() {
                // 3.1.1 Prevent re-initialization
                if (state.stripeInstance?.paymentElement) return state.stripeInstance;

                // 3.1.2 Validate Stripe configuration
                if (!window.stripePublishableKey || !window.stripeClientSecret) {
                    UIManager.showError('Payment system configuration is missing.');
                    return null;
                }

                try {
                    // 3.1.3 Create Stripe instance and elements
                    const stripe = Stripe(window.stripePublishableKey);
                    const elements = stripe.elements({
                        clientSecret: window.stripeClientSecret,
                        appearance: { 
                            theme: 'stripe',
                            variables: {
                                colorText: '#32324D',
                                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                            }
                        }
                    });

                    // 3.1.4 Create and mount payment element
                    const paymentElement = elements.create('payment', {
                        fields: {
                            billingDetails: 'never'
                        },
                        paymentMethodConfiguration: {
                            ideal: { collectBillingAddress: false },
                            card: { collectBillingAddress: false },
                            bancontact: { collectBillingAddress: false },
                            klarna: { collectBillingAddress: false }
                        }
                    });
                    
                    await paymentElement.mount('#payment-element');
                    state.stripeInstance = { stripe, elements, paymentElement };
                    return state.stripeInstance;

                } catch (error) {
                    UIManager.showError('Failed to initialize payment system.');
                    return null;
                }
            },

            // 3.2 Setup payment element validation
            setupValidation(paymentElement) {
                paymentElement.on('change', (event) => {
                    const { paymentErrors, submitButton } = state.elements;
                    if (event.error) {
                        UIManager.showError(event.error.message);
                        submitButton.disabled = true;
                    } else {
                        paymentErrors.classList.add('hidden');
                        submitButton.disabled = !event.complete;
                    }
                });
            },

            // 3.3 Validate required form fields before submission
            validateRequiredFields() {
                const errors = {};
                const requiredFields = {
                    shipping_street_name: 'Please enter your street name',
                    shipping_house_number: 'Please enter your house number',
                    shipping_zipcode: 'Please enter your postal code',
                    shipping_city: 'Please enter your city',
                    shipping_country: 'Please select your country'
                };

                // 3.3.1 Add billing fields if not using shipping address
                if (!$wire.use_shipping_address) {
                    Object.assign(requiredFields, {
                        billing_street_name: 'Please enter billing street name',
                        billing_house_number: 'Please enter billing house number',
                        billing_zipcode: 'Please enter billing postal code',
                        billing_city: 'Please enter billing city',
                        billing_country: 'Please select billing country'
                    });
                }

                // 3.3.2 Check for empty required fields
                Object.entries(requiredFields).forEach(([field, message]) => {
                    if (!$wire[field]) errors[field] = [message];
                });

                return Object.keys(errors).length === 0 ? null : errors;
            }
        };

        // 4. Payment Submission Handler
        async function handleSubmit(e) {
            e.preventDefault();
            
            try {
                // 4.1 Validate form fields
                const validationErrors = PaymentHandler.validateRequiredFields();
                if (validationErrors) {
                    UIManager.displayLivewireErrors(validationErrors);
                    return;
                }

                // 4.2 Set processing state
                UIManager.setProcessing(true);

                // 4.3 Initialize Stripe payment
                const stripeSetup = await PaymentHandler.initialize();
                if (!stripeSetup) throw new Error('Payment initialization failed');

                // 4.4 Confirm payment and handle response
                const { error, paymentIntent } = await stripeSetup.stripe.confirmPayment({
                    elements: stripeSetup.elements,
                    confirmParams: {
                        return_url: `${window.location.origin}/payment/success`,
                        payment_method_data: {
                            billing_details: {
                                name: `${$wire.first_name} ${$wire.last_name}`.trim(),
                                email: $wire.email,
                                phone: $wire.phone_number,
                                address: {
                                    city: $wire.use_shipping_address ? $wire.shipping_city : $wire.billing_city,
                                    country: $wire.use_shipping_address ? $wire.shipping_country : $wire.billing_country,
                                    line1: $wire.use_shipping_address ? 
                                        `${$wire.shipping_street_name} ${$wire.shipping_house_number}` : 
                                        `${$wire.billing_street_name} ${$wire.billing_house_number}`,
                                    line2: $wire.use_shipping_address ? 
                                        '' : 
                                        ($wire.billing_address_additional || ''),
                                    postal_code: $wire.use_shipping_address ? $wire.shipping_zipcode : $wire.billing_zipcode,
                                    state: '' // Empty state since we don't collect it
                                }
                            }
                        }
                    }
                });

                // 4.5 Handle payment errors or success
                if (error) {
                    window.location.href = `${window.location.origin}/payment/cancel`;
                    throw error;
                }

                if (paymentIntent.status === 'succeeded') {
                    const result = await $wire.handlePaymentSuccess(paymentIntent.id);
                    if (result.redirectUrl) {
                        window.location.href = result.redirectUrl;
                        return;
                    }
                }

            } catch (error) {
                // 4.6 Display any payment errors
                UIManager.showError(error.message || 'Payment failed');
            } finally {
                // 4.7 Reset processing state
                UIManager.setProcessing(false);
            }
        }

        // 5. Form Initialization
        function initializeForm() {
            const { form } = state.elements;
            if (!form) return;

            // 5.1 Setup Stripe and attach submit handler
            PaymentHandler.initialize().then(stripeSetup => {
                if (stripeSetup) {
                    form.removeEventListener('submit', handleSubmit);
                    form.addEventListener('submit', handleSubmit);
                    PaymentHandler.setupValidation(stripeSetup.paymentElement);
                }
            });
        }

        // 6. Initialize form on page load
        document.readyState === 'loading'
            ? document.addEventListener('DOMContentLoaded', initializeForm)
            : initializeForm();
    });
</script>
@endscript