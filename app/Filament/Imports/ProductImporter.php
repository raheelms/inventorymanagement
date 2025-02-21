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
                ->rules(['nullable', 'string'])
                ->relationship(resolveUsing: function (string $state): ?Collection {
                    $names = array_map('trim', explode(',', $state));
                    return Collection::query()
                        ->where('name', $names[0])
                        ->first() ?? Collection::create([
                            'name' => $names[0],
                            'slug' => Str::slug($names[0]),
                            'is_visible' => true
                        ]);
                }),

            ImportColumn::make('price')
                ->requiredMapping()
                ->rules(['required', 'numeric', 'min:0']),

            ImportColumn::make('sku')
                ->requiredMapping()
                ->rules(['required', 'string']),

            ImportColumn::make('stock')
                ->requiredMapping()
                ->rules(['required', 'numeric', 'min:0']),

            ImportColumn::make('safety_stock')
                ->rules(['nullable', 'numeric', 'min:0']),

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

            // Store collections data separately
            $collectionsData = $this->data['collections'] ?? null;

            // Create or update the product
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

            // Handle additional collections
            if ($collectionsData) {
                $collectionNames = array_map('trim', explode(',', $collectionsData));
                if (count($collectionNames) > 1) {
                    // Skip the first name as it's handled by the relationship
                    array_shift($collectionNames);
                    $collections = collect($collectionNames)->map(function ($name) {
                        return Collection::firstOrCreate(
                            ['name' => $name],
                            [
                                'slug' => Str::slug($name),
                                'is_visible' => true
                            ]
                        );
                    });
                    $product->collections()->syncWithoutDetaching($collections->pluck('id'));
                }
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
        $sanitized = [
            'name' => trim($data['name'] ?? ''),
            'price' => (float)($data['price'] ?? 0),
            'sku' => trim($data['sku'] ?? ''),
            'stock' => (int)($data['stock'] ?? 0),
            'safety_stock' => (int)($data['safety_stock'] ?? 0),
            'status' => trim($data['status'] ?? 'draft'),
            'description' => trim($data['description'] ?? ''),
            'user_id' => (int)($data['user_id'] ?? Filament::auth()->id() ?? 1),
            'discount_price' => $data['discount_price'] ?? 0,
            'discount_to' => $data['discount_to'],
        ];

        // Handle boolean fields
        foreach (['is_visible', 'is_featured', 'in_stock', 'on_sale'] as $boolField) {
            $sanitized[$boolField] = strtoupper(trim($data[$boolField] ?? 'false')) === 'TRUE' ? 1 : 0;
        }

        // Handle images
        if (!empty($data['images'])) {
            if (is_string($data['images']) && str_starts_with($data['images'], '/')) {
                $sanitized['images'] = [$data['images']];
            } else {
                $sanitized['images'] = $this->parseArray($data['images']);
            }
        } else {
            $sanitized['images'] = ['/images/default_image.png'];
        }

        return $sanitized;
    }

    protected function parseArray($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            if ($value === '[]' || $value === '{}') {
                return [];
            }

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