<?php

namespace App\Filament\Fabricator\PageBlocks;

use App\Models\Product as ProductModel;
use App\Models\Collection;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use FilamentTiptapEditor\Enums\TiptapOutput;
use FilamentTiptapEditor\TiptapEditor;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class ProductBlock extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('product')
            ->schema([
                TextInput::make('heading'),

                TiptapEditor::make('description')
                    ->label('Short Description')
                    ->output(TiptapOutput::Json),
    
                Select::make(name: 'collection')
                    ->label('Collection')
                    ->options(Collection::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->required(),
                
                Select::make('limit')
                    ->label('Product Limit')
                    ->required()
                    ->options([
                        '3' => '3',
                        '6' => '6',
                        '9' => '9',
                        '12' => '12',
                ])
                ->searchable(),

                Select::make('sort_by')
                    ->label('Sort By')
                    ->required()
                    ->options([
                        'created_at' => 'Created At',
                        'updated_at' => 'Updated At',
                        'popular' => 'Most Popular',
                    ])
                    ->searchable(),

                Select::make('show_load_more')
                    ->label('Show Load More Button')
                    ->required()
                    ->options([
                        'true' => 'Yes',
                        'false' => 'No',
                    ]),

                ]);
    }

    public static function mutateData(array $data): array
    {
        return [
            'heading' => $data['heading'],
            'description' => $data['description'],            
            'collection' => $data['collection'],
            'limit' => $data['limit'],
            'sort_by' => $data['sort_by'],
            'show_load_more' => $data['show_load_more'],
        ];
    }
}