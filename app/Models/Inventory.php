<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Inventory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'purchase_date',
        'total_amount',
        'taxes',
        'discount_amount',
        'grand_total',
        'currency',
        'payment_method',  
        'payment_status',
        'shipping_amount',
        'shipping_method', 
        'status',
        'notes',
        'data'
    ];

    /**
    * Get all providers associated with this inventory
    * Many-to-Many: An inventory can have multiple providers,
    * and a provider can have multiple inventories
    * Uses pivot table: inventory_provider
    * 
    * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
    */
    public function providers(): BelongsToMany 
    {
        return $this->belongsToMany(Provider::class);
    }

    /**
     * Defines the relationship between Inventory and Invoice Records.
     * 
     * This method establishes a one-to-many relationship, allowing an inventory 
     * to have multiple associated invoice records.
     * 
     * @return HasMany Eloquent relationship with invoice records
     */
    public function invoiceRecords(): HasMany
    {
        // Create a relationship with the invoiceRecord model 
        // using the default foreign key convention (inventory_id)
        return $this->hasMany(invoiceRecord::class);
    }

    /**
     * Get the inventory items for this inventory
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventory_items(): HasMany
    {
        return $this->hasMany(InventoryProduct::class);
    }

        /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    //PROPERTY IS JSON, USE THIS FUNCTION
    protected $casts = [
        'purchase_date' => 'datetime',
        'data' => 'array',
        'purchase_date' => 'date',
        'total_amount' => 'decimal:2',
        'grand_total' => 'decimal:2'
    ];

    public function generateInvoiceNumber()
    {
        // Define the prefix
        $prefix = 'INV';
        
        // Get today's date in the format YYYYMMDD
        $datePrefix = now()->format('Ym');
        
        // Find the last inventory with today's date prefix
        $lastInventory = Inventory::where('invoice_number', 'like', "{$prefix}-{$datePrefix}%")
            ->orderBy('invoice_number', 'desc')
            ->first();
        
        // If no previous invoice today, start with 001
        if (!$lastInventory) {
            return "{$prefix}-{$datePrefix}-001";
        }
        
        // Extract the current increment from the last invoice number
        preg_match('/-(\d{3})$/', $lastInventory->invoice_number, $matches);
        $lastIncrement = $matches[1] ?? '000';
        
        // Increment the number
        $newIncrement = str_pad((int)$lastIncrement + 1, 3, '0', STR_PAD_LEFT);
        
        // Return the new invoice number
        return "{$prefix}-{$datePrefix}-{$newIncrement}";
    }

    public function getGrandTotalAttribute()
    {
        return ($this->total_amount ?? 0) + 
               ($this->taxes ?? 0) + 
               ($this->shipping_amount ?? 0) - 
               ($this->discount_amount ?? 0);
    }
}
