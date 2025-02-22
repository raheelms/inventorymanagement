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
            ImportColumn::make('name')->requiredMapping()->rules(['required', 'string', 'max:255']),
            ImportColumn::make('collections')->rules(['nullable', 'string']),
            ImportColumn::make('price')->requiredMapping()->rules(['required', 'numeric', 'min:0']),
            ImportColumn::make('sku')->requiredMapping()->rules(['required', 'string', 'max:255']),
            ImportColumn::make('stock')->requiredMapping()->rules(['required', 'integer', 'min:0']),
            ImportColumn::make('safety_stock')->rules(['nullable', 'integer', 'min:0']),
            ImportColumn::make('status')->requiredMapping()->rules(['required', Rule::in(['published', 'draft', 'archived', 'discontinued'])]),
            ImportColumn::make('is_visible')->requiredMapping()->rules(['required']),
            ImportColumn::make('is_featured')->requiredMapping()->rules(['required']),
            ImportColumn::make('in_stock')->requiredMapping()->rules(['required']),
            ImportColumn::make('on_sale')->requiredMapping()->rules(['required']),
            ImportColumn::make('description')->rules(['nullable', 'string']),
            ImportColumn::make('user_id')->rules(['nullable', 'integer', 'exists:users,id']),
            ImportColumn::make('images')->rules(['nullable', 'string']),
            ImportColumn::make('taxes')->rules(['nullable', 'numeric', 'min:0']),
            ImportColumn::make('discount_price')->rules(['nullable', 'numeric', 'min:0']),
            ImportColumn::make('discount_to')->rules(['nullable', 'date']),
            ImportColumn::make('data')->rules(['nullable', 'string']),
            ImportColumn::make('tags')->rules(['nullable', 'string']),
        ];
    }

    public function resolveRecord(): ?Product
    {
        try {
            if (empty($this->data['sku'])) {
                Log::warning('Skipping product import: SKU is missing.', $this->data);
                return null;
            }
    
            // Convert boolean values
            foreach (['is_visible', 'is_featured', 'in_stock', 'on_sale'] as $field) {
                if (isset($this->data[$field])) {
                    $this->data[$field] = strtoupper($this->data[$field]) === 'TRUE' ? 1 : 0;
                }
            }
    
            // Ensure numeric fields have default values
            $numericFields = [
                'price' => 0,
                'discount_price' => 0,
                'stock' => 0,
                'safety_stock' => 0,
                'taxes' => 0,
                'user_id' => Filament::auth()->id() ?? 1
            ];
    
            foreach ($numericFields as $field => $defaultValue) {
                $this->data[$field] = isset($this->data[$field]) && $this->data[$field] !== '' 
                    ? (float)$this->data[$field] 
                    : $defaultValue;
            }
    
            // Get collections data before we start modifying the data array
            $collectionsData = $this->data['collections'] ?? null;
            unset($this->data['collections']); // Remove collections from data array
    
            $product = Product::firstOrNew(['sku' => $this->data['sku']]);
            
            $product->fill([
                'name' => $this->data['name'],
                'slug' => Str::slug($this->data['name']),
                'description' => $this->data['description'] ?? null,
                'price' => $this->data['price'],
                'discount_price' => $this->data['discount_price'],
                'stock' => $this->data['stock'],
                'safety_stock' => $this->data['safety_stock'],
                'status' => $this->data['status'] ?? 'draft',
                'is_visible' => $this->data['is_visible'],
                'is_featured' => $this->data['is_featured'],
                'in_stock' => $this->data['in_stock'],
                'on_sale' => $this->data['on_sale'],
                'user_id' => $this->data['user_id'],
                'discount_to' => !empty($this->data['discount_to']) ? Carbon::parse($this->data['discount_to']) : null,
                'taxes' => $this->data['taxes'],
                'data' => $this->data['data'] ?? '{}',
                'tags' => $this->data['tags'] ?? '[]',
                'published_at' => $product->exists ? $product->published_at : now(),
            ]);
    
            if (!empty($this->data['images'])) {
                $product->images = str_starts_with($this->data['images'], '/') 
                    ? [$this->data['images']] 
                    : ['/images/default_image.png'];
            }
    
            // Save product first
            $product->save();
    
            // Then handle collections
            if (!empty($collectionsData)) {
                $collectionNames = array_map('trim', explode(',', $collectionsData));
                
                $collectionIds = collect($collectionNames)->map(function ($name) {
                    return Collection::firstOrCreate(
                        ['name' => trim($name)],
                        [
                            'slug' => Str::slug(trim($name)),
                            'is_visible' => true
                        ]
                    )->id;
                });
    
                $product->collections()->sync($collectionIds);
            }
    
            return $product;
    
        } catch (\Exception $e) {
            Log::error('Product Import Error', [
                'message' => $e->getMessage(),
                'data' => $this->data
            ]);
            return null;
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your product import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}