<?php

namespace App\Livewire\Partials;

use App\Helpers\CartManagement;
use App\Livewire\Actions\Logout;
use Datlechin\FilamentMenuBuilder\Models\Menu;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Navbar extends Component
{
    public $menu;
    public $total_count = 0;

    public function mount()
    {
        $this->menu = Menu::location('header');
        $this->total_count = count(CartManagement::getCartItemsFromCookie());
    }

    #[On('update-cart-count')]
    public function updateCartCount($total_count)
    {
        $this->total_count = $total_count;
    }

    public bool $responsiveMenu = false;

    public function toggleDrawer(): void
    {
        $this->responsiveMenu = !$this->responsiveMenu;
    }

    public function render(): View
    {
        $menu = Menu::with('menuItems')->first();

        return view('livewire.partials.navbar', compact('menu'));
    }

    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}
