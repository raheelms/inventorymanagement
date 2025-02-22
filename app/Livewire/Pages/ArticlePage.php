<?php

namespace App\Livewire\Pages;

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Article as ArticleModel;

class ArticlePage extends Component
{
    public ArticleModel $article;

    public function mount(ArticleModel $article): void
    {
        $this->article = $article;
        views($this->article)->record();
    }

    #[Layout('components.layouts.app', ['seoData' => 'article'])]
    public function render(): View
    {
        return view('livewire.pages.article-page', [
            'articles' => ArticleModel::where('id', '!=', $this->article->id)
                ->limit(5)
                ->get()
        ]);
    }
}