<?php

namespace App\Livewire\Pages;

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Article as ArticleModel;
use Datlechin\FilamentMenuBuilder\Models\Menu;

class ArticlePage extends Component
{
    public ArticleModel $article;

    public function mount(): void
    {
        views($this->article)->record();
    }

    #[Layout('components.layouts.app')]
    public function render(): View
    {
        return view('livewire.pages.article-page', [
            'articles' => ArticlePage::all()
        ]);
    }
}