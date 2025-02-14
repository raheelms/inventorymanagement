<?php

namespace App\Livewire\Pages;

use App\Enums\ArticleStatus;
use App\Models\Article as ArticleModel;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Z3d0X\FilamentFabricator\Models\Page as PageModel;

class Page extends Component
{
    public PageModel $page;
    public $articles;
        
    public function mount(): void
    {
        $this->articles = ArticleModel::isPublished()->limit(12)->get();
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.pages.page');
    }
}
