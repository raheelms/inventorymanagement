<?php

namespace App\Models;

use App\Enums\ProductStatus;
use App\Observers\ProductObserver;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
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
* Product Model
* 
* Represents a product in the e-commerce system.
* Supports soft deletes, SEO capabilities, and view tracking.
* Includes inventory management and pricing features.
*/
#[ObservedBy([ProductObserver::class])]
class Product extends Model implements Viewable
{
   use HasFactory;
   use HasSEO;
   use SoftDeletes;
   use InteractsWithViews;

   /**
    * The attributes that are mass assignable.
    *
    * @var array<string>
    */
   protected $fillable = [
       'name',
       'slug',
       'description',
       'user_id',
       'images',
       'sku',          // Stock Keeping Unit
       'stock',        // Current stock level
       'price',        // Regular price
       'taxes',        // Tax information
       'discount_price', // Sale price
       'discount_to',    // Sale end date
       'safety_stock',   // Minimum stock level
       'data',          // Additional product data
       'published_at',   // Publication date
       'status',        // Product status (Published, Draft, etc)
       'is_visible',    // Visibility flag
       'is_featured',   // Featured product flag
       'in_stock',      // Stock availability flag
       'on_sale',       // Sale status flag
       'tags'           // Product tags
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
       'status' => ProductStatus::class,
       'tags' => 'array',
       'data' => 'array',
       'is_visible' => 'boolean',
       'is_featured' => 'boolean',
       'in_stock' => 'boolean',
       'on_sale' => 'boolean',
   ];

   /**
    * Get the user who created/manages this product.
    * One-to-Many: A product belongs to one user.
    */
   public function user(): BelongsTo
   {
       return $this->BelongsTo(User::class);
   }

   /**
    * Get the primary collection for this product.
    * One-to-Many: A product can belong to one primary collection.
    */
   public function collection(): BelongsTo
   {
       return $this->belongsTo(Collection::class);
   }

   /**
    * Get all collections this product belongs to.
    * Many-to-Many: A product can belong to multiple collections,
    * and a collection can have multiple products.
    * Uses pivot table: collection_product
    */
    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'collection_product', 'product_id', 'collection_id');
    }

    /**
     * Get the inventories associated with this product
     * Many-to-Many relationship with pivot data:
     * - quantity: number of items
     * - price: unit price at time of inventory
     * - total: calculated total (quantity * price)
     * - data: additional JSON data
     * Uses pivot table: inventory_product
     * Uses custom pivot model: InventoryProduct
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function inventories(): BelongsToMany
    {
        return $this->belongsToMany(Inventory::class, 'inventory_product')
            ->withPivot(['quantity', 'price', 'total', 'data'])
            ->using(InventoryProduct::class)
            ->withTimestamps();
    }

   /**
    * Generate SEO data for this product.
    * Overrides the base SEO implementation with product-specific data.
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
    * Scope query to only include published products.
    *
    * @param Builder $builder
    * @return Builder
    */
   public function scopeIsPublished(Builder $builder): Builder
   {
       return $builder->where('status', ProductStatus::PUBLISHED);
   }

   /**
    * Scope query to only include discontinued products.
    *
    * @param Builder $builder
    * @return Builder
    */
   public function scopeIsDiscontinued(Builder $builder): Builder
   {
       return $builder->where('status', ProductStatus::DISCONTINUED);
   }

   /**
    * Show the product details page.
    * Finds product by slug and returns the view.
    *
    * @param string $slug
    * @return \Illuminate\View\View
    */
   public function show($slug)
   {
       $product = Product::where('slug', $slug)->firstOrFail();
       return view('product.show', compact('product'));
   } 
}