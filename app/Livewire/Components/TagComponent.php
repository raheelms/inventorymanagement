<?php

namespace App\Livewire\Components;

use App\Models\Category;
use Livewire\Component;

class TagComponent extends Component
{
    public Category $category;

    public function mount(Category $category): void
    {
        $this->category = $category;
    }

    public function render()
    {
        return view('livewire.components.tag-component');
    }
}
