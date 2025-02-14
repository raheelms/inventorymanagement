<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryResource\Pages;
use App\Filament\Resources\InventoryResource\RelationManagers;
use App\Filament\Resources\InventoryResource\RelationManagers\InvoiceRecordsRelationManager;
use App\Models\Collection;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Provider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;
    protected static ?string $navigationGroup = 'Inventory Management';
    // protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $activeNavigationIcon = 'heroicon-o-check-badge'; //CHANGE THE ICON IN FRONT OF THE MENU ITEM AS THE ACTIVE MENU ITEM- comment by raheel
    protected static ?string $navigationLabel = 'Inventories'; //CHANGE THE LABEL OF THE MENU ITEM- comment by raheel
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make('Inventory Information')
                ->columns(3)  // Changed to 3 columns
                ->schema([
                    Forms\Components\Group::make()
                        ->columnSpan(2)  // Takes 2 columns
                        ->columns(2)     // Internal layout is 2 columns
                        ->schema([
                            Forms\Components\Section::make('Basic Details')
                                ->collapsible()
                                ->collapsed(false)
                                ->columns(3)
                                ->schema([
                                    Forms\Components\TextInput::make('invoice_number')
                                        ->label(__('Invoice Number'))
                                        ->default(function () {
                                            $invoiceNumber = (new Inventory())->generateInvoiceNumber();
                                            return strval($invoiceNumber);
                                        })
                                        ->disabled()
                                        ->dehydrated()
                                        ->formatStateUsing(function ($state) {
                                            return is_object($state) ? strval($state) : $state;
                                        }),
    
                                        Forms\Components\Select::make('providers')
                                        ->label(__('Provider Name'))
                                        ->options(Provider::pluck('name', 'id'))
                                        ->live()
                                        ->afterStateUpdated(function (Forms\Set $set, $state) {
                                            if ($state) {
                                                $provider = Provider::find($state);
                                                if ($provider) {
                                                    $set('email', $provider->email ?? '');
                                                }
                                            }
                                        })
                                        ->createOptionAction(
                                            fn (Forms\Components\Actions\Action $action) => $action
                                                ->slideOver()
                                                ->modalWidth('xl')
                                        )
                                        ->createOptionForm(function(){
                                            return ProviderResource::getProviderFormSchema();
                                        })
                                        ->relationship('providers', 'name')
                                        ->required(),
    
                                    Forms\Components\DateTimePicker::make('purchase_date')
                                        ->label(__('Order Date'))
                                        ->format('Y-m-d H:i') 
                                        ->default(now())  // Use the current time
                                        ->required(),

                                    Forms\Components\MarkdownEditor::make('notes')
                                        ->label(__('Notes'))
                                        ->columnSpanFull(),
    
                                    Forms\Components\Hidden::make('user_id')
                                        ->default(Auth::id()),
                                ]),
    
                            Forms\Components\Section::make('Product Details')
                                ->collapsible()
                                ->collapsed(false)
                                ->columns(2)
                                ->schema([
                                    Forms\Components\Repeater::make('inventory_items')
                                        ->label(__('Inventory Items'))
                                        ->columns(5)
                                        ->columnSpanFull()
                                        ->relationship()
                                        ->schema([
                                            Forms\Components\Select::make('product_id')
                                                ->label(__('Product Name'))
                                                ->options(Product::pluck('name', 'id')->toArray())
                                                ->searchable()
                                                ->required()
                                                ->columnSpan(2)
                                                ->createOptionAction(
                                                    fn (Forms\Components\Actions\Action $action) => $action
                                                        ->slideOver()
                                                        ->modalWidth('xl')
                                                )
                                                ->createOptionUsing(function (array $data) {
                                                    $product = Product::create([
                                                        'name' => $data['name'],
                                                        'slug' => $data['slug'],
                                                        'sku' => $data['sku'],
                                                        'price' => $data['price'],
                                                        'published_at' => $data['published_at'],
                                                    ]);
                                                    
                                                    $product->collections()->attach($data['collections']);
                                                    
                                                    return $product->id;
                                                })
                                                ->createOptionForm([
                                                    Forms\Components\TextInput::make('name')
                                                        ->required()
                                                        ->maxLength(255)
                                                        ->live(onBlur: true)
                                                        ->unique(ignoreRecord: true)
                                                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                            if (! $state) {
                                                                return;
                                                            }
                                                            $set('slug', Str::slug($state));
                                                        }),
                                            
                                                    Forms\Components\TextInput::make('slug')
                                                        ->disabled()
                                                        ->dehydrated()
                                                        ->required()
                                                        ->maxLength(255)
                                                        ->unique(Product::class, 'slug', ignoreRecord: true),
                                            
                                                    Forms\Components\TextInput::make('sku')
                                                        ->required()
                                                        ->maxLength(255)
                                                        ->unique(Product::class, 'sku', ignoreRecord: true),
                                            
                                                    Forms\Components\Select::make('collections')
                                                        ->preload()
                                                        ->multiple()
                                                        ->options(Collection::pluck('name', 'id'))
                                                        ->required()
                                                        ->searchable()
                                                        ->createOptionUsing(fn (string $name) => Collection::create(['name' => $name])->id),
                                            
                                                    Forms\Components\TextInput::make('price')
                                                        ->numeric()
                                                        ->nullable()
                                                        ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                                        ->prefix('EUR'),

                                                    Forms\Components\DatePicker::make(name: 'published_at')
                                                        ->label(__('Published At'))
                                                        ->default(now()),
                                                ]),
    
                                            Forms\Components\TextInput::make('quantity')
                                                ->label(__('Quantity'))
                                                ->numeric()
                                                ->default(1)
                                                ->minValue(1)
                                                ->required()
                                                ->reactive()
                                                ->debounce(1000)  // 1 second
                                                ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                                    self::updateFormData($get, $set);
                                                })
                                                ->columnSpan(1),
                                            
                                            Forms\Components\TextInput::make('price')
                                                ->label(__('Unit Price'))
                                                ->numeric()
                                                ->required()
                                                ->reactive()
                                                ->debounce(1000)  // 1 second
                                                ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                                    self::updateFormData($get, $set);
                                                })
                                                ->columnSpan(1),
    
                                            Forms\Components\TextInput::make('total')
                                                ->label(__('Total'))
                                                ->numeric()
                                                ->required()
                                                ->disabled()
                                                ->dehydrated()
                                                ->columnSpan(1),
                                        ])
                                ]),

                            Forms\Components\Section::make('Total Details')
                                ->collapsible()
                                ->collapsed(false)
                                ->schema([
                                    Forms\Components\TextInput::make('total_amount')
                                        ->label(__('Sub Total'))
                                        ->numeric()
                                        ->required()
                                        ->disabled()
                                        ->dehydrated()
                                        ->columnSpan(1),

                                    Forms\Components\TextInput::make('shipping_amount')
                                        ->label(__('Shipping Amount'))
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->debounce(1000)  // 1 second
                                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                            self::updateFormData($get, $set);
                                        })
                                        ->columnSpan(1),

                                    Forms\Components\TextInput::make('discount_amount')
                                        ->label(__('Discount Amount'))
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->debounce(1000)  // 1 second
                                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                            self::updateFormData($get, $set);
                                        })
                                        ->columnSpan(1),

                                    Forms\Components\TextInput::make('grand_total')
                                        ->label(__('Grand Total'))
                                        ->numeric()
                                        ->disabled()
                                        ->dehydrated()
                                        ->columnSpan(1),
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
                        ]),
    
                    Forms\Components\Group::make()
                        ->columnSpan(1) // Takes 1 column
                        ->columns(1) // Internal layout is 1 column
                        ->schema([
                            Forms\Components\Section::make('Order Information')
                                ->collapsible()
                                ->collapsed(false)
                                ->schema([
                                    Forms\Components\ToggleButtons::make('status')
                                        ->label(__('Order Status'))
                                        ->inline()
                                        ->default('in transit')
                                        ->options([
                                            'in transit' => 'In Transit',
                                            'completed' => 'Completed',
                                        ])
                                        ->colors([
                                            'in transit' => 'info',
                                            'completed' => 'success',
                                        ])
                                        ->icons([
                                            'in transit' => 'heroicon-m-truck',
                                            'completed' => 'heroicon-m-check-badge',
                                        ]),

                                        Forms\Components\Select::make('payment_method')
                                            ->label(__('Payment Method'))
                                            ->default('ideal')
                                            ->options([
                                                'paypal' => 'Paypal',
                                                'ideal' => 'Ideal',
                                                'klarna' => 'Klarna',
                                                'alibaba' => 'Alibaba',
                                                'stripe' => 'Stripe',
                                                'cod' => 'Cash on delivery',
                                            ]),
        
                                        Forms\Components\Select::make('payment_status')
                                            ->label('Payment Status')
                                            ->options([
                                                'paid' => 'Paid',
                                                'unpaid' => 'Unpaid',
                                            ])
                                            ->default('unpaid'),
        
                                        Forms\Components\Select::make('currency')
                                            ->label(__('Currency'))
                                            ->default('eur')
                                            ->options([
                                                'eur' => 'EUR',
                                                'usd' => 'USD',
                                                'gbp' => 'GBP',
                                            ]),

                                        Forms\Components\Select::make('shipping_method')
                                            ->label(__('Shipping Method'))
                                            ->options([
                                                'postnl' => 'PostNL',
                                                'dhl' => 'DHL',
                                                'fedex' => 'Fedex',
                                                'ups' => 'UPS',
                                                'dpd' => 'DPD',
                                                'gls' => 'GLS',
                                                'other' => 'Other',
                                            ]),
                                ]),
                        ])
                ])
        ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
        ->query(Inventory::query()->with('providers'))
        ->columns([
                Tables\Columns\TextColumn::make(name: 'invoice_number')  // Changed from order_id to invoice_number
                    ->label(__('Order Number'))
                    ->searchable()
                    ->sortable(),           
                    
                Tables\Columns\TextColumn::make('providers.name')  // Changed from provider.name
                    ->label(__('Customer Name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('providers.email')  // Changed from provider.email
                    ->label(__('Email'))
                    ->searchable()
                    ->tooltip('Email Address')
                    ->toggleable(isToggledHiddenByDefault: false),
                
                Tables\Columns\TextColumn::make(name: 'grand_total')
                    ->label(__('Grand Total'))
                    ->money('EUR')
                    ->sortable()
                    ->searchable()
                    ->summarize(
                        Tables\Columns\Summarizers\Sum::make()
                            ->formatStateUsing(fn($state) => Number::currency($state, 'EUR'))
                    ),

                Tables\Columns\SelectColumn::make(name: 'status')
                    ->label(__('Order Status'))
                    ->options([
                        'in transit' => 'In Transit',
                        'completed' => 'Completed',
                    ])
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make(name: 'payment_method')
                    ->label(__('Payment Method'))
                    ->badge()
                    ->color(fn (string $state):string => match($state)
                    {
                        'stripe' => 'success',
                        'cod' => 'info',
                        'paypal' => 'primary',
                        'ideal' => 'warning',
                        'klarna' => 'danger',
                        'alibaba' => 'gray',
                        default => 'secondary'
                    })
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),              

                Tables\Columns\SelectColumn::make(name: 'payment_status')
                    ->label(__('Payment Status'))
                    ->options([
                        'unpaid' => 'Unpaid',
                        'paid' => 'Paid',
                    ])
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\SelectColumn::make(name: 'shipping_method')
                    ->label(__('Shipping Method'))
                    ->options([
                        'postnl' => 'PostNL',
                        'dhl' => 'DHL',
                        'fedex' => 'Fedex',
                        'ups' => 'UPS',
                        'dpd' => 'DPD',
                        'gls' => 'GLS',
                        'other' => 'Other',
                    ])
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make(name: 'created_at')
                    ->label(__('Order Date'))
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make(name: 'updated_at')
                    ->label(__('Updated At'))
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->slideOver()
                    ->modalWidth('5xl'),
                Tables\Actions\Action::make('view_invoice')
                    ->label(__('Invoice'))
                    ->icon('heroicon-o-document')
                    ->url(fn($record) => self::getUrl('invoice', ['record' => $record->id])),

                Tables\Actions\ActionGroup::make([                    
                    Tables\Actions\EditAction::make(),
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
            InvoiceRecordsRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventories::route('/'),
            'create' => Pages\CreateInventory::route('/create'),
            'edit' => Pages\EditInventory::route('/{record}/edit'),
            'invoice' => Pages\Invoice::route('{record}/invoice'),
        ];
    }

    /**
     * Updates form data calculations for inventory items, shipping, and discount
     * 
     * This function handles two types of updates:
     * 1. Inventory item updates: When quantity or price of an item changes
     * 2. Shipping/Discount updates: When shipping amount or discount amount changes
     * 
     * For inventory items, we need to:
     * - Calculate the individual item total (quantity * price)
     * - Sum up all items to get subtotal
     * - Apply shipping and discount to get grand total
     * 
     * For shipping/discount updates, we:
     * - Keep the existing subtotal
     * - Apply new shipping/discount amounts to get grand total
     * 
     * @param Forms\Get $get Getter for accessing form values
     * @param Forms\Set $set Setter for updating form values
     */
    public static function updateFormData(Forms\Get $get, Forms\Set $set)
    {
        // Get both direct form data and parent form data
        // - Direct form data ($formData) is used for shipping/discount updates
        // - Parent form data ($parentData) is used for inventory item updates
        $formData = $get('');
        $parentData = $get('../../');
        
        // Check if we're updating an inventory item by looking for quantity or price fields
        // This helps us determine which data and update paths to use
        $isInventoryItemUpdate = isset($formData['quantity']) || isset($formData['price']);
        
        // Choose which data to work with based on update type
        // - For inventory items: use parent data to get full form context
        // - For shipping/discount: use direct form data
        $workingData = $isInventoryItemUpdate ? $parentData : $formData;
        
        // Step 1: If this is an inventory item update, calculate its total
        if ($isInventoryItemUpdate) {
            $quantity = floatval($formData['quantity'] ?? 0);
            $price = floatval($formData['price'] ?? 0);
            $itemTotal = $quantity * $price;
            $set('total', $itemTotal);
        }

        // Step 2: Calculate subtotal from all inventory items
        // Loop through all items and sum up (quantity * price) for each
        $subtotal = 0;
        if ($items = ($workingData['inventory_items'] ?? [])) {
            foreach ($items as $item) {
                $subtotal += floatval($item['quantity'] ?? 0) * floatval($item['price'] ?? 0);
            }
        }
        
        // Step 3: Apply shipping and discount to get grand total
        // - Add shipping amount to subtotal
        // - Subtract discount amount from result
        $shipping = floatval($workingData['shipping_amount'] ?? 0);
        $discount = floatval($workingData['discount_amount'] ?? 0);
        $grandTotal = $subtotal + $shipping - $discount;

        // Step 4: Update form fields with new totals
        // Use different paths depending on update type:
        // - For inventory items: use parent path ('../../')
        // - For shipping/discount: use direct path
        if ($isInventoryItemUpdate) {
            $set('../../total_amount', $subtotal);
            $set('../../grand_total', $grandTotal);
        } else {
            $set('total_amount', $subtotal);
            $set('grand_total', $grandTotal);
        }

        // Log calculation details for debugging
        Log::info('Calculation Summary', [
            'update_type' => $isInventoryItemUpdate ? 'inventory_item' : 'shipping_discount',
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'discount' => $discount,
            'grand_total' => $grandTotal
        ]);
    }
}
