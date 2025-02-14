<?php

namespace App\Filament\Fabricator\PageBlocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class CarouselBlock extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('carousel')
            ->schema([
                Repeater::make('images')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Image')
                            ->image()
                            ->imageEditor(),
                        TextInput::make('title')->label('Title'),
                        Textarea::make('description')->label('Description'),
                        TextInput::make('url')->label('URL'),
                        TextInput::make('urlText')->label('URL Text'),
                    ]),
            ]);
    }

    public static function mutateData(array $data): array
    {        
        return $data;
    }

}
