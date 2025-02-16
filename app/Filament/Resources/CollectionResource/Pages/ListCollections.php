<?php

namespace App\Filament\Resources\CollectionResource\Pages;

use App\Filament\Exports\CollectionExporter;
use App\Filament\Imports\CollectionImporter;
use App\Filament\Resources\CollectionResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListCollections extends ListRecords
{
    protected static string $resource = CollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(CollectionImporter::class)
                ->label('Import')
                ->icon('heroicon-o-cloud-arrow-up')
                //->outlined()
                ->color('purple-500'),

            ExportAction::make()
                ->exporter(CollectionExporter::class)
                ->label('Export')
                ->icon('heroicon-o-cloud-arrow-down')
                //->outlined()
                ->color('fuchsia-600'),

            Actions\CreateAction::make()
        ];
    }
}
