<?php

namespace App\Filament\Imports;

use App\Models\Customer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class CustomerImporter extends Importer
{
    protected static ?string $model = Customer::class;

    public static function getColumns(): array
    {
        return [
            // ImportColumn::make('name'),
            ImportColumn::make('first_name'),
            ImportColumn::make('last_name'),
            ImportColumn::make('email'),
            // ImportColumn::make('email_verified_at'),
            // ImportColumn::make('password'),
            // ImportColumn::make('company_name'),
            // ImportColumn::make('phone_number'),
            // ImportColumn::make('group'),
            // ImportColumn::make('shipping_street_name'),
            // ImportColumn::make('shipping_house_number'),
            // ImportColumn::make('shipping_postal_code'),
            // ImportColumn::make('shipping_city'),
            // ImportColumn::make('shipping_country'),
            // ImportColumn::make('use_shipping_address'), // boolean
            // ImportColumn::make('billing_street_name'),
            // ImportColumn::make('billing_house_number'),
            // ImportColumn::make('billing_address'),
            // ImportColumn::make('billing_city'),
            // ImportColumn::make('billing_country'),
            // ImportColumn::make('data'), // json
        ];
    }

    public function resolveRecord(): ?Customer
    {
        // return Customer::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Customer();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your customer import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
