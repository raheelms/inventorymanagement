<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum ProductStatus: String
{
    case PUBLISHED = 'published';
    case DRAFT = 'draft';
    case ARCHIVED = 'archived';
    case DISCONTINUED = 'discontinued';

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
