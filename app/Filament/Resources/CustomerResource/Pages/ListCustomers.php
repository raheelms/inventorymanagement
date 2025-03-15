<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Exports\CustomerExporter;
use App\Filament\Imports\CustomerImporter;
use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(CustomerImporter::class)
                ->label('Import')
                ->icon('heroicon-o-cloud-arrow-up')
                ->csvDelimiter(';')
                //->outlined()
                ->color('purple-500'),

            ExportAction::make()
                ->exporter(CustomerExporter::class)
                ->label('Export')
                ->icon('heroicon-o-cloud-arrow-down')
                //->outlined()
                ->color('fuchsia-600'),

            Actions\CreateAction::make()
        ];
    }
}
