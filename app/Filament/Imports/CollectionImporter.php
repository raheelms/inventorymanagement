<?php

namespace App\Filament\Imports;

use App\Models\Collection;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CollectionImporter extends Importer
{
    protected static ?string $model = Collection::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('id')
                ->label('ID')
                ->rules(['nullable', 'integer'])
                ->integer(),
                
            ImportColumn::make('name')
                ->label('Name')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),
                
            ImportColumn::make('slug')
                ->label('Slug')
                ->rules(['nullable', 'string', 'max:255'])
                ->helperText('Will be auto-generated from name if not provided.'),
                
            ImportColumn::make('description')
                ->label('Description')
                ->rules(['nullable'])
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
                ->rules(['nullable'])
                ->castStateUsing(function ($state) {
                    if (blank($state)) {
                        return ['/images/default_image.png'];
                    }
                    
                    // Handle common Excel format: ["collections\/01JMYENZR4ZXWJV9DKCV6H95KF.png"]
                    if (is_string($state) && str_starts_with(trim($state), '[') && str_contains($state, '\\/')) {
                        try {
                            // Parse JSON and fix slashes
                            $parsed = json_decode($state, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                                $fixed = [];
                                foreach ($parsed as $path) {
                                    if (is_string($path)) {
                                        $fixed[] = str_replace('\/', '/', $path);
                                    }
                                }
                                return $fixed;
                            }
                        } catch (\Exception $e) {
                            // Fall through to other methods
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
                
            ImportColumn::make('media_id')
                ->label('Media')
                ->rules(['nullable', 'integer'])
                ->integer()
                ->castStateUsing(function ($state) {
                    if (blank($state)) {
                        return null;
                    }
                    
                    $mediaId = (int) $state;
                    
                    // Check if the media_id exists
                    if (DB::table('media')->where('id', $mediaId)->exists()) {
                        return $mediaId;
                    }
                    
                    return null;
                }),
                
            ImportColumn::make('parent_id')
                ->label('Parent')
                ->rules(['nullable', 'integer'])
                ->integer()
                ->castStateUsing(function ($state) {
                    if (blank($state)) {
                        return null;
                    }
                    
                    $parentId = (int) $state;
                    
                    // Check if the parent_id exists
                    if (DB::table('collections')->where('id', $parentId)->exists()) {
                        return $parentId;
                    }
                    
                    return null;
                }),
                
            ImportColumn::make('is_visible')
                ->label('Visibility')
                ->boolean()
                ->rules(['nullable'])
                ->castStateUsing(function ($state) {
                    if (blank($state)) {
                        return true; // Default to visible
                    }
                    
                    if (is_bool($state)) {
                        return $state;
                    }
                    
                    if (is_string($state)) {
                        return in_array(
                            strtolower(trim($state)),
                            ['true', '1', 'yes', 'y', 'visible', 'on', 'x'],
                            true
                        );
                    }
                    
                    if (is_numeric($state)) {
                        return (int) $state !== 0;
                    }
                    
                    return true;
                }),
                
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

    public function resolveRecord(): ?Collection
    {
        // If ID is provided, try to find the record by ID first
        if (isset($this->data['id']) && !empty($this->data['id'])) {
            $id = (int) $this->data['id'];
            $existingById = Collection::find($id);
            
            if ($existingById) {
                return $existingById;
            }
        }
        
        // Otherwise, look up by slug
        $slug = isset($this->data['slug']) && !empty($this->data['slug']) 
            ? $this->data['slug'] 
            : Str::slug($this->data['name']);
        
        return Collection::firstOrNew([
            'slug' => $slug,
        ]);
    }

    protected function beforeSave(): void
    {
        // Generate slug if not provided
        if (empty($this->record->slug)) {
            $this->record->slug = Str::slug($this->data['name']);
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your collection import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}