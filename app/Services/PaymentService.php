<?php

namespace App\Services;

use App\Helpers\CartManagement;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\PaymentIntent;
use Stripe\Customer as StripeCustomer;
use Stripe\Stripe;
use Stripe\Exception\ApiErrorException;

class PaymentService
{
    protected $stripeSecretKey;
    protected $paymentMethods = ['card', 'ideal', 'bancontact', 'klarna'];
    
    public function __construct()
    {
        $this->stripeSecretKey = config('services.stripe.secret');
        Stripe::setApiKey($this->stripeSecretKey);
    }

    /**
     * Get or create a new payment intent
     */
    public function initializeOrRetrievePaymentIntent(array $cartItems, ?int $customerId)
    {
        try {
            Log::info('Payment intent initialization requested', [
                'customer_id' => $customerId,
                'cart_items_count' => count($cartItems)
            ]);

            // Calculate cart total
            $cartTotal = CartManagement::calculateGrandTotal($cartItems);
            if ($cartTotal <= 0) {
                throw new \Exception('Invalid cart total');
            }

            // Check for existing payment intent
            $existingIntentId = session('checkout_payment_intent_id');
            if ($existingIntentId) {
                try {
                    $existingIntent = PaymentIntent::retrieve($existingIntentId);
                    
                    // Check if intent is still valid
                    if ($this->isValidPaymentIntent($existingIntent, $cartTotal)) {
                        Log::info('Using existing payment intent', [
                            'intent_id' => $existingIntent->id
                        ]);
                        return $this->formatPaymentResponse($existingIntent);
                    }

                    // Cancel invalid intent
                    if ($existingIntent->status !== 'succeeded' && $existingIntent->status !== 'canceled') {
                        $existingIntent->cancel();
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to retrieve existing payment intent', [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Create new payment intent
            $intent = $this->createPaymentIntent($cartItems, $cartTotal, $customerId);
            
            // Store in session
            session(['checkout_payment_intent_id' => $intent->id]);
            
            return $this->formatPaymentResponse($intent);

        } catch (\Exception $e) {
            Log::error('Payment intent initialization failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create a new Stripe payment intent
     */
    protected function createPaymentIntent(array $cartItems, float $amount, ?int $customerId): PaymentIntent
    {
        $paymentIntentData = [
            'amount' => (int)($amount * 100), // Convert to cents
            'currency' => config('app.currency', 'eur'),
            'payment_method_types' => $this->paymentMethods,
            'metadata' => [
                'cart_hash' => $this->generateCartHash($cartItems),
                'customer_id' => $customerId ?? 'guest'
            ],
            'capture_method' => 'automatic',
        ];

        // Add customer if exists
        if ($customerId) {
            $stripeCustomerId = $this->getCustomerStripeId($customerId);
            if ($stripeCustomerId) {
                $paymentIntentData['customer'] = $stripeCustomerId;
            }
        }

        $intent = PaymentIntent::create($paymentIntentData);
        
        Log::info('Payment intent created', [
            'intent_id' => $intent->id,
            'amount' => $amount
        ]);

        return $intent;
    }

    /**
     * Process payment after confirmation
     */
    public function processPayment(string $paymentIntentId, array $orderData)
    {
        Log::channel('payment')->info('Payment Processing Initiated', [
            'payment_intent_id' => $paymentIntentId,
            'customer_id' => auth('customer')->id() ?? 'guest',
            'cart_items' => count($orderData['cart_items'] ?? []),
            'payment_method' => $orderData['payment_method'] ?? 'unknown',
            'cart_total' => CartManagement::calculateGrandTotal($orderData['cart_items'])
        ]);

        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            
            Log::channel('payment')->debug('Payment Intent Retrieved', [
                'intent_id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount / 100,
                'currency' => $paymentIntent->currency
            ]);
            
            if (!$paymentIntent) {
                Log::channel('payment')->error('Payment Intent Not Found', [
                    'payment_intent_id' => $paymentIntentId
                ]);
                throw new \Exception('Payment intent not found');
            }

            // Validate payment status
            if ($paymentIntent->status !== 'succeeded') {
                Log::channel('payment')->error('Invalid Payment Status', [
                    'status' => $paymentIntent->status,
                    'payment_intent_id' => $paymentIntentId,
                    'last_payment_error' => $paymentIntent->last_payment_error ?? null
                ]);
                throw new \Exception("Payment incomplete. Status: {$paymentIntent->status}");
            }

            // Verify amount matches
            $expectedAmount = (int)(CartManagement::calculateGrandTotal($orderData['cart_items']) * 100);
            
            Log::channel('payment')->info('Amount Verification', [
                'expected_amount' => $expectedAmount / 100,
                'actual_amount' => $paymentIntent->amount / 100,
                'payment_intent_id' => $paymentIntentId
            ]);

            if ($paymentIntent->amount !== $expectedAmount) {
                Log::channel('payment')->error('Amount Mismatch', [
                    'expected' => $expectedAmount / 100,
                    'actual' => $paymentIntent->amount / 100,
                    'payment_intent_id' => $paymentIntentId
                ]);
                throw new \Exception('Payment amount mismatch');
            }

            // Verify cart hasn't changed
            $currentHash = $this->generateCartHash($orderData['cart_items']);
            
            Log::channel('payment')->info('Cart Hash Verification', [
                'stored_hash' => $paymentIntent->metadata['cart_hash'],
                'current_hash' => $currentHash,
                'payment_intent_id' => $paymentIntentId
            ]);

            if ($paymentIntent->metadata['cart_hash'] !== $currentHash) {
                Log::channel('payment')->error('Cart Hash Mismatch', [
                    'stored_hash' => $paymentIntent->metadata['cart_hash'],
                    'current_hash' => $currentHash,
                    'payment_intent_id' => $paymentIntentId
                ]);
                throw new \Exception('Cart contents have changed');
            }

            Log::channel('payment')->info('Payment Processing Successful', [
                'payment_intent_id' => $paymentIntentId,
                'amount' => $paymentIntent->amount / 100,
                'customer_id' => auth('customer')->id() ?? 'guest'
            ]);

            return [
                'success' => true,
                'payment_intent' => $paymentIntent
            ];

        } catch (\Exception $e) {
            Log::channel('payment')->critical('Payment Processing Failed', [
                'error_message' => $e->getMessage(),
                'payment_intent_id' => $paymentIntentId,
                'exception_type' => get_class($e),
                'trace' => $e->getTraceAsString(),
                'customer_details' => [
                    'id' => auth('customer')->id() ?? 'guest',
                    'email' => auth('customer')->user()->email ?? 'unknown'
                ]
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get customer's Stripe ID
     */
    protected function getCustomerStripeId(int $customerId): ?string
    {
        try {
            $customer = \App\Models\Customer::find($customerId);
            if (!$customer) {
                return null;
            }

            if ($customer->stripe_id) {
                return $customer->stripe_id;
            }

            // Create new Stripe customer
            $stripeCustomer = StripeCustomer::create([
                'email' => $customer->email,
                'name' => $customer->name,
                'phone' => $customer->phone_number,
                'metadata' => [
                    'customer_id' => $customer->id
                ]
            ]);

            // Save Stripe ID
            $customer->update(['stripe_id' => $stripeCustomer->id]);

            return $stripeCustomer->id;

        } catch (\Exception $e) {
            Log::error('Failed to get/create Stripe customer', [
                'error' => $e->getMessage(),
                'customer_id' => $customerId
            ]);
            return null;
        }
    }

    /**
     * Check if payment intent is still valid
     */
    protected function isValidPaymentIntent(PaymentIntent $intent, float $cartTotal): bool
    {
        $validStatuses = ['requires_payment_method', 'requires_confirmation', 'requires_action'];
        
        return in_array($intent->status, $validStatuses) && 
               $intent->amount === (int)($cartTotal * 100) &&
               !$intent->canceled_at;
    }

    /**
     * Format payment intent response
     */
    protected function formatPaymentResponse(PaymentIntent $intent): array
    {
        return [
            'success' => true,
            'intent_id' => $intent->id,
            'client_secret' => $intent->client_secret,
            'publishable_key' => config('services.stripe.key'),
            'amount' => $intent->amount / 100, // Add amount for validation
            'status' => $intent->status, // Add status for validation
            'payment_method_types' => $intent->payment_method_types // Add available payment methods
        ];
    }

    /**
     * Generate hash of cart contents
     */
    protected function generateCartHash(array $cartItems): string
    {
        $cartData = array_map(function ($item) {
            return [
                'id' => $item['product_id'],
                'qty' => $item['quantity'],
                'price' => $item['unit_amount']
            ];
        }, $cartItems);
        
        return md5(json_encode($cartData));
    }

    /**
     * Handle payment webhooks
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, 
                $sig_header, 
                config('services.stripe.webhook_secret')
            );

            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    $order = Order::where('payment_intent_id', $paymentIntent->id)->first();
                    if ($order) {
                        $order->update(['payment_status' => 'paid']);
                        //event(new OrderPaid($order));
                    }
                    break;

                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    $order = Order::where('payment_intent_id', $paymentIntent->id)->first();
                    if ($order) {
                        $order->update(['payment_status' => 'failed']);
                        //event(new OrderFailed($order));
                    }
                    break;

                case 'payment_intent.requires_action':
                    $paymentIntent = $event->data->object;
                    $order = Order::where('payment_intent_id', $paymentIntent->id)->first();
                    if ($order) {
                        $order->update(['payment_status' => 'unpaid']);
                    }
                    break;
            }

            return response()->json(['status' => 'success']);
            
        } catch(\Exception $e) {
            Log::error('Webhook handling failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Handle successful payment webhook
     */
    protected function handleSuccessfulPayment($paymentIntent)
    {
        Log::info('Payment succeeded webhook', [
            'payment_intent_id' => $paymentIntent->id
        ]);
        // Additional webhook handling if needed
    }

    /**
     * Handle failed payment webhook
     */
    protected function handleFailedPayment($paymentIntent)
    {
        Log::error('Payment failed webhook', [
            'payment_intent_id' => $paymentIntent->id,
            'error' => $paymentIntent->last_payment_error ?? null
        ]);
        // Additional webhook handling if needed
    }
}