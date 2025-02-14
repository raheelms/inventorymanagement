<?php

namespace App\Filament\Plugins;

use Awcodes\Curator\CuratorPlugin;

class Curator extends CuratorPlugin
{
    public static function make(): static
    {
        return parent::make()
            ->label('Media')
            ->pluralLabel('Media')
            ->navigationIcon('heroicon-o-photo')
            ->navigationGroup('Settings')
            ->navigationSort(2)
            ->navigationCountBadge()
            ->registerNavigation(true)
            ->defaultListView('grid' || 'list');
    }


}
