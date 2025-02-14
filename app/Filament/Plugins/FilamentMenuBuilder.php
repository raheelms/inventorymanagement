<?php

namespace App\Filament\Plugins;

use App\Enums\HeroIcons;
use App\Filament\Inputs\IconPicker;
use App\Models\Category;
use App\Models\Collection;
use Datlechin\FilamentMenuBuilder\FilamentMenuBuilderPlugin;
use Datlechin\FilamentMenuBuilder\MenuPanel\ModelMenuPanel;
use Datlechin\FilamentMenuBuilder\MenuPanel\StaticMenuPanel;
use Filament\Forms\Components\TextInput;

class FilamentMenuBuilder extends FilamentMenuBuilderPlugin
{
    public static function make(): static
    {
        return parent::make()

            ->addLocations([
                'header' => 'Header',
                'footer' => 'Footer',
            ])
            ->addMenuItemFields([
                IconPicker::make('icon'),
                TextInput::make('subtitle')
                    ->string()
                    ->minLength(3)
                    ->maxLength(255)
                    ->nullable(),
            ])
            ->addMenuPanels([
                StaticMenuPanel::make()
                    ->add('Home', url('/'))
                    ->add('Blog', url('/blog')),
                ModelMenuPanel::make()
                    ->model(Category::class)
                    ->collapsible()
                    ->collapsed(true)
                    ->description('add a category')
                    ->icon('heroicon-o-squares-2x2'),
                ModelMenuPanel::make()
                    ->model(Collection::class)
                    ->collapsible()
                    ->collapsed(true)
                    ->description('add a collection')
                    ->icon('heroicon-o-squares-2x2'),
            ]);
    }
}
