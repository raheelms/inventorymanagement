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
use Illuminate\Validation\Rule;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),

            ImportColumn::make('collections')
                ->rules(['nullable', 'string']),

            ImportColumn::make('price')
                ->requiredMapping()
                ->rules(['required', 'numeric', 'min:0']),

            ImportColumn::make('sku')
                ->requiredMapping()
                ->rules(['required', 'string', Rule::unique('products', 'sku')->ignore(request('record'))]),

            ImportColumn::make('stock')
                ->requiredMapping()
                ->rules(['required', 'integer', 'min:0']),

            ImportColumn::make('safety_stock')
                ->rules(['nullable', 'integer', 'min:0']),

            ImportColumn::make('status')
                ->requiredMapping()
                ->rules(['required', Rule::in(['published', 'draft', 'archived', 'discontinued'])]),

            ImportColumn::make('is_visible')
                ->requiredMapping()
                ->rules(['required', 'boolean']),

            ImportColumn::make('is_featured')
                ->requiredMapping()
                ->rules(['required', 'boolean']),

            ImportColumn::make('in_stock')
                ->requiredMapping()
                ->rules(['required', 'boolean']),

            ImportColumn::make('on_sale')
                ->requiredMapping()
                ->rules(['required', 'boolean']),

            ImportColumn::make('description')
                ->rules(['nullable', 'string']),

            ImportColumn::make('user_id')
                ->rules(['nullable', 'integer', 'exists:users,id']),

            ImportColumn::make('images')
                ->rules(['nullable']),

            ImportColumn::make('discount_price')
                ->rules(['nullable', 'numeric', 'min:0']),

            ImportColumn::make('discount_to')
                ->rules(['nullable', 'date']),
        ];
    }

    public function resolveRecord(): ?Product
    {
        try {
            Log::info('Raw Import Data', ['data' => $this->data]);

            $sanitizedData = $this->sanitizeImportData($this->data);
            Log::info('Sanitized Record', ['record' => $sanitizedData]);

            $product = Product::updateOrCreate(
                ['sku' => $sanitizedData['sku']],
                array_merge(
                    $sanitizedData,
                    [
                        'slug' => Str::slug($sanitizedData['name']),
                        'published_at' => now(),
                    ]
                )
            );

            // Handle collections after product is created/updated
            if (!empty($sanitizedData['collections'])) {
                $this->syncProductCollections($product, $sanitizedData['collections']);
            }

            return $product;

        } catch (\Exception $e) {
            Log::error('Product Import Error', [
                'message' => $e->getMessage(),
                'data' => $this->data,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
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
            'discount_price' => $this->parseFloat($data['discount_price'] ?? 0),
            'discount_to' => $data['discount_to'] ? Carbon::parse($data['discount_to']) : null,
        ];
    }

    protected function syncProductCollections(Product $product, $collectionsData): void
    {
        if (empty($collectionsData)) {
            return;
        }

        $collectionNames = is_string($collectionsData) 
            ? array_filter(array_map('trim', explode(',', $collectionsData)))
            : (array)$collectionsData;

        $collectionIds = collect($collectionNames)
            ->map(function ($name) {
                return Collection::firstOrCreate(
                    ['name' => trim($name)],
                    [
                        'slug' => Str::slug(trim($name)),
                        'is_visible' => true
                    ]
                )->id;
            })
            ->toArray();

        $product->collections()->sync($collectionIds);
    }

    protected function parseFloat($value): float
    {
        if (is_numeric($value)) {
            return (float)$value;
        }
        
        if (is_string($value)) {
            $value = str_replace(',', '.', trim($value));
            return (float)$value;
        }
        
        return 0.0;
    }

    protected function parseInt($value): int
    {
        return filter_var($value, FILTER_VALIDATE_INT) ?: 0;
    }

    protected function parseBool($value): int
    {
        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        if (is_string($value)) {
            $value = strtolower(trim($value));
            return in_array($value, ['1', 'true', 'yes', 'y', 'on'], true) ? 1 : 0;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ? 1 : 0;
    }

    protected function parseArray($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }

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