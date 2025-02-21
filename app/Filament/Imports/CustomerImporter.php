<?php

namespace App\Filament\Imports;

use App\Models\Customer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;

class CustomerImporter extends Importer
{
    protected static ?string $model = Customer::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('first_name'),
            ImportColumn::make('last_name'),
            ImportColumn::make('email'),
            ImportColumn::make('company_name'),
            ImportColumn::make('phone_number'),
            ImportColumn::make('group'),
            ImportColumn::make('shipping_street_name'),
            ImportColumn::make('shipping_house_number'),
            ImportColumn::make('shipping_postal_code'),
            ImportColumn::make('shipping_city'),
            ImportColumn::make('shipping_country'),
            ImportColumn::make('use_shipping_address'),
            ImportColumn::make('billing_street_name'),
            ImportColumn::make('billing_house_number'),
            ImportColumn::make('billing_postal_code'),
            ImportColumn::make('billing_city'),
            ImportColumn::make('billing_country'),
            ImportColumn::make('data'),
        ];
    }

    public function resolveRecord(): ?Customer
    {
        // Validate email (required and valid format)
        if (empty($this->data['email'])) {
            Log::warning('Skipping customer import: Email is missing.', $this->data);
            return null;
        }

        if (!filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)) {
            Log::warning('Skipping customer import: Invalid email format.', $this->data);
            return null;
        }

        // Combine first and last name for the `name` field
        $name = trim($this->data['first_name'] . ' ' . $this->data['last_name']);

        // Find or create the customer
        return Customer::firstOrNew(
            ['email' => $this->data['email']],
            [
                'name' => $name,
                'first_name' => $this->data['first_name'],
                'last_name' => $this->data['last_name'],
                'company_name' => $this->data['company_name'] ?? null,
                'phone_number' => $this->data['phone_number'] ?? null,
                'group' => $this->data['group'] ?? null,
                'shipping_street_name' => $this->data['shipping_street_name'] ?? null,
                'shipping_house_number' => $this->data['shipping_house_number'] ?? null,
                'shipping_postal_code' => $this->data['shipping_postal_code'] ?? null,
                'shipping_city' => $this->data['shipping_city'] ?? null,
                'shipping_country' => $this->data['shipping_country'] ?? null,
                'use_shipping_address' => $this->data['use_shipping_address'] ?? false,
                'billing_street_name' => $this->data['billing_street_name'] ?? null,
                'billing_house_number' => $this->data['billing_house_number'] ?? null,
                'billing_postal_code' => $this->data['billing_postal_code'] ?? null,
                'billing_city' => $this->data['billing_city'] ?? null,
                'billing_country' => $this->data['billing_country'] ?? null,
                'data' => $this->data['data'] ?? null,
            ]
        );
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your customer import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';

            // Log failed rows for debugging
            $failedRows = $import->getFailedRows();
            foreach ($failedRows as $failedRow) {
                Log::error('Failed to import customer row:', [
                    'row_data' => $failedRow->data,
                    'error' => $failedRow->validation_error,
                ]);
            }
        }

        return $body;
    }
}