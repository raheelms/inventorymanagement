<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'company_name',
        'phone_number',
        'group',
        'shipping_address',
        'shipping_street_name',
        'shipping_house_number',
        'shipping_postal_code',
        'shipping_city',
        'shipping_country',
        'use_shipping_address',
        'billing_address',
        'billing_street_name',
        'billing_house_number',
        'billing_postal_code',
        'billing_city',
        'billing_country',
        'data',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'use_shipping_address' => 'boolean',
        'data' => 'json',
    ];

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'customer_order', 'customer_id', 'order_id');
    }
}
