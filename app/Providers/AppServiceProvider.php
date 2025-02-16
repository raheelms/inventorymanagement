<?php

namespace App\Providers;

use Filament\Pages\Dashboard;
use Illuminate\Support\ServiceProvider;
use Z3d0X\FilamentFabricator\Forms\Components\PageBuilder;
use Z3d0X\FilamentFabricator\Resources\PageResource;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        PageBuilder::configureUsing(function (PageBuilder $builder) {
            $builder->collapsible();
            $builder->collapsed();            
        });

        if (class_exists(PageResource::class)) {
            PageResource::navigationGroup('Settings');
            PageResource::navigationSort(1);
            PageResource::navigationIcon('heroicon-o-cube');
        }
    }
}