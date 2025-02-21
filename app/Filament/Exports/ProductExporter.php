<?php

namespace App\Filament\Exports;

use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name'),
            // ExportColumn::make('slug'),
            ExportColumn::make('collections'),
            // ExportColumn::make('description'),
            // ExportColumn::make('images'), // json
            ExportColumn::make('price'),
            // ExportColumn::make('discount_price'),
            // ExportColumn::make('discount_to'),
            // ExportColumn::make('taxes'),
            ExportColumn::make('sku'),
            ExportColumn::make('stock'),
            ExportColumn::make('safety_stock'),
            ExportColumn::make('status'), // enum: 'published', 'draft', 'archived', 'discontinued'
            ExportColumn::make('is_visible'), // boolean
            ExportColumn::make('is_featured'), // boolean
            ExportColumn::make('in_stock'), // boolean
            ExportColumn::make('on_sale'), // boolean
            // ExportColumn::make('tags'), // json
            // ExportColumn::make('data'), // json
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your product export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
