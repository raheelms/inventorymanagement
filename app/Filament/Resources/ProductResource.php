<?php

namespace App\Filament\Resources;

use App\Enums\ProductStatus;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use FilamentTiptapEditor\Enums\TiptapOutput;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use RalphJSmit\Filament\SEO\SEO;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationGroup = 'Store';
    // protected static ?string $navigationIcon = 'heroicon-o-tag';    
    protected static ?string $activeNavigationIcon = 'heroicon-o-check-badge'; //CHANGE THE ICON IN FRONT OF THE MENU ITEM AS THE ACTIVE MENU ITEM- comment by raheel
    protected static ?string $navigationLabel = 'Products'; //CHANGE THE LABEL OF THE MENU ITEM- comment by raheel
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->schema([
                        Forms\Components\Tabs\Tab::make('Content')
                            ->schema([
                                Forms\Components\Group::make()
                                    ->schema([
                                        // Manage Products Section
                                        Forms\Components\Section::make('Product Details')
                                            ->collapsible()
                                            ->collapsed(false)
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->maxlength(255)
                                                    ->live(onBlur: true)
                                                    ->unique(ignoreRecord: true)
                                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                                        if ($operation === 'create') {
                                                            $set('slug', Str::slug($state));
                                                        }
                                                    }),
                
                                                Forms\Components\TextInput::make('slug')
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->required()
                                                    ->maxlength(255)
                                                    ->unique(Product::class, 'slug', ignoreRecord: true),

                                                Forms\Components\Select::make('collections')
                                                    ->preload()
                                                    ->multiple()
                                                    ->relationship('collections', 'name')
                                                    ->required()
                                                    ->searchable(),

                                                TiptapEditor::make('description')
                                                    ->output(TiptapOutput::Json)
                                                    ->columnSpanFull(),
                
                                                // User ID (Hidden)
                                                Forms\Components\Hidden::make('user_id')
                                                    ->dehydrateStateUsing(fn () => Auth::id()),
                                            ])
                                            ->columns(3)
                                            ->columnSpan(2),

                                        // Media
                                        Forms\Components\Section::make('Media')
                                            ->collapsible()
                                            ->collapsed(false)
                                            ->schema([
                                                Forms\Components\FileUpload::make(name: 'images')
                                                    ->label(__('Images'))
                                                    ->panelLayout('grid')
                                                    ->disk('public')
                                                    ->directory(directory: 'images')
                                                    ->multiple()
                                                    ->reorderable()
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(1)
                                            ->columnSpan(2),
                
                                        // Inventory & Pricing Section
                                        Forms\Components\Section::make('Inventory & Pricing')
                                            ->collapsible()
                                            ->collapsed(false)
                                            ->schema([
                                                Forms\Components\TextInput::make(name: 'sku')
                                                    ->label(__('SKU'))
                                                    ->unique(ignoreRecord: true)
                                                    ->prefix('SKU'),

                                                Forms\Components\TextInput::make(name: 'stock')
                                                    ->label(__('Quantity'))
                                                    ->numeric()
                                                    ->default(1)
                                                    ->minValue(value: 1)
                                                    ->maxValue(value: 10000)
                                                    ->prefix('QTY'),

                                                Forms\Components\TextInput::make('price')
                                                    ->numeric()
                                                    ->nullable()  
                                                    ->rules(['regex: /^\d{1,6}(\.\d{0,2})?$/'])
                                                    ->prefix('EUR'),
                                                    
                                                Forms\Components\TextInput::make('taxes')
                                                    ->type('number')  // Ensures it behaves as a numeric field
                                                    ->default(21)
                                                    ->nullable()
                                                    ->step(0.01)      // Allows decimal input
                                                    ->prefix('VAT'),
                                        
                                                Forms\Components\TextInput::make('discount_price')
                                                    ->numeric()
                                                    ->default(21)
                                                    ->rules(['regex: /^\d{1,6}(\.\d{0,2})?$/'])
                                                    ->prefix('EUR'),

                                                Forms\Components\DatePicker::make(name: 'discount_to')
                                                    ->label(label: 'Discount Till')
                                                    ->default(now()),

                                                Forms\Components\TextInput::make(name: 'safety_stock')
                                                    ->label(__('Safety Stock'))
                                                    ->numeric()
                                                    ->default(0)
                                                    ->minValue(value: 0)
                                                    ->maxValue(value: 2000)
                                                    ->prefix('QTY'),
                                            ])
                                            ->columns(4)
                                            ->columnSpan(2),

                                        // Additional Details
                                        Forms\Components\Section::make('Additional Details')
                                            ->collapsible()
                                            ->collapsed(false)
                                            ->schema([
                                                Forms\Components\KeyValue::make('data')
                                                ->label(__('Additional Details'))
                                                ->columnSpanFull(),
                                            ])
                                            ->columns(1)
                                            ->columnSpan(2),

                                    ])->columnSpan(2),

                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Section::make('Status')
                                            ->collapsible()
                                            ->collapsed(false)
                                            ->schema([
                                                Forms\Components\DatePicker::make(name: 'published_at')
                                                    ->label(__('Published At'))
                                                    ->default(now()),

                                                Forms\Components\Select::make('status')
                                                    ->label(__('Product Availability'))
                                                    ->options(ProductStatus::options())
                                                    ->default(ProductStatus::DRAFT->value),

                                                Forms\Components\Toggle::make('is_visible')
                                                    ->label(__('Visibility'))
                                                    ->helperText('Enable or disable product visibility')
                                                    ->default(true),

                                                Forms\Components\Toggle::make('is_featured')
                                                    ->label(__('Featured'))
                                                    ->helperText('Enable or disable')
                                                    ->default(false),

                                                Forms\Components\Toggle::make('in_stock')
                                                    ->label(__('Stock'))
                                                    ->helperText('Enable or disable')
                                                    ->default(true),
                                                    
                                                Forms\Components\Toggle::make('on_sale')
                                                    ->label(__('Sale'))
                                                    ->helperText('Enable or disable')
                                                    ->default(false),
                                            ]),
                
                                        Forms\Components\Section::make('Meta')
                                            ->collapsible()
                                            ->collapsed(false)
                                            ->schema([
                                                Forms\Components\TagsInput::make('tags'),
                                            ]),
                                            
                                    ])->columnSpan(1),

                            ])->columns(3),
            
                        // SEO Tab
                        Forms\Components\Tabs\Tab::make('SEO')
                            ->schema([
                                Forms\Components\Section::make('Manage SEO')
                                    ->collapsible()
                                    ->collapsed(false)
                                    ->description('')
                                    ->schema([
                                        SEO::make(),
                                    ])->columnSpan(2)->columns(2),
                            ])->columns(2),
                    ]),
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
                ->defaultImageUrl('/images/default_image.png')
                ->toggleable(isToggledHiddenByDefault: false),
                
            Tables\Columns\TextColumn::make(name: 'id')
                ->label(label: 'ID')
                ->sortable()
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make(name: 'name')
                ->label(label: 'Name')
                ->searchable()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: false),

            Tables\Columns\TextColumn::make(name: 'slug')
                ->label(label: 'Slug')
                ->searchable()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: false),
            
            Tables\Columns\TextColumn::make( name: 'collections.name')
                ->label(label: 'Collection Name')             
                ->searchable()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: false),
            
            Tables\Columns\TextColumn::make( name: 'price')
                ->label(label: 'Price')
                ->money('EUR')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: false),

            Tables\Columns\TextColumn::make(name: 'tags')
                ->label(label: 'Tags')
                ->searchable()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            
            SelectColumn::make('status')
                ->label('Status')
                ->options(ProductStatus::options())
                ->default(ProductStatus::DRAFT->value)
                ->toggleable(isToggledHiddenByDefault: false),

            Tables\Columns\IconColumn::make('in_stock')
                ->label(label: 'In Stock')
                ->sortable()
                ->boolean()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\IconColumn::make( name: 'is_visible')
                ->label(label: 'Visibility')
                ->sortable()
                ->boolean()
                ->toggleable(isToggledHiddenByDefault: false),
            
            Tables\Columns\IconColumn::make('is_featured')
                ->label(label: 'Featured')
                ->sortable() 
                ->boolean()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\IconColumn::make('on_sale')
                ->label(label: 'On Sale')
                ->sortable()
                ->boolean()
                ->toggleable(isToggledHiddenByDefault: true),
            
            Tables\Columns\TextColumn::make('published_at')
                ->date()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('collections')
                ->searchable()
                ->multiple()
                ->relationship('collections', 'name'),

            Tables\Filters\SelectFilter::make('status')
                ->options(ProductStatus::options()),

            Tables\Filters\TrashedFilter::make()
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
