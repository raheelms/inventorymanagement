<?php

namespace App\Filament\Exports;

use App\Models\Collection;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;

class CollectionExporter extends Exporter
{
    protected static ?string $model = Collection::class;

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
            ExportColumn::make('description')->label('Description')->listAsJson(),            
            ExportColumn::make('images')->label('Images')->listAsJson(),            
            ExportColumn::make('media_id')->label('Media'),
            ExportColumn::make('parent_id')->label('Parent'),            
            ExportColumn::make('is_visible')->label('Visibility')->formatStateUsing(fn (bool $state): string => $state ? 'Visible' : 'Hidden'),            
            ExportColumn::make('tags')->label('Tags')->listAsJson(),            
            ExportColumn::make('data')->label('Data')->listAsJson(),
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