<?php

namespace App\Livewire\Partials;

use Datlechin\FilamentMenuBuilder\Models\Menu;
use Livewire\Component;

class Footer extends Component
{
    public $menu;
    public function mount()
    {
        $this->menu = Menu::location('footer');
    }

    public function render()
    {
        return view('livewire.partials.footer', [
            'menu' => $this->menu,
        ]);
    }
}