<?php

namespace App\Livewire\Pages;

use App\Models\Article as ArticleModel;
use App\Models\Category as CategoryModel;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CategoriesPage extends Component
{
    public $articles;

    public CategoryModel $category;

    public function mount(CategoryModel $category): void
    {
        $this->category = $category;  // Inject the category model
        $this->category->articles = ArticleModel::isPublished()->limit(9)->get();
    }

    #[Layout('components.layouts.app')]
    public function render(): View
    {
        return view('livewire.pages.categories-page');
    }
}
