<?php

namespace App\Livewire\Pages;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

#[Title('Success')]
class SuccessPage extends Component
{
    #[Url]
    public $order;

    public function mount()
    {
        $orderId = request()->query('order');
        Log::debug('Order ID: ' . $orderId);
        
        if (!$orderId) {
            return redirect()->route('cart');
        }

        // Find the order by ID
        $this->order = Order::where('id', $orderId)
            ->where('payment_status', 'paid')
            ->first();

        if (!$this->order) {
            return redirect()->route('cart')
                ->with('error', 'Order not found or payment incomplete.');
        }
    }

    public function render()
    {
        return view('livewire.pages.success-page', [
            'order' => $this->order
        ]);
    }
}