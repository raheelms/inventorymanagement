<?php

namespace App\Filament\Exports;

use App\Enums\ProductStatus;
use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function modifyQuery(Builder $query): Builder
    {
        return $query;
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('name')->label('Name'),
            ExportColumn::make('slug')->label('Slug'),
            ExportColumn::make('collections')
                ->label('Collections')
                ->state(function (Product $record) {
                    // Get all collection names for this product
                    return $record->collections->pluck('name')->toArray();
                })
                ->listAsJson(),
            ExportColumn::make('description')->label('Description')->listAsJson(),
            ExportColumn::make('images')->label('Images')->listAsJson(),
            ExportColumn::make('price')->label('Price'),
            ExportColumn::make('discount_price')->label('Discount Price'),
            ExportColumn::make('discount_to')->label('Discount To'),
            ExportColumn::make('taxes')->label('Taxes'),
            ExportColumn::make('sku')->label('SKU'),
            ExportColumn::make('stock')->label('Stock'),
            ExportColumn::make('safety_stock')->label('Safety Stock'),
            ExportColumn::make('status')
                ->label('Status')
                ->formatStateUsing(function (ProductStatus $state): string {
                    // Using the enum's name or description
                    return ProductStatus::options()[$state->value] ?? (string) $state->value;
                }),
            ExportColumn::make('is_visible')->label('Visibility')->formatStateUsing(fn (bool $state): string => $state ? 'Visible' : 'Hidden'),
            ExportColumn::make('is_featured')->label('Featured')->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
            ExportColumn::make('in_stock')->label('In Stock')->formatStateUsing(fn (bool $state): string => $state ? 'In Stock' : 'Out of Stock'),
            ExportColumn::make('on_sale')->label('On Sale')->formatStateUsing(fn (bool $state): string => $state ? 'On Sale' : 'Regular Price'),
            ExportColumn::make('tags')->label('Tags')->listAsJson(),
            ExportColumn::make('data')->label('Data')->listAsJson(),
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