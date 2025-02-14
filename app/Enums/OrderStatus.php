<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum OrderStatus : string {

    case PENDING = 'Pending';
    case DISPATCHED = 'Dispatched';
    case OUT_FOR_DELIVERY = 'Out For Delivery';
    case DELIVERED = 'Delivered';
    case CANCELED = 'Canceled';
    
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => Str::title($case->name)])
            ->all();
    }
}