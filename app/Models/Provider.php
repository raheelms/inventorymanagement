<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;

/**
* Provider Model
* 
* Represents a provider/supplier in the system.
* Handles provider details including personal info, company info,
* and shipping address details. Supports notifications.
*/
class Provider extends Model
{
   use HasFactory;
   use Notifiable;

   /**
    * The attributes that are mass assignable.
    * Includes provider's personal details, company info, and shipping address.
    *
    * @var array<int, string>
    */
   protected $fillable = [
       // Personal Information
       'name',          // Full name or display name
       'first_name',    // First name
       'last_name',     // Last name
       'email',
       
       // Company Information
       'company_name',  // Name of the company
       'phone_number',  // Contact phone number
       'group',         // Provider group/category
       
       // Shipping Address Details
       'shipping_address',      // Full shipping address
       'shipping_street_name',  // Street name
       'shipping_house_number', // House/building number
       'shipping_postal_code',  // Postal/ZIP code
       'shipping_city',         // City
       'shipping_country',      // Country
       
       // Additional Data
       'data',                  // JSON field for additional flexible data storage
   ];

   /**
    * The attributes that should be cast.
    * Handles type conversion for JSON data.
    *
    * @var array<string, string>
    */
   protected $casts = [
       'data' => 'json',
   ];

   /**
    * TODO: Consider adding these relationships:
    * - products(): BelongsToMany - Products supplied by this provider
    * - orders(): HasMany - Orders placed with this provider
    * - inventory(): BelongsToMany - Inventory items from this provider
    */
   
   /**
    * TODO: Consider adding these methods:
    * - getFullNameAttribute() - Combine first and last name
    * - getFullShippingAddressAttribute() - Combine all shipping fields
    * - scopeActive() - Query scope for active providers
    * - scopeByGroup() - Query scope to filter by group
    */

        public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get all inventories associated with this provider
     * Many-to-Many: A provider can have multiple inventories,
     * and an inventory can have multiple providers
     * Uses pivot table: inventory_provider
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function inventories(): BelongsToMany
    {
        return $this->belongsToMany(Inventory::class, 'inventory_provider')
            ->withTimestamps();
    }
}