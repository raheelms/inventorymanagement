<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Exports\ProductExporter;
use App\Filament\Imports\ProductImporter;
use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ImportAction::make()
            ->importer(ProductImporter::class)
            ->label('Import')
            ->icon('heroicon-o-cloud-arrow-up')
            ->csvDelimiter(';')
            //->outlined()
            ->color('purple-500'),

        ExportAction::make()
            ->exporter(ProductExporter::class)
            ->label('Export')
            ->icon('heroicon-o-cloud-arrow-down')
            //->outlined()
            ->color('fuchsia-600'),
                    
        Actions\CreateAction::make()

        ];
    }
}
