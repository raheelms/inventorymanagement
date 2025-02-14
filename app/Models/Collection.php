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
* Collection Model
* 
* Represents a product collection that can be hierarchically organized.
* Supports menu panel functionality and SEO capabilities.
*/
class Collection extends Model implements MenuPanelable
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
    * Get the URL for this collection in menu panel.
    */
   public function getMenuPanelUrlUsing(): callable
   {
       return fn (self $model) => route('collections.show', $model->slug);
   }   

   /**
    * Get the display name for the menu panel.
    */
   public function getMenuPanelName(): string
   {
       return 'Collections';
   }
   
   /**
    * Modify the query for menu panel items to only show visible collections.
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
       'images',
       'is_visible',
       'parent_id',         
       'description',
       'media_id',    
       'tags',
       'data'
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
       'data' => 'array',
   ];

   /**
    * Get the parent collection.
    * One-to-Many: A collection can have one parent collection.
    */
   public function parent(): BelongsTo
   {
       return $this->belongsTo(Collection::class, 'parent_id');
   }

   /**
    * Get the child collections.
    * One-to-Many: A collection can have multiple child collections.
    */
   public function child(): HasMany
   {
       return $this->hasMany(related: Collection::class, foreignKey: 'parent_id');
   }

   /**
    * Get associated products.
    * Many-to-Many: A collection can be related to multiple products.
    * @deprecated Use products() relationship instead
    */
   public function collections(): BelongsToMany
   {
       return $this->belongsToMany(related: Product::class);
   }

   /**
    * Get all products in this collection.
    * Many-to-Many: A collection can have multiple products,
    * and a product can belong to multiple collections.
    * Uses pivot table: collection_product
    */
   public function products(): BelongsToMany
   {
       return $this->belongsToMany(Product::class, 'collection_product', 'collection_id', 'product_id');
   }
   
   /**
    * Get the featured image associated with this collection.
    * One-to-Many: A collection belongs to one media item.
    */
   public function image(): BelongsTo
   {
       return $this->belongsTo(Media::class, 'media_id');
   }

   /**
    * Get the user who created/manages this collection.
    * One-to-Many: A collection belongs to one user.
    */
   public function user(): BelongsTo
   {
       return $this->belongsTo(User::class);
   }

   /**
    * Generate SEO data for this collection.
    * Overrides the base SEO implementation with collection-specific data.
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