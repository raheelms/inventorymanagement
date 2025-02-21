<?php

namespace App\Filament\Imports;

use App\Models\Product;
use App\Models\Collection;
use Carbon\Carbon;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Facades\Filament;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')->requiredMapping()->rules(['required', 'string', 'max:255']),
            // //ImportColumn::make('collections')->relationship(resolveUsing: function (string $state): ?Collection {
            //         return Collection::query()
            //             ->where('name', $state)
            //             ->first();
            //     }),
            ImportColumn::make('price')->requiredMapping()->rules(['required', 'numeric', 'min:0']),
            ImportColumn::make('sku')->requiredMapping()->rules(['required', 'string']),
            ImportColumn::make('stock')->requiredMapping()->rules(['required', 'integer', 'min:0']),
            ImportColumn::make('safety_stock')->rules(['nullable', 'integer', 'min:0']),
            ImportColumn::make('status')->requiredMapping()->rules(['required', 'string']),
            ImportColumn::make('is_visible') ->requiredMapping()->rules(['required']),
            ImportColumn::make('is_featured')->requiredMapping()->rules(['required']),
            ImportColumn::make('in_stock')->requiredMapping()->rules(['required']),
            ImportColumn::make('on_sale')->requiredMapping()->rules(['required']),
            ImportColumn::make('description')->rules(['nullable', 'string']),
            ImportColumn::make('user_id')->rules(['nullable', 'integer', 'exists:users,id']),
            ImportColumn::make('images')->rules(['nullable']),
            //ImportColumn::make('taxes')->rules(['nullable']),
            ImportColumn::make('discount_price')->rules(['nullable', 'numeric', 'min:0']),
            ImportColumn::make('discount_to')->rules(['nullable', 'date']),
            //ImportColumn::make('data')->rules(['nullable']),
            //ImportColumn::make('tags')>rules(['nullable']),
        ];
    }

    public function resolveRecord(): ?Product
    {
        // Log the raw import data for debugging
        Log::info('Raw Import Data', ['data' => $this->data]);
    
        try {
            // Manually parse the CSV row if it's a string
            if (is_string($this->data['row_data'] ?? null)) {
                $parsedData = $this->parseCSVRow($this->data['row_data']);
            } else {
                $parsedData = $this->data;
            }
    
            // Log parsed data for additional visibility
            Log::info('Parsed CSV Data', ['parsedData' => $parsedData]);
    
            // Sanitize and validate data
            $record = $this->sanitizeImportData($parsedData);
            
            // Log sanitized record
            Log::info('Sanitized Record', ['record' => $record]);
    
            if($record['sku'])
            {
                // Create or update product
                $product = Product::updateOrCreate(
                    ['sku' => $record['sku']],
                    $record
                );
                return $product;
            }
        } catch (\Exception $e) {
            Log::error('Product Import Error', [
                'message' => $e->getMessage(),
                'data' => $this->data,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    protected function parseCSVRow(string $rowData): array
    {
        // Use str_getcsv with custom options to handle complex CSV
        $parsed = str_getcsv($rowData, ',', '"');

        // Map parsed data to expected keys
        return [
            'name' => $parsed[0] ?? null,
            'collections' => $parsed[1] ?? null,
            'price' => $parsed[2] ?? null,
            'sku' => $parsed[3] ?? null,
            'stock' => $parsed[4] ?? null,
            'safety_stock' => $parsed[5] ?? null,
            'status' => $parsed[6] ?? null,
            'is_visible' => $parsed[7] ?? null,
            'is_featured' => $parsed[8] ?? null,
            'in_stock' => $parsed[9] ?? null,
            'on_sale' => $parsed[10] ?? null,
            'description' => $parsed[11] ?? null,
            'user_id' => $parsed[12] ?? null,
            'images' => $parsed[13] ?? null,
            'taxes' => $parsed[14] ?? null,
            'discount_price' => $parsed[15] ?? null,
            'discount_to' => $parsed[16] ?? null,
            'data' => $parsed[17] ?? null,
            'tags' => $parsed[18] ?? null,
        ];
    }

    protected function sanitizeImportData(array $data): array
    {
        return [
            'name' => trim($data['name'] ?? ''),
            'collections' => $data['collections'] ?? '',
            'price' => $this->parseFloat($data['price'] ?? 0),
            'sku' => trim($data['sku'] ?? ''),
            'stock' => $this->parseInt($data['stock'] ?? 0),
            'safety_stock' => $this->parseInt($data['safety_stock'] ?? 0),
            'status' => trim($data['status'] ?? 'draft'),
            'is_visible' => $this->parseBool($data['is_visible'] ?? false),
            'is_featured' => $this->parseBool($data['is_featured'] ?? false),
            'in_stock' => $this->parseBool($data['in_stock'] ?? false),
            'on_sale' => $this->parseBool($data['on_sale'] ?? false),
            'description' => trim($data['description'] ?? ''),
            'user_id' => $this->parseInt($data['user_id'] ?? Filament::auth()->id() ?? 1),
            'images' => $this->parseArray($data['images'] ?? ['/images/default_image.png']),
            'taxes' => trim($data['taxes'] ?? 0),
            'discount_price' => $this->parseFloat($data['discount_price'] ?? 0),
            'discount_to' => isset($data['discount_to']) ? Carbon::createFromFormat('d/m/y',$data['discount_to']) : null,
            'data' => $this->parseArray($data['data'] ?? []),
            'tags' => $this->parseArray($data['tags'] ?? []),
        ];
    }

    protected function getProductDefaultData(array $data): array
    {
        return [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'],
            'user_id' => $data['user_id'],
            'images' => $data['images'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'safety_stock' => $data['safety_stock'],
            'status' => $data['status'],
            'is_visible' => $data['is_visible'],
            'is_featured' => $data['is_featured'],
            'in_stock' => $data['in_stock'],
            'on_sale' => $data['on_sale'],
            'published_at' => now(),
            'taxes' => $data['taxes'],
            'discount_price' => $data['discount_price'],
            'discount_to' => $data['discount_to'],
            // 'data' => $data['data'],
            // 'tags' => $data['tags'],
        ];
    }

    protected function getProductUpdateData(array $data): array
    {
        return [
            'name' => $data['name'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'safety_stock' => $data['safety_stock'],
            'status' => $data['status'],
            'is_visible' => $data['is_visible'],
            'is_featured' => $data['is_featured'],
            'in_stock' => $data['in_stock'],
            'on_sale' => $data['on_sale'],
        ];
    }

    protected function syncProductCollections(Product $product, $collectionsData): void
    {
        if (empty($collectionsData)) {
            return;
        }

        // Handle both comma-separated string and array inputs
        $collectionNames = is_string($collectionsData) 
            ? array_filter(array_map('trim', explode(',', trim($collectionsData, '"')))) 
            : (array)$collectionsData;

        $collectionIds = collect($collectionNames)
            ->map(function ($name) {
                return Collection::firstOrCreate(
                    ['name' => trim($name)],
                    ['slug' => Str::slug(trim($name))]
                )->id;
            })
            ->toArray();

        $product->collections()->sync($collectionIds);
    }

    protected function parseFloat($value): ?float
    {
        return $value !== null && $value !== '' 
            ? floatval(str_replace(',', '.', $value)) 
            : null;
    }

    protected function parseInt($value): ?int
    {
        return $value !== null && $value !== '' 
            ? intval($value) 
            : null;
    }

    protected function parseBool($value): int
    {
        // If the value is already an integer, return it
        if (is_int($value)) {
            return $value ? 1 : 0;
        }
    
        // If it's already a boolean, convert to integer
        if (is_bool($value)) {
            return $value ? 1 : 0;
        }
    
        // If it's a string, convert it
        if (is_string($value)) {
            $value = strtolower(trim($value));
            
            // Explicit true values
            $trueValues = ['1', 'true', 'yes', 'y', 'on'];
            
            // Explicit false values
            $falseValues = ['0', 'false', 'no', 'n', 'off'];
    
            // Check true values first
            if (in_array($value, $trueValues, true)) {
                return 1;
            }
    
            // Then check false values
            if (in_array($value, $falseValues, true)) {
                return 0;
            }
        }
    
        // For numeric values
        if (is_numeric($value)) {
            return $value ? 1 : 0;
        }
    
        // Default to 0
        return 0;
    }

    protected function parseArray($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            // Try JSON decode first
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }

            // Fallback to comma-separated
            return array_filter(array_map('trim', explode(',', $value)));
        }

        return [];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your product import has completed and ' . 
            number_format($import->successful_rows) . ' ' . 
            str('row')->plural($import->successful_rows) . ' imported.';

        $failedRowsCount = $import->getFailedRowsCount();
        if ($failedRowsCount > 0) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . 
                str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}