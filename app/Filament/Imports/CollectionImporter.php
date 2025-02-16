<?php

namespace App\Filament\Imports;

use App\Models\Collection;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class CollectionImporter extends Importer
{
    protected static ?string $model = Collection::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name'),
            // ImportColumn::make('slug'),
            ImportColumn::make('description'),
            // ImportColumn::make('images'), // json
            // ImportColumn::make('media_id'),
            // ImportColumn::make('is_visible'), // boolean
            // ImportColumn::make('parent_id'),
            // ImportColumn::make('tags'), // json
            // ImportColumn::make('data'), // json
        ];
    }

    public function resolveRecord(): ?Collection
    {
        // return Collection::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Collection();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your collection import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
