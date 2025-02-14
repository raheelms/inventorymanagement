<?php

namespace App\Livewire\Components;

use App\Models\Article as ArticleModel;
use Illuminate\View\View;
use Livewire\Component;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ArticleGrid extends Component
{
    public Collection $articles;
    public int $limit;
    public $category;
    public $sort_by;
    public bool $show_load_more = false;
    protected $listeners = ['statusUpdated' => 'loadArticles'];  // Listen for the statusUpdated event

    public function mount(): void
    {
        $this->loadMore();
    }

    // Method to reload articles when statusUpdated event is emitted
    public function loadArticles()
    {
        $this->loadMore();  // Reload the articles when the event is triggered
    }

    public function loadMore(): void
    {   
        $offset = 0;
        if (isset($this->articles)) {
            $offset = $this->articles->count();
        }

        $newArticles = $this->sort_by === 'popular'
            ? $this->getArticlesByViews($offset)
            : $this->getArticlesBySortOrder($offset);

        if (isset($this->articles)) {
            $this->articles = $this->articles->merge($newArticles);
        } else {
            $this->articles = $newArticles;
        }

        $this->show_load_more = $newArticles->count() >= $this->limit;
    }

    private function getBaseQuery(): Builder
    {
        return ArticleModel::with('categories')
            ->isPublished()
            ->whereHas('categories', function ($query) {
                $query->whereIn('categories.id', (array) $this->category);
            });
    }

    private function getArticlesByViews(int $offset = 0): Collection
    {
        return $this->getBaseQuery()
        ->orderByViews()
        ->skip($offset)
        ->limit($this->limit)
        ->get();
    }

    private function getArticlesBySortOrder(int $offset = 0): Collection
    {    
        $validColumns = ['created_at', 'updated_at'];
        $sortBy = in_array($this->sort_by, $validColumns) ? $this->sort_by : 'created_at';

            return $this->getBaseQuery()
                ->orderBy($sortBy, 'desc')
                ->skip($offset)
                ->limit($this->limit)
                ->get();
    }    

    public function render(): View
    {
        return view('livewire.components.article-grid');
    }
}
