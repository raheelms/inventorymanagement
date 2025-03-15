<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CollectionResource\Pages;
use App\Filament\Resources\CollectionResource\RelationManagers;
use App\Models\Collection;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\Enums\TiptapOutput;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RalphJSmit\Filament\SEO\SEO;

class CollectionResource extends Resource
{
    protected static ?string $model = Collection::class;
    protected static ?string $navigationGroup = 'Store';
    // protected static ?string $navigationIcon = 'heroicon-o-square-2-stack';
    protected static ?string $activeNavigationIcon = 'heroicon-o-check-badge'; //CHANGE THE ICON IN FRONT OF THE MENU ITEM AS THE ACTIVE MENU ITEM- comment by raheel
    protected static ?string $navigationLabel = 'Collections'; //CHANGE THE LABEL OF THE MENU ITEM- comment by raheel
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->schema([
                        Forms\Components\Tabs\Tab::make('Content')
                            ->schema([                       
                                Forms\Components\Section::make('Collection Details')
                                ->collapsible()
                                ->collapsed(false)
                                ->description('')
                                ->schema([
                                    Forms\Components\TextInput::make(name: 'name')
                                        ->required()
                                        ->maxlength(255)
                                        ->live(onBlur: true)
                                        ->unique(ignoreRecord: true)
                                        ->afterStateUpdated(function(string $operation, $state, Forms\Set $set)
                                        {                                        
                                            if($operation !== 'create') {
                                                return;
                                            }
                                            $set('slug', Str::slug($state) );
                                        }),
                
                                    Forms\Components\TextInput::make(name: 'slug')
                                        ->disabled()
                                        ->dehydrated()
                                        ->required()
                                        ->maxlength(255)
                                        ->unique(table: Collection::class, column: 'slug', ignoreRecord: true ),
                
                                    TiptapEditor::make('description')
                                        ->output(TiptapOutput::Json)
                                        ->columnSpanFull(),
                                    
                                    Forms\Components\Hidden::make('user_id')
                                        ->dehydrateStateUsing(fn ($state) => Auth::user()->id),

                                    Forms\Components\KeyValue::make('data')
                                        ->label(__('Additional Details'))
                                        ->columnSpanFull(),

                                    Forms\Components\FileUpload::make(name: 'images')
                                        ->label(label: 'Images')
                                        ->panelLayout('grid')
                                        ->disk('public')
                                        ->directory(directory: 'collections')
                                        ->multiple()
                                        ->reorderable(),

                                    CuratorPicker::make('media_id')
                                        ->label('Media ID')
                                        ->helperText(text: 'Add Media')
                                        ->color('primary')
                                        ->disk('public')
                                        ->directory(directory: 'collections'),
                
                                ])->columnSpan(2)->columns(2),
                
                                Forms\Components\Group::make()
                                ->schema([
                                    Forms\Components\Section::make('Status')
                                    ->collapsible()
                                    ->collapsed(false)
                                    ->description('')
                                    ->schema([
                                        Forms\Components\Toggle::make(name: 'is_visible')
                                        ->label(label: 'Visibility')
                                        ->helperText(text: 'Enable or disable collection visibility')
                                        ->default(state: true),
            
                                        Forms\Components\Select::make(name: 'parent_id')
                                            ->relationship(name: 'parent', titleAttribute: 'name')
                                    ]),
                
                                    Forms\Components\Section::make('Meta')
                                    ->collapsible()
                                    ->collapsed(false)
                                    ->description('')
                                    ->schema([
                                        Forms\Components\TagsInput::make(name: 'tags'),

                                    ]),

                                ])->columnSpan(1),

                            ])->columns(3),

                        Forms\Components\Tabs\Tab::make('SEO')
                            ->schema([
                                Forms\Components\Section::make('Manage SEO')
                                ->collapsible()
                                ->collapsed(false)
                                ->description('')
                                ->schema([
                                    SEO::make(),
                                ])->columnSpan(2)->columns(2),
                            ])->columns(1),
                    ])
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make(name: 'images')
                    ->label(label: 'Image')
                    ->width(60)
                    ->height(60)
                    ->limit(1)
                    ->defaultImageUrl('/images/default_image.png'),

                Tables\Columns\TextColumn::make(name: 'id')
                    ->label(label: 'ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make(name: 'name')
                    ->label(label: 'Collection Name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                
                Tables\Columns\TextColumn::make(name: 'parent.name')
                    ->label(label: 'Parent')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\IconColumn::make(name: 'is_visible')
                    ->label(label: 'Visibility')
                    ->sortable()             
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make(name: 'tags')
                    ->label(label: 'Tags')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make(name: 'created_at')                
                    ->label(label: 'Created Date')    
                    ->sortable()
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make(name: 'updated_at')                
                    ->label(label: 'Updated Date')    
                    ->sortable()
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make(name: 'is_visible')
                    ->label(label: 'Visibility')
                    ->boolean()
                    ->trueLabel('Only Visible Collections')
                    ->falseLabel('Only Hidden Collections')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\ActionGroup::make([                    
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])     
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCollections::route('/'),
            'create' => Pages\CreateCollection::route('/create'),
            'edit' => Pages\EditCollection::route('/{record}/edit'),
        ];
    }
}
