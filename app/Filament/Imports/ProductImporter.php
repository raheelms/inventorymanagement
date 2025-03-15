<?php

namespace App\Filament\Imports;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\Collection;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class ProductImporter extends Importer
{
    // Store collections data separately
    protected $importedCollections = null;
    
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('id')
                ->label('ID'),
                
            ImportColumn::make('name')
                ->label('Name')
                ->requiredMapping(),
                
            ImportColumn::make('slug')
                ->label('Slug'),
                
            // This captures collections data but doesn't try to save it
            ImportColumn::make('collections')
                ->label('Try this Collections')
                ->requiredMapping(),
                
            ImportColumn::make('description')
                ->label('Description')
                ->castStateUsing(function ($state) {
                    if (blank($state)) {
                        return null;
                    }
                    
                    // Try to parse as JSON if it starts with {
                    if (is_string($state) && str_starts_with(trim($state), '{')) {
                        try {
                            $parsed = json_decode($state, true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                return $parsed;
                            }
                        } catch (\Exception $e) {
                            // Fall through to simple text handling
                        }
                    }
                    
                    // Simple text format for Tiptap
                    return [
                        'type' => 'doc',
                        'content' => [
                            [
                                'type' => 'paragraph',
                                'attrs' => [
                                    'class' => null,
                                    'style' => null,
                                    'textAlign' => 'start'
                                ],
                                'content' => [
                                    [
                                        'type' => 'text',
                                        'text' => (string) $state
                                    ]
                                ]
                            ]
                        ]
                    ];
                }),
                
            ImportColumn::make('images')
                ->label('Images')
                ->castStateUsing(function ($state) {
                    if (blank($state)) {
                        return ['/images/default_image.png'];
                    }
                    
                    // Handle JSON string
                    if (is_string($state) && str_starts_with(trim($state), '[')) {
                        try {
                            $parsed = json_decode($state, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                                return array_map(function ($path) {
                                    return is_string($path) ? str_replace('\/', '/', $path) : $path;
                                }, $parsed);
                            }
                        } catch (\Exception $e) {
                            // Fall through
                        }
                    }
                    
                    // Standard array handling
                    if (is_array($state)) {
                        $fixed = [];
                        foreach ($state as $image) {
                            if (is_string($image)) {
                                $fixed[] = str_replace('\/', '/', $image);
                            } else {
                                $fixed[] = $image;
                            }
                        }
                        return $fixed;
                    }
                    
                    // Single string value
                    return [str_replace('\/', '/', (string) $state)];
                }),
                
            ImportColumn::make('price')
                ->label('Price'),
                
            ImportColumn::make('discount_price')
                ->label('Discount Price'),
                
            ImportColumn::make('discount_to')
                ->label('Discount To'),
                
            ImportColumn::make('taxes')
                ->label('Taxes'),
                
            ImportColumn::make('sku')
                ->label('SKU'),
                
            ImportColumn::make('stock')
                ->label('Stock'),
                
            ImportColumn::make('safety_stock')
                ->label('Safety Stock'),
                
            ImportColumn::make('status')
                ->label('Status')
                ->castStateUsing(function ($state) {
                    if (blank($state)) {
                        return ProductStatus::DRAFT;
                    }
                    
                    // Handle string values
                    if (is_string($state)) {
                        $normalizedState = strtolower(trim($state));
                        
                        if ($normalizedState === 'published') {
                            return ProductStatus::PUBLISHED;
                        } elseif ($normalizedState === 'draft') {
                            return ProductStatus::DRAFT;
                        } elseif ($normalizedState === 'archived') {
                            return ProductStatus::ARCHIVED;
                        } elseif ($normalizedState === 'discontinued') {
                            return ProductStatus::DISCONTINUED;
                        }
                    }
                    
                    // Already an enum
                    if ($state instanceof ProductStatus) {
                        return $state;
                    }
                    
                    // Default
                    return ProductStatus::DRAFT;
                }),
                
            ImportColumn::make('is_visible')
                ->label('Visibility')
                ->guess(['Visibility']),
                
            ImportColumn::make('is_featured')
                ->label('Featured')
                ->guess(['Featured']),
                
            ImportColumn::make('in_stock')
                ->label('In Stock')
                ->guess(['In Stock']),
                
            ImportColumn::make('on_sale')
                ->label('On Sale')
                ->guess(['On Sale']),
                
                ImportColumn::make('tags')
                ->label('Tags')
                ->rules(['nullable'])
                ->castStateUsing(function ($state) {
                    if (blank($state)) {
                        return null;
                    }
                    
                    // Try to handle as JSON string
                    if (is_string($state) && str_starts_with(trim($state), '[')) {
                        try {
                            $parsed = json_decode($state, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                                // Clean any quotes or brackets from individual tags
                                $clean = [];
                                foreach ($parsed as $tag) {
                                    if (is_string($tag)) {
                                        $clean[] = trim($tag, '"\'[] ');
                                    } else {
                                        $clean[] = $tag;
                                    }
                                }
                                return $clean;
                            }
                        } catch (\Exception $e) {
                            // Continue to next method
                        }
                    }
                    
                    // Handle array input
                    if (is_array($state)) {
                        // Clean up any quotes or brackets in array items
                        $clean = [];
                        foreach ($state as $tag) {
                            if (is_string($tag)) {
                                $clean[] = trim($tag, '"\'[] ');
                            } else {
                                $clean[] = $tag;
                            }
                        }
                        return $clean;
                    }
                    
                    // Handle comma-separated string
                    if (is_string($state) && str_contains($state, ',')) {
                        return array_map(function ($tag) {
                            return trim($tag, '"\'[] ');
                        }, explode(',', $state));
                    }
                    
                    // Single string value
                    return [trim((string) $state, '"\'[] ')];
                }),
                
            ImportColumn::make('data')
                ->label('Additional Data')
                ->rules(['nullable'])
                ->castStateUsing(function ($state) {
                    if (blank($state)) {
                        return null;
                    }
                    
                    if (is_string($state)) {
                        // Try to parse as JSON
                        try {
                            $parsed = json_decode($state, true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                return $parsed;
                            }
                        } catch (\Exception $e) {
                            // Fall through to default handling
                        }
                        
                        // Store as simple value
                        return ['value' => $state];
                    }
                    
                    if (is_array($state)) {
                        return $state;
                    }
                    
                    // Default value for other types
                    return ['value' => (string) $state];
                }),
        ];
    }

    public function resolveRecord(): ?Product
    {
        dd($this->data);
        // Store collections data separately for later use
        if (isset($this->data['_collections'])) {
            $this->importedCollections = $this->data['_collections'];
            
            // Remove from data to prevent saving to the database
            unset($this->data['_collections']);
        }
        // Find by SKU first
        if (isset($this->data['sku']) && !empty($this->data['sku'])) {
            $product = Product::where('sku', $this->data['sku'])->first();
            if ($product) return $product;
        }
        
        // Find by ID as fallback
        if (isset($this->data['id']) && !empty($this->data['id'])) {
            $product = Product::find($this->data['id']);
            if ($product) return $product;
        }
        
        // Create new
        return new Product();
    }

    protected function beforeSave(): void
    {
        // Generate slug if needed
        if (empty($this->record->slug)) {
            $this->record->slug = Str::slug($this->data['name']);
        }
        
        // Set published_at if needed
        if (empty($this->record->published_at)) {
            $this->record->published_at = now();
        }
        
        // Set user_id if needed
        if (empty($this->record->user_id)) {
            $this->record->user_id = 1;
        }
    }
    
    protected function afterSave(): void
    {
        // Process collections relationship if data was captured
        if (!empty($this->importedCollections)) {
            $collectionsData = $this->importedCollections;
            $collectionNames = [];
            
            // Parse collections data
            if (is_string($collectionsData) && str_starts_with(trim($collectionsData), '[')) {
                try {
                    $parsed = json_decode($collectionsData, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                        $collectionNames = $parsed;
                    }
                } catch (\Exception $e) {
                    // Continue with string processing
                }
            } else if (is_string($collectionsData)) {
                if (str_contains($collectionsData, ',')) {
                    $collectionNames = array_map('trim', explode(',', $collectionsData));
                } else {
                    $collectionNames = [trim($collectionsData)];
                }
            } else if (is_array($collectionsData)) {
                $collectionNames = $collectionsData;
            }
            
            // Find collection IDs using pluck method
            if (!empty($collectionNames)) {
                $collectionIds = Collection::whereIn('name', $collectionNames)
                    ->pluck('id')
                    ->toArray();
                
                // Sync collections
                if (!empty($collectionIds)) {
                    $this->record->collections()->sync($collectionIds);
                }
            }
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