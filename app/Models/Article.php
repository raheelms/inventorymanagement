<?php

namespace App\Models;

use App\Enums\ArticleStatus;
use App\Observers\ArticleObserver;
use Awcodes\Curator\Models\Media;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Datlechin\FilamentMenuBuilder\Models\Menu;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;

/**
 * Article Model
 * 
 * Represents a blog article or content piece in the system.
 * Uses soft deletes, SEO capabilities, and view tracking.
 */
#[ObservedBy([ArticleObserver::class])]
class Article extends Model implements Viewable
{
    use HasFactory;
    use SoftDeletes;
    use HasSEO;
    use InteractsWithViews;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'title', 
        'slug', 
        'color',
        'categories',
        'description',
        'images',
        'media_id',
        'user_id',       
        'tags',
        'data',
        'is_visible',
        'published'
    ];

    /**
    * Default attribute values for the model.
    * Sets a default image when no images are provided.
    * 
    * @var array
    */
    protected $attributes = [
        'images' => '["/images/default_image.png"]'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'description' => 'array',
        'images' => 'array',
        'media_id' => 'array',
        'status' => ArticleStatus::class,
        'tags' => 'array'
    ];

    /**
     * Get the featured image associated with this article.
     * One-to-Many: An article belongs to one media item.
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    /**
     * Get the user who authored this article.
     * One-to-Many: An article belongs to one user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');       
    }

    /**
     * Get the primary category of this article.
     * One-to-Many: An article belongs to one primary category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all categories associated with this article.
     * Many-to-Many: An article can have multiple categories,
     * and a category can have multiple articles.
     * Uses pivot table: article_category
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'article_category', 'article_id', 'category_id');
    }

    /**
     * Generate SEO data for this article.
     * Overrides the base SEO implementation with article-specific data.
     */
    public function getDynamicSEOData(): SEOData
    {
        return new SEOData(
            title: $this->title,
            description: Str::limit(tiptap_converter()->asText($this->content, 160)),
            image: $this->image?->path,
        );
    }

    /**
     * Scope query to only include published articles.
     * 
     * @param Builder $builder
     * @return Builder
     */
    public function scopeIsPublished(Builder $builder): Builder
    {
        return $builder->where('status', ArticleStatus::PUBLISHED);
    }
}