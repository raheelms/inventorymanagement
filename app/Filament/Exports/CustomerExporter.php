<?php

namespace App\Filament\Exports;

use App\Models\Customer;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CustomerExporter extends Exporter
{
    protected static ?string $model = Customer::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('first_name'),
            ExportColumn::make('last_name'),
            ExportColumn::make('email'),
            ExportColumn::make('company_name'),
            ExportColumn::make('phone_number'),
            ExportColumn::make('group'),
            ExportColumn::make('shipping_street_name'),
            ExportColumn::make('shipping_house_number'),
            ExportColumn::make('shipping_postal_code'),
            ExportColumn::make('shipping_city'),
            ExportColumn::make('shipping_country'),
            ExportColumn::make('use_shipping_address'),
            ExportColumn::make('billing_street_name'),
            ExportColumn::make('billing_house_number'),
            ExportColumn::make('billing_postal_code'),
            ExportColumn::make('billing_city'),
            ExportColumn::make('billing_country'),
            ExportColumn::make('data'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your customer export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
