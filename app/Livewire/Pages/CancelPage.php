<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class CancelPage extends Component
{
    public function mount()
    {
        // Get error message if any from Stripe redirect
        $error = request()->query('error');
        
        if ($error) {
            session()->flash('error', 'Payment failed: ' . $error);
        }
    }

    public function render()
    {
        return view('livewire.pages.cancel-page');
    }
}