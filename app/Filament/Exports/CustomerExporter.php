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
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('first_name')->label('First Name'),
            ExportColumn::make('last_name')->label('Last Name'),
            ExportColumn::make('email')->label('Email'),
            ExportColumn::make('company_name')->label('Company Name'),
            ExportColumn::make('phone_number')->label('Phone Number'),
            ExportColumn::make('group')->label('Group'),
            ExportColumn::make('shipping_street_name')->label('Shipping Street Name'),
            ExportColumn::make('shipping_house_number')->label('Shipping House Number'),
            ExportColumn::make('shipping_postal_code')->label('Shipping Postal Code'),
            ExportColumn::make('shipping_city')->label('Shipping City'),
            ExportColumn::make('shipping_country')->label('Shipping Country'),
            ExportColumn::make('use_shipping_address')->label('Use Shipping Address')
                ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
            ExportColumn::make('billing_street_name')->label('Billing Street Name'),
            ExportColumn::make('billing_house_number')->label('Billing House Number'),
            ExportColumn::make('billing_postal_code')->label('Billing Postal Code'),
            ExportColumn::make('billing_city')->label('Billing City'),
            ExportColumn::make('billing_country')->label('Billing Country'),
            ExportColumn::make('data')->label('Data')->listAsJson(),
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