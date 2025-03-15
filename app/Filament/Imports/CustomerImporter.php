<?php

namespace App\Filament\Imports;

use App\Models\Customer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerImporter extends Importer
{
    protected static ?string $model = Customer::class;

    public static function getOptionsFormComponents(): array
    {
        return [
            \Filament\Forms\Components\Toggle::make('preserveExisting')
                ->label('Preserve existing records')
                ->helperText('When enabled, only updates matching records and adds new ones. When disabled, replaces all data.')
                ->default(true),
        ];
    }

    protected function setUp(): void
    {
        // Handle table truncation if preserveExisting is disabled
        $preserveExisting = $this->options['preserveExisting'] ?? true;
        
        if (!$preserveExisting) {
            Log::warning('Replace mode enabled - truncating customers table');
            
            try {
                // Only truncate if we're explicitly told not to preserve data
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                Customer::query()->delete();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                
                Log::info('Customer table truncated successfully');
            } catch (\Exception $e) {
                Log::error('Failed to truncate customers table', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('id')->label('ID')->rules(['nullable', 'integer']),
            ImportColumn::make('first_name')->label('First Name')->requiredMapping()->rules(['required', 'string', 'max:255']),
            ImportColumn::make('last_name')->label('Last Name')->requiredMapping()->rules(['required', 'string', 'max:255']),
            ImportColumn::make('email')->label('Email')->requiredMapping()->rules(['required', 'email', 'max:255']),
            ImportColumn::make('company_name')->label('Company Name')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('phone_number')->label('Phone Number')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('group')->label('Group')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('shipping_street_name')->label('Shipping Street Name')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('shipping_house_number')->label('Shipping House Number')->rules(['nullable', 'string', 'max:50']),
            ImportColumn::make('shipping_postal_code')->label('Shipping Postal Code')->rules(['nullable', 'string', 'max:50']),
            ImportColumn::make('shipping_city')->label('Shipping City')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('shipping_country')->label('Shipping Country')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('use_shipping_address')->label('Use Shipping Address')->rules(['nullable']),
            ImportColumn::make('billing_street_name')->label('Billing Street Name')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('billing_house_number')->label('Billing House Number')->rules(['nullable', 'string', 'max:50']),
            ImportColumn::make('billing_postal_code')->label('Billing Postal Code')->rules(['nullable', 'string', 'max:50']),
            ImportColumn::make('billing_city')->label('Billing City')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('billing_country')->label('Billing Country')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('data')->label('Data')->rules(['nullable']),
        ];
    }

    public function resolveRecord(): ?Customer
    {
        try {
            // Validate and clean email first
            $email = trim($this->data['email'] ?? '');
            
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Log::warning('Skipping customer import: Email is missing or invalid.', [
                    'email' => $email,
                    'data' => $this->data
                ]);
                return null;
            }
            
            // Check for existing customer by email first
            $existingByEmail = Customer::where('email', $email)->first();
            
            if ($existingByEmail) {
                Log::info('Found existing customer by email', [
                    'email' => $email,
                    'id' => $existingByEmail->id
                ]);
                return $existingByEmail;
            }
            
            // Fallback to ID check if email not found
            if (!empty($this->data['id']) && is_numeric($this->data['id'])) {
                $customerId = (int)$this->data['id'];
                $existingById = Customer::find($customerId);
                
                if ($existingById) {
                    Log::info('Found existing customer by ID', [
                        'id' => $customerId
                    ]);
                    return $existingById;
                }
            }
            
            // Clean up names
            $firstName = trim($this->data['first_name'] ?? '');
            $lastName = trim($this->data['last_name'] ?? '');
            
            if (empty($firstName) && empty($lastName)) {
                Log::warning('Skipping customer import: Both first and last name are missing.', $this->data);
                return null;
            }
            
            // Create a new customer record if no match found
            Log::info('Creating new customer', [
                'email' => $email,
                'name' => $firstName . ' ' . $lastName
            ]);
            return new Customer();
            
        } catch (\Exception $e) {
            Log::error('Error resolving customer record', [
                'message' => $e->getMessage(),
                'data' => $this->data
            ]);
            return null;
        }
    }
    
    protected function beforeSave(): void 
    {
        try {
            Log::info('Before saving customer', [
                'existing_record' => $this->record?->exists,
                'record_id' => $this->record?->id,
                'email' => $this->data['email'] ?? null,
            ]);
    
            // Clean input data
            $firstName = trim($this->data['first_name'] ?? '');
            $lastName = trim($this->data['last_name'] ?? '');
            $email = trim($this->data['email'] ?? '');
            $companyName = trim($this->data['company_name'] ?? '');
            $phoneNumber = trim($this->data['phone_number'] ?? '');
            $group = trim($this->data['group'] ?? '');
            
            // Generate full name
            $name = trim($firstName . ' ' . $lastName);
            
            // Handle use_shipping_address as boolean
            $useShippingAddress = false;
            $shippingAddressField = $this->data['use_shipping_address'] ?? null;
            
            if (!is_null($shippingAddressField)) {
                if (is_string($shippingAddressField)) {
                    $shippingAddressField = strtolower(trim($shippingAddressField));
                    $useShippingAddress = in_array($shippingAddressField, ['true', '1', 'yes', 'y', 'on'], true);
                } else {
                    $useShippingAddress = (bool) $shippingAddressField;
                }
            }
            
            // Handle data JSON field
            $data = [];
            if (!empty($this->data['data'])) {
                if (is_string($this->data['data'])) {
                    try {
                        $parsed = json_decode($this->data['data'], true);
                        $data = is_array($parsed) ? $parsed : ['value' => $this->data['data']];
                    } catch (\Exception $e) {
                        $data = ['value' => $this->data['data']];
                    }
                } else if (is_array($this->data['data'])) {
                    $data = $this->data['data'];
                }
            }
            
            // Fill record with cleaned data
            $this->record->fill([
                'name' => $name,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'company_name' => !empty($companyName) ? $companyName : null,
                'phone_number' => !empty($phoneNumber) ? $phoneNumber : null,
                'group' => !empty($group) ? $group : null,
                'shipping_street_name' => trim($this->data['shipping_street_name'] ?? ''),
                'shipping_house_number' => trim($this->data['shipping_house_number'] ?? ''),
                'shipping_postal_code' => trim($this->data['shipping_postal_code'] ?? ''),
                'shipping_city' => trim($this->data['shipping_city'] ?? ''),
                'shipping_country' => trim($this->data['shipping_country'] ?? ''),
                'use_shipping_address' => $useShippingAddress,
                'billing_street_name' => trim($this->data['billing_street_name'] ?? ''),
                'billing_house_number' => trim($this->data['billing_house_number'] ?? ''),
                'billing_postal_code' => trim($this->data['billing_postal_code'] ?? ''),
                'billing_city' => trim($this->data['billing_city'] ?? ''),
                'billing_country' => trim($this->data['billing_country'] ?? ''),
                'data' => $data,
            ]);
            
            Log::info('Customer ready to save', [
                'email' => $email,
                'name' => $name
            ]);
        } catch (\Exception $e) {
            Log::error('Error in beforeSave', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function afterSave(): void
    {
        try {
            // Verify the record was saved
            if ($this->record && $this->record->exists) {
                Log::info('Customer saved successfully', [
                    'id' => $this->record->id,
                    'email' => $this->record->email,
                    'name' => $this->record->name
                ]);
                
                // Force a database sync to ensure data is committed
                DB::connection()->commit();
                
                // Verify in database
                $freshRecord = Customer::find($this->record->id);
                if ($freshRecord) {
                    Log::info('Verified customer in database', [
                        'id' => $freshRecord->id
                    ]);
                } else {
                    Log::warning('Could not verify customer in database after save', [
                        'id' => $this->record->id
                    ]);
                    
                    // Try forcing a save again
                    $this->record->save();
                    Log::info('Attempted forced save again', [
                        'id' => $this->record->id
                    ]);
                }
            } else {
                Log::warning('Record appears not to have been saved', [
                    'record_exists' => $this->record ? $this->record->exists : false,
                    'record_id' => $this->record ? $this->record->id : null
                ]);
                
                // Try forcing a save
                if ($this->record) {
                    $this->record->save();
                    Log::info('Attempted forced save', [
                        'id' => $this->record->id
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in afterSave', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
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