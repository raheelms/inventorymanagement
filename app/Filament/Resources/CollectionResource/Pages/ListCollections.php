<?php

namespace App\Filament\Resources\CollectionResource\Pages;

use App\Filament\Exports\CollectionExporter;
use App\Filament\Imports\CollectionImporter;
use App\Filament\Resources\CollectionResource;
use App\Models\Collection;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class ListCollections extends ListRecords
{
    protected static string $resource = CollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(CollectionImporter::class)
                ->label('Import')
                ->icon('heroicon-o-cloud-arrow-up')
                //->outlined()
                ->color('purple-500'),

            ExportAction::make()
                ->exporter(CollectionExporter::class)
                ->label('Export')
                ->icon('heroicon-o-cloud-arrow-down')
                //->outlined()
                ->color('fuchsia-600'),

            Actions\CreateAction::make(),

            Action::make('debugCollection')
                ->label('Debug All Collections')
                ->icon('heroicon-o-bug-ant')
                ->color('warning')
                ->action(function () {
                    // Get all collections (limiting to 20 to avoid memory issues)
                    $collections = Collection::limit(20)->get();
                    
                    if ($collections->isEmpty()) {
                        Notification::make()
                            ->warning()
                            ->title('No collections found to debug.')
                            ->send();
                        return;
                    }
                    
                    $allDebugInfo = [];
                    
                    // Process each collection
                    foreach ($collections as $collection) {
                        // Get raw database values from the table directly
                        $rawRecord = DB::table('collections')->where('id', $collection->id)->first();
                        
                        // Prepare debug information
                        $debugInfo = [
                            'Collection ID' => $collection->id,
                            'Name' => $collection->name,
                            'Model vs Database' => [
                                'Description' => [
                                    'model_type' => gettype($collection->description),
                                    'model_value' => $collection->description,
                                    'db_type' => isset($rawRecord->description) ? gettype($rawRecord->description) : 'null',
                                    'db_value' => $rawRecord->description ?? null,
                                ],
                                'Images' => [
                                    'model_type' => gettype($collection->images),
                                    'model_value' => $collection->images,
                                    'db_type' => isset($rawRecord->images) ? gettype($rawRecord->images) : 'null',
                                    'db_value' => $rawRecord->images ?? null,
                                ],
                                'Media ID' => [
                                    'model_type' => gettype($collection->media_id),
                                    'model_value' => $collection->media_id,
                                    'db_type' => isset($rawRecord->media_id) ? gettype($rawRecord->media_id) : 'null',
                                    'db_value' => $rawRecord->media_id ?? null,
                                ],
                                'Tags' => [
                                    'model_type' => gettype($collection->tags),
                                    'model_value' => $collection->tags,
                                    'db_type' => isset($rawRecord->tags) ? gettype($rawRecord->tags) : 'null',
                                    'db_value' => $rawRecord->tags ?? null,
                                ],
                                'Data' => [
                                    'model_type' => gettype($collection->data),
                                    'model_value' => $collection->data,
                                    'db_type' => isset($rawRecord->data) ? gettype($rawRecord->data) : 'null',
                                    'db_value' => $rawRecord->data ?? null,
                                ],
                            ],
                        ];
                        
                        $allDebugInfo[$collection->id] = $debugInfo;
                    }
                    
                    // Add global model configuration
                    $allDebugInfo['Global Info'] = [
                        'Model Configuration' => [
                            'casts' => $collections->first()->getCasts(),
                            'fillable' => $collections->first()->getFillable(),
                        ],
                        'Database Schema' => [
                            'table_columns' => collect(DB::select('SHOW COLUMNS FROM collections'))->pluck('Type', 'Field')->toArray(),
                        ],
                    ];
                    
                    // Dump the debug information
                    dd($allDebugInfo);
                })
                ->requiresConfirmation()
                ->modalHeading('Debug Collection Data')
                ->modalDescription('This will display detailed debug information for all collections (limited to 20).')
                ->modalSubmitActionLabel('Run Debug'),
                
            Action::make('debugSingleCollection')
                ->label('Debug Collection #2828')
                ->icon('heroicon-o-eye')
                ->color('success')
                ->action(function () {
                    // Get specific collection by ID
                    $collection = Collection::find(2828); // Change this ID as needed
                    
                    if (!$collection) {
                        Notification::make()
                            ->warning()
                            ->title('Collection #28 not found.')
                            ->send();
                        return;
                    }
                    
                    // Get raw database values from the table directly
                    $rawRecord = DB::table('collections')->where('id', $collection->id)->first();
                    
                    // Convert objects to arrays for better inspection
                    $rawRecordArray = json_decode(json_encode($rawRecord), true);
                    
                    // Prepare debug information
                    $debugInfo = [
                        'Collection Details' => [
                            'ID' => $collection->id,
                            'Name' => $collection->name,
                            'Slug' => $collection->slug,
                        ],
                        'Raw Database Record' => $rawRecordArray,
                        'Model with Casts' => $collection->toArray(),
                        'JSON Fields' => [
                            'Description' => [
                                'DB Value (raw string)' => $rawRecord->description,
                                'Model Value (after cast)' => $collection->description,
                            ],
                            'Images' => [
                                'DB Value (raw string)' => $rawRecord->images,
                                'Model Value (after cast)' => $collection->images,
                            ],
                            'Tags' => [
                                'DB Value (raw string)' => $rawRecord->tags,
                                'Model Value (after cast)' => $collection->tags,
                            ],
                            'Data' => [
                                'DB Value (raw string)' => $rawRecord->data,
                                'Model Value (after cast)' => $collection->data,
                            ],
                        ],
                        'Export Values' => [
                            'Description' => json_encode($collection->description),
                            'Images' => is_array($collection->images) ? implode('|', $collection->images) : $collection->images,
                            'Tags' => is_array($collection->tags) ? implode('|', $collection->tags) : $collection->tags,
                            'Data' => $rawRecord->data, // Use raw DB value
                        ],
                    ];
                    
                    // Dump the debug information
                    dd($debugInfo);
                })
                ->requiresConfirmation()
                ->modalHeading('Debug Collection #2828')
                ->modalDescription('This will display detailed debug information for collection with ID 2828.')
                ->modalSubmitActionLabel('Run Debug'),
        ];
    }
}