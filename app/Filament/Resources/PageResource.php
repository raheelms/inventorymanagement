<?php

namespace App\Filament\Resources;

use Z3d0X\FilamentFabricator\Resources\PageResource as BasePageResource;

class PageResource extends BasePageResource
{
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $activeNavigationIcon = 'heroicon-o-check-badge'; //CHANGE THE ICON IN FRONT OF THE MENU ITEM AS THE ACTIVE MENU ITEM- comment by raheel
    protected static ?string $navigationLabel = 'Pages'; //CHANGE THE LABEL OF THE MENU ITEM- comment by raheel
    protected static ?int $navigationSort = 3;
}