<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\Enums\TiptapOutput;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use RalphJSmit\Filament\SEO\SEO;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationGroup = 'Content Management';   
    // protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $activeNavigationIcon = 'heroicon-o-check-badge'; //CHANGE THE ICON IN FRONT OF THE MENU ITEM AS THE ACTIVE MENU ITEM- comment by raheel
    protected static ?string $navigationLabel = 'Categories'; //CHANGE THE LABEL OF THE MENU ITEM- comment by raheel
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
            return $form
            ->schema([
                Forms\Components\Tabs::make()
                ->schema([
                    Forms\Components\Tabs\Tab::make('Content')
                    ->schema([                       
                        Forms\Components\Section::make('Category Details')
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
                                ->unique(table: Category::class, column: 'slug', ignoreRecord: true ),
        
                            Forms\Components\ColorPicker::make(name: 'text_color'),
        
                            Forms\Components\ColorPicker::make(name: 'bg_color'),
        
                            TiptapEditor::make('description')
                                ->output(TiptapOutput::Json)
                                ->columnSpanFull(),
                            
                            Forms\Components\Hidden::make('user_id')
                                ->dehydrateStateUsing(fn ($state) => Auth::user()->id),
        
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
                                ->helperText(text: 'Enable or disable category visibility')
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

                                Forms\Components\Toggle::make(name: 'is_tag')
                                    ->label(label: 'Is Tag')
                                    ->default(state: false),
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
                        ])->columnSpan(1)->columns(1),
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Media')
                    ->schema([
                        Forms\Components\Section::make('Manage Media')
                        ->collapsible()
                        ->collapsed(false)
                        ->description('')
                        ->schema([
                            Forms\Components\FileUpload::make(name: 'images')
                                ->label(label: 'Images')
                                ->panelLayout('grid')
                                ->disk('public')
                                ->directory(directory: 'categories')
                                ->multiple()
                                ->reorderable(),

                            CuratorPicker::make('media_id')
                                ->label('Media ID')
                                ->helperText(text: 'Add Media')
                                ->color('primary')
                                ->disk('public')
                                ->directory(directory: 'categories'),

                        ])->columnSpan(2)->columns(2),
                    ])->columns()
                ])
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make(name: 'images')
                    ->label(label: 'Image')
                    ->width(40)
                    ->height(40)
                    ->limit(1)
                    ->defaultImageUrl('/images/default_image.png'),

                Tables\Columns\TextColumn::make(name: 'id')
                    ->label(label: 'ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make(name: 'name')
                    ->label(label: 'Category Name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                
                Tables\Columns\TextColumn::make(name: 'parent.name')
                    ->label(label: 'Parent')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make(name: 'tags')
                    ->label(label: 'Tags')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                
                Tables\Columns\IconColumn::make(name: 'is_visible')
                    ->label(label: 'Visibility')
                    ->sortable()             
                    ->boolean()
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
                    ->trueLabel('Only Visible Categories')
                    ->falseLabel('Only Hidden Categories')
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
