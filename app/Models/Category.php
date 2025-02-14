<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Datlechin\FilamentMenuBuilder\Contracts\MenuPanelable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;

/**
* Category Model
* 
* Represents a category that can be assigned to articles and products.
* Supports hierarchical structure (parent/child categories) and SEO capabilities.
*/
class Category extends Model
{
   use HasFactory;
   use HasSEO;

   /**
    * Get the column name to be used as title in menu panel.
    */
   public function getMenuPanelTitleColumn(): string
   {
       return 'name';
   }

   /**
    * Get the URL for this category in menu panel.
    */
   public function getMenuPanelUrlUsing(): callable
   {
       return fn (self $model) => route('category.show', $model->slug);
   }   

   /**
    * Get the display name for the menu panel.
    */
   public function getMenuPanelName(): string
   {
       return 'Categories';
   }
   
   /**
    * Modify the query for menu panel items to only show visible categories.
    */
   public function getMenuPanelModifyQueryUsing(): callable
   {
       return fn ($query) => $query->where('is_visible', true);
   }

   /**
    * The attributes that are mass assignable.
    *
    * @var array<string>
    */
   protected $fillable = [
       'name', 
       'slug',
       'text_color',
       'bg_color',
       'is_tag',
       'images',
       'media_id',    
       'is_visible',
       'user_id',
       'parent_id',         
       'description', 
       'tags'
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
       'tags' => 'array',
   ];

   /**
    * Get the parent category.
    * One-to-Many: A category can have one parent category.
    */
   public function parent(): BelongsTo    
   {
       return $this->belongsTo(related: Category::class, foreignKey: 'parent_id');
   }

   /**
    * Get the child categories.
    * One-to-Many: A category can have multiple child categories.
    */
   public function child(): HasMany
   {
       return $this->hasMany(related: Category::class, foreignKey: 'parent_id');
   }

   /**
    * Get associated categories.
    * Many-to-Many: A category can be related to multiple articles.
    * @deprecated Use articles() relationship instead
    */
   public function categories(): BelongsToMany
   {
       return $this->belongsToMany(related: Article::class);
   }

   /**
    * Get all articles in this category.
    * Many-to-Many: A category can have multiple articles,
    * and an article can have multiple categories.
    * Uses pivot table: article_category
    */
   public function articles()
   {
       return $this->belongsToMany(Article::class, 'article_category', 'category_id', 'article_id');
   }

   /**
    * Get the featured image associated with this category.
    * One-to-Many: A category belongs to one media item.
    */
   public function image(): BelongsTo
   {
       return $this->belongsTo(Media::class, 'media_id');
   }

   /**
    * Get the user who created/manages this category.
    * One-to-Many: A category belongs to one user.
    */
   public function user(): BelongsTo
   {
       return $this->belongsTo(User::class);
   }

   /**
    * TODO: Implement product relationships
    * Uncomment and modify these relations when ProductResource is created
    *
   public function products(): BelongsToMany
   {
       return $this->belongsToMany(Product::class);
   }
   */

   /**
    * Generate SEO data for this category.
    * Overrides the base SEO implementation with category-specific data.
    */
   public function getDynamicSEOData(): SEOData
   {
       return new SEOData(
           title: $this->title,
           description: Str::limit(tiptap_converter()->asText($this->content, 160)),
           image: $this->image?->path,
       );
   }
}