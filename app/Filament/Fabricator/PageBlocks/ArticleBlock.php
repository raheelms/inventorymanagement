<?php

namespace App\Filament\Fabricator\PageBlocks;

use App\Models\Article as ArticleModel;
use App\Models\Category;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use FilamentTiptapEditor\Enums\TiptapOutput;
use FilamentTiptapEditor\TiptapEditor;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class ArticleBlock extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('article')
            ->schema([
                TextInput::make('heading'),

                TiptapEditor::make('content')
                    ->label('Short Description')
                    ->output(TiptapOutput::Json),

                Select::make(name: 'category')
                    ->label('Category')
                    ->options(Category::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->required(),

                Select::make('limit')
                    ->label('Article Limit')
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
            'content' => $data['content'],
            'category' => $data['category'],
            'limit' => $data['limit'],
            'sort_by' => $data['sort_by'],
            'show_load_more' => $data['show_load_more'],
        ];
    }

}