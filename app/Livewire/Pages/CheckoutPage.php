<?php

namespace App\Livewire\Pages;

use App\Helpers\CartManagement;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Checkout')]
class CheckoutPage extends Component
{
    // Customer Information
    public $customer_id;
    public $name, $email, $first_name, $last_name, $phone_number, $company_name;
    
    // Address Management
    public $use_shipping_address = true;
    public $shipping_address, $shipping_street_name, $shipping_house_number, $shipping_zipcode, $shipping_city, $shipping_country;
    public $billing_address, $billing_street_name, $billing_house_number, $billing_zipcode, $billing_city, $billing_country;
    
    // Order Information
    public $notes;
    public $cart_items = [];
    public $payment_method = ['card', 'ideal', 'klarna', 'bancontact'];

    /**
     * Define validation rules for checkout form
     *
     * @return array Validation rules
     */
    protected function rules()
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'company_name' => ['nullable', 'string', 'max:255'],
            
            // Shipping address validation
            'shipping_address' => ['required', 'string', 'max:255'],
            'shipping_street_name' => ['required', 'string', 'max:255'],
            'shipping_house_number' => ['required', 'string', 'max:10'],
            'shipping_zipcode' => ['required', 'string', 'max:10'],
            'shipping_city' => ['required', 'string', 'max:255'],
            'shipping_country' => ['required', 'string', 'max:255'],
            
            // Payment validation
            'payment_method' => ['required', 'string', 'in:card,ideal,bancontact,klarna'],
            'notes' => ['nullable', 'string', 'max:500']
        ];

        // Conditional billing address validation
        if (!$this->use_shipping_address) {
            $rules = array_merge($rules, [
                'billing_address' => ['required', 'string', 'max:255'],
                'billing_street_name' => ['required', 'string', 'max:255'],
                'billing_house_number' => ['required', 'string', 'max:10'],
                'billing_zipcode' => ['required', 'string', 'max:10'],
                'billing_city' => ['required', 'string', 'max:255'],
                'billing_country' => ['required', 'string', 'max:255'],
            ]);
        }

        return $rules;
    }

    /**
     * Custom validation error messages
     *
     * @return array Custom error messages
     */
    protected function messages()
    {
        return [
            'first_name.required' => 'Please enter your first name',
            'last_name.required' => 'Please enter your last name',
            'email.required' => 'Please enter your email address',
            'email.email' => 'Please enter a valid email address',
            'shipping_street_name.required' => 'Please enter your street name',
            'shipping_house_number.required' => 'Please enter your house number',
            'shipping_zipcode.required' => 'Please enter your postal code',
            'shipping_city.required' => 'Please enter your city',
            'shipping_country.required' => 'Please select your country',
            'billing_street_name.required' => 'Please enter billing street name',
            'billing_house_number.required' => 'Please enter billing house number',
            'billing_zipcode.required' => 'Please enter billing postal code',
            'billing_city.required' => 'Please enter billing city',
            'billing_country.required' => 'Please select billing country',
        ];
    }

    /**
     * Validate checkout form fields
     *
     * @return bool Validation status
     */
    public function validateCheckout()
    {
        try {
            Log::info('Starting checkout form validation', [
                'email' => $this->email,
                'payment_method' => $this->payment_method
            ]);
    
            // Validate form data with custom rules and messages
            $validated = $this->validate(
                $this->rules(),
                $this->messages()
            );
    
            Log::info('Checkout validation passed', [
                'email' => $this->email
            ]);
    
            return $validated;
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Checkout form validation failed', [
                'errors' => $e->validator->errors()->toArray(),
                'email' => $this->email ?? 'not_provided'
            ]);
    
            // Optional: Add a flash message for global errors
            $this->addError('checkout', 'Please correct the errors in the form.');
    
            // Re-throw to allow Livewire to handle error display
            throw $e;
        }
    }

    /**
     * Initialize checkout page
     * Loads cart, customer data, and sets up payment intent
     *
     * @param PaymentService $paymentService Payment processing service
     */
    public function mount(PaymentService $paymentService)
    {
        try {
            // Retrieve and validate cart items
            $this->cart_items = CartManagement::getCartItemsFromCookie();
            
            // Check if cart is empty
            if (empty($this->cart_items)) {
                Log::warning('Attempted checkout with empty cart', [
                    'customer_id' => auth('customer')->id() ?? 'guest'
                ]);
                session()->flash('error', 'Your cart is empty');
                return redirect()->route('cart');
            }

            // Validate cart total
            $cart_total = CartManagement::calculateGrandTotal($this->cart_items);
            if ($cart_total <= 0) {
                Log::error('Invalid cart total', [
                    'total' => $cart_total,
                    'items' => $this->cart_items
                ]);
                session()->flash('error', 'Invalid cart total');
                return redirect()->route('cart');
            }
    
            // Load customer data if authenticated
            if (auth('customer')->check()) {
                $this->loadCustomerData();
            }
    
            // Initialize payment intent
            $paymentData = $paymentService->initializeOrRetrievePaymentIntent(
                $this->cart_items, 
                auth('customer')->id()
            );
    
            if (!isset($paymentData['success']) || !$paymentData['success']) {
                throw new \Exception($paymentData['error'] ?? 'Payment initialization failed');
            }
    
            // Pass Stripe configuration to JavaScript
            $this->js("
                window.stripePublishableKey = '{$paymentData['publishable_key']}';
                window.stripeClientSecret = '{$paymentData['client_secret']}';
                console.log('Stripe config initialized');
            ");
    
            Log::info('Checkout initialized successfully', [
                'customer_id' => auth('customer')->id() ?? 'guest',
                'cart_items' => count($this->cart_items)
            ]);
    
        } catch (\Exception $e) {
            Log::error('Checkout initialization failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            session()->flash('error', 'Unable to initialize checkout: ' . $e->getMessage());
            return redirect()->route('cart');
        }
    }

    /**
     * Handle successful payment processing
     *
     * @param string $paymentIntentId Stripe payment intent identifier
     * @return array Processing result
     */
   public function handlePaymentSuccess($paymentIntentId)
   {
       Log::info('Payment Success Handler Started', [
           'payment_intent_id' => $paymentIntentId,
           'customer_id' => auth('customer')->id() ?? 'guest'
       ]);

       DB::beginTransaction();
       try {
           // Validate checkout form
           $this->validate();
           
           // Process payment
           $paymentService = app(PaymentService::class);
           $paymentResult = $paymentService->processPayment($paymentIntentId, [
               'cart_items' => $this->cart_items,
               'payment_method' => $this->payment_method[0]
           ]);
       
           if (!$paymentResult['success']) {
               Log::warning('Payment Processing Failed', [
                   'payment_intent_id' => $paymentIntentId,
                   'error' => $paymentResult['message'] ?? 'Unknown error'
               ]);

               return [
                   'success' => false,
                   'redirectUrl' => route('payment.cancel')
               ];
           }
       
           // Create or update customer
           $customer = $this->createOrUpdateCustomer();
           
           // Create order
           $order = $this->createOrder($customer, $paymentIntentId);
           
           // Create order items
           $this->createOrderItems($order);

           // Commented out cart clearing as requested
           //CartManagement::clearCartItems();

           DB::commit();

           Log::info('Order Processing Completed', [
               'order_id' => $order->id,
               'customer_id' => $customer->id
           ]);
       
           return [
               'success' => true,
               'order_id' => $order->id,
               'redirectUrl' => route('payment.success', ['order' => $order->id])
           ];

       } catch (\Exception $e) {
           DB::rollBack();
           Log::error('Order Creation Failed', [
               'error' => $e->getMessage(),
               'payment_intent_id' => $paymentIntentId,
               'trace' => $e->getTraceAsString()
           ]);
       
           return [
               'success' => false,
               'redirectUrl' => route('payment.cancel')
           ];
       }
   }

    /**
     * Create order record in the database
     *
     * @param Customer $customer Customer model instance
     * @param string $paymentIntentId Stripe payment intent identifier
     * @return Order Created order model instance
     */
    protected function createOrder($customer, $paymentIntentId)
    {
        return Order::create([
            'customer_id' => $customer->id,
            'email' => $this->email,
            'payment_status' => 'paid',
            'payment_method' => $this->payment_method,
            'payment_intent_id' => $paymentIntentId,
            'currency' => config('app.currency', 'eur'),
            'grand_total' => CartManagement::calculateGrandTotal($this->cart_items),
            'shipping_amount' => 0,
            'shipping_method' => 'standard',
            'notes' => $this->notes,
            'shipping_address' => $this->formatShippingAddress(),
            'billing_address' => $this->use_shipping_address ? 
                $this->formatShippingAddress() : 
                $this->formatBillingAddress()
        ]);
    }

    /**
     * Create order items for the given order
     *
     * @param Order $order Order model instance
     */
    protected function createOrderItems($order)
    {
        foreach ($this->cart_items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_amount' => $item['unit_amount'],
                'total_amount' => $item['unit_amount'] * $item['quantity']
            ]);
        }
    }

    /**
     * Create or update customer information
     *
     * @return Customer Customer model instance
     */
    protected function createOrUpdateCustomer()
    {
        // Combine addresses
        $shipping_address = $this->shipping_street_name . ' ' . $this->shipping_house_number;
        $billing_address = $this->use_shipping_address ? 
            $shipping_address : 
            $this->billing_street_name . ' ' . $this->billing_house_number;

        $customerData = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'name' => "{$this->first_name} {$this->last_name}",
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'company_name' => $this->company_name,
            
            // Shipping address
            'shipping_address' => $shipping_address,
            'shipping_street_name' => $this->shipping_street_name,
            'shipping_house_number' => $this->shipping_house_number,
            'shipping_zipcode' => $this->shipping_zipcode,
            'shipping_city' => $this->shipping_city,
            'shipping_country' => $this->shipping_country,
            
            // Billing preference
            'use_shipping_address' => $this->use_shipping_address,
            'billing_address' => $billing_address
        ];

        // Add billing address details if different from shipping
        if (!$this->use_shipping_address) {
            $customerData = array_merge($customerData, [
                'billing_street_name' => $this->billing_street_name,
                'billing_house_number' => $this->billing_house_number,
                'billing_zipcode' => $this->billing_zipcode,
                'billing_city' => $this->billing_city,
                'billing_country' => $this->billing_country,
            ]);
        }

        // Update or create customer
        return Customer::updateOrCreate(
            ['email' => $this->email],
            $customerData
        );
    }

    /**
     * Format shipping address as JSON
     *
     * @return string JSON-encoded shipping address
     */
    protected function formatShippingAddress()
    {
        return json_encode([
            'street_name' => $this->shipping_street_name,
            'house_number' => $this->shipping_house_number,
            'zipcode' => $this->shipping_zipcode,
            'city' => $this->shipping_city,
            'country' => $this->shipping_country
        ]);
    }

    /**
     * Format billing address as JSON
     *
     * @return string JSON-encoded billing address
     */
    protected function formatBillingAddress()
    {
        return json_encode([
            'street_name' => $this->billing_street_name,
            'house_number' => $this->billing_house_number,
            'zipcode' => $this->billing_zipcode,
            'city' => $this->billing_city,
            'country' => $this->billing_country
        ]);
    }

    /**
     * Load customer data for authenticated users
     */
        protected function loadCustomerData()
    {
        $customer = auth('customer')->user();
        if (!$customer) return;

        // Load basic info
        $this->customer_id = $customer->id;
        $this->name = $customer->name;
        $this->email = $customer->email;
        $this->first_name = $customer->first_name;
        $this->last_name = $customer->last_name;
        $this->phone_number = $customer->phone_number;
        $this->company_name = $customer->company_name;

        // Load shipping address
        $this->shipping_address = $customer->shipping_address;
        $this->shipping_street_name = $customer->shipping_street_name;
        $this->shipping_house_number = $customer->shipping_house_number;
        $this->shipping_zipcode = $customer->shipping_zipcode;
        $this->shipping_city = $customer->shipping_city;
        $this->shipping_country = $customer->shipping_country;

        // Load billing address preference
        $this->use_shipping_address = $customer->use_shipping_address;
        
        // Load billing address
        $this->billing_address = $customer->billing_address;
        if (!$this->use_shipping_address) {
            $this->billing_street_name = $customer->billing_street_name;
            $this->billing_house_number = $customer->billing_house_number;
            $this->billing_zipcode = $customer->billing_zipcode;
            $this->billing_city = $customer->billing_city;
            $this->billing_country = $customer->billing_country;
        }
    }

    /**
     * Render checkout page view
     *
     * @return \Illuminate\View\View Checkout page view
     */
    public function render()
    {
        return view('livewire.pages.checkout-page', [
            'cart_items' => $this->cart_items,
            'grand_total' => CartManagement::calculateGrandTotal($this->cart_items)
        ]);
    }
}