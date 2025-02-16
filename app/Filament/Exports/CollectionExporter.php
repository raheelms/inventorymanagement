<?php

namespace App\Filament\Exports;

use App\Models\Collection;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CollectionExporter extends Exporter
{
    protected static ?string $model = Collection::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name'),
            // ExportColumn::make('slug'),
            ExportColumn::make('description'),
            // ExportColumn::make('images'), // json
            // ExportColumn::make('media_id'),
            // Exportcolumn::make('is_visible'), // boolean
            // Exportcolumn::make('parent_id'),
            // Exportcolumn::make('tags'), // json
            // Exportcolumn::make('data'), // json
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your collection export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
