<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\Pages\CreateCustomer;
use App\Filament\Resources\CustomerResource\Pages\EditCustomer;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationGroup = 'User Management';
    // protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $activeNavigationIcon = 'heroicon-o-check-badge'; //CHANGE THE ICON IN FRONT OF THE MENU ITEM AS THE ACTIVE MENU ITEM- comment by raheel
    protected static ?string $navigationLabel = 'Customers'; //CHANGE THE LABEL OF THE MENU ITEM- comment by raheel
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(heading: 'Customer Details')
                    ->columnSpan(1)
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make(name: 'first_name')
                            ->label(__('First Name'))
                            ->maxValue(value: 50)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                $lastName = $get('last_name') ?? '';
                                $set('name', trim("$state $lastName"));
                            })
                            ->required(),
                        
                        Forms\Components\TextInput::make(name: 'last_name')
                            ->label(__('Last Name'))
                            ->maxValue(value: 50)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                $firstName = $get('first_name') ?? '';
                                $set('name', trim("$firstName $state"));
                            })
                            ->required(),
                        
                        Forms\Components\TextInput::make(name: 'name')
                            ->label(__('Customer Name'))
                            ->maxValue(value: 50)
                            ->disabled()
                            ->dehydrated(true),
        
                        Forms\Components\TextInput::make(name: 'email')
                            ->label(__('Email Address'))
                            ->email()
                            ->maxlength(255)
                            ->unique(ignoreRecord: true )
                            ->required(),
                        
                        Forms\Components\DateTimePicker::make(name: 'email_verified_at')
                            ->label(__('Email Verified At'))                                
                            ->default(now()),

                        Forms\Components\TextInput::make(name: 'company_name')
                            ->label(__('Company Name'))                              
                            ->maxlength(255),

                        Forms\Components\TextInput::make(name: 'phone_number')
                            ->label(__('Phone Number'))                                  
                            ->maxlength(20),

                        Forms\Components\Select::make(name:'group')
                            ->label(__('Group'))  
                            ->options([                                            
                                'uncategorized' => 'Uncategorized',
                                'bol.com' => 'Bol.com',
                                'meddirect' => 'Meddirect',
                            ])
                            ->default('uncategorized'),
                        
                        Forms\Components\TextInput::make(name: 'password')
                            ->label(__('Password'))
                            ->required()
                            ->password()
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->visible(fn($livewire) => $livewire instanceof CreateCustomer)
                            ->rule(Password::default()),
                    ]),   

                Forms\Components\Section::make(heading: 'Additional Details')
                    ->columnSpan(1)
                    ->columns(2)
                    ->schema([
                        Forms\Components\KeyValue::make('data')
                            ->label(__('Additional Details'))
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make(heading: 'New User Password')
                    ->visible(fn($livewire) => $livewire instanceof EditCustomer)
                    ->columnSpan(1)
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make(name: 'new_password')
                            ->label(__('New Password'))
                            ->nullable()
                            ->password()                            
                            ->rule(Password::default()),
                        
                        Forms\Components\TextInput::make(name: 'new_confirmation_password')
                            ->label(__('New Confirmation Password'))
                            ->password()
                            ->same('new_password')
                            ->requiredWith('new_password'),
                    ]),
            
                Forms\Components\Section::make(heading: 'Addresses')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Section::make('Shipping Details')
                            ->columnSpan(1)
                            ->columns(1)
                            ->schema([
                                Forms\Components\TextInput::make('shipping_address')
                                    ->placeholder(__('Address'))
                                    ->hiddenLabel()
                                    ->disabled()
                                    ->dehydrated()
                                    ->default(function ($record) {
                                        return $record && ($record->shipping_street_name || $record->shipping_house_number)
                                            ? trim($record->shipping_street_name . ' ' . $record->shipping_house_number)
                                            : '';
                                    })
                                    ->afterStateHydrated(function ($set, $get, $record) {
                                        // Set the initial state from the record when loading the form
                                        if ($record) {
                                            $shippingStreetName = $record->shipping_street_name ?? '';
                                            $shippingHouseNumber = $record->shipping_house_number ?? '';
                                
                                            // Only set shipping_address if either part is available
                                            $set('shipping_address', trim($shippingStreetName . ' ' . $shippingHouseNumber));
                                        } else {
                                            // Clear the shipping_address if no record exists
                                            $set('shipping_address', '');
                                        }
                                    })
                                    ->reactive(),
                            
                                Forms\Components\TextInput::make('shipping_street_name')
                                    ->placeholder(__('Street Name'))
                                    ->hiddenLabel()
                                    ->reactive()
                                    ->debounce(300)
                                    ->afterStateUpdated(function ($set, $get) {
                                        // Update shipping_address when street name is updated
                                        $set('shipping_address', trim($get('shipping_street_name') . ' ' . $get('shipping_house_number')));
                                    }),
                                
                                Forms\Components\TextInput::make('shipping_house_number')
                                    ->placeholder(__('House Number'))
                                    ->hiddenLabel()
                                    ->reactive()
                                    ->debounce(300)
                                    ->afterStateUpdated(function ($set, $get) {
                                        // Update shipping_address when house number is updated
                                        $set('shipping_address', trim($get('shipping_street_name') . ' ' . $get('shipping_house_number')));
                                    }),
                
                                Forms\Components\TextInput::make('shipping_postal_code')
                                    ->placeholder(__('Postal Code'))
                                    ->hiddenLabel(),
                
                                Forms\Components\TextInput::make('shipping_city')
                                    ->placeholder(__('City'))
                                    ->hiddenLabel(),
                
                                Forms\Components\TextInput::make('shipping_country')
                                    ->placeholder(__('Country'))
                                    ->hiddenLabel(),

                                Forms\Components\Checkbox::make('use_shipping_address')
                                    ->live()
                                    ->default(fn($record) => 
                                        empty($record->billing_address) && 
                                        empty($record->billing_street_name) && 
                                        empty($record->billing_house_number) && 
                                        empty($record->billing_zipcode) && 
                                        empty($record->billing_city) && 
                                        empty($record->billing_country)
                                    )
                                    ->label('Same as shipping address')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) { // if checkbox is checked
                                            $set('billing_street_name', null);
                                            $set('billing_house_number', null);
                                            $set('billing_address', null);
                                            $set('billing_postal_code', null);
                                            $set('billing_city', null);
                                            $set('billing_country', null);
                                        }
                                    }),
                            ]),
            
                        Forms\Components\Section::make('Billing Details')
                            ->columnSpan(1)
                            ->columns(1)
                            ->schema([
                                Forms\Components\Placeholder::make('') // add a heading
                                ->content(function (Forms\Get $get) {
                                    return 'Same as shipping address:' . PHP_EOL . 
                                           $get('shipping_address') . PHP_EOL .
                                           $get('shipping_postal_code') . ' ' . $get('shipping_city') . PHP_EOL .
                                           $get('shipping_country');
                                })
                                ->visible(fn (Forms\Get $get) => $get('use_shipping_address'))
                                ->columnSpanFull(),          

                                Forms\Components\Group::make([
                                    Forms\Components\TextInput::make('billing_address')
                                        ->placeholder(__('Address'))
                                        ->hiddenLabel()
                                        ->disabled()
                                        ->dehydrated()
                                        ->default(function ($record) {
                                            return $record && ($record->billing_street_name || $record->billing_house_number)
                                                ? trim($record->billing_street_name . ' ' . $record->billing_house_number)
                                                : '';
                                        })
                                        ->afterStateHydrated(function ($set, $get, $record) {
                                            // Set the initial state from the record when loading the form
                                            if ($record) {
                                                $billingStreetName = $record->billing_street_name ?? '';
                                                $billingHouseNumber = $record->billing_house_number ?? '';
                                
                                                // Only set billing_address if either part is available
                                                $set('billing_address', trim($billingStreetName . ' ' . $billingHouseNumber));
                                            } else {
                                                // Clear the billing_address if no record exists
                                                $set('billing_address', '');
                                            }
                                        })
                                        ->reactive(),
                            
                                    Forms\Components\TextInput::make('billing_street_name')
                                        ->placeholder(__('Street Name'))
                                        ->hiddenLabel()
                                        ->live()
                                        ->dehydrated()
                                        ->reactive()
                                        ->debounce(300)
                                        ->afterStateUpdated(function ($set, $get) {
                                            // Update billing_address when street name is updated
                                            $set('billing_address', trim($get('billing_street_name') . ' ' . $get('billing_house_number')));
                                
                                            // Clear billing address if shipping fields are empty
                                            if (empty($get('billing_street_name')) && empty($get('billing_house_number'))) {
                                                $set('billing_street_name', '');
                                                $set('billing_house_number', '');
                                                $set('billing_address', ''); // Clear billing_address if both are empty
                                            }
                                        }),
                                
                                    Forms\Components\TextInput::make('billing_house_number')
                                        ->placeholder(__('House Number'))
                                        ->hiddenLabel()
                                        ->live()
                                        ->dehydrated()
                                        ->reactive()
                                        ->debounce(300)
                                        ->afterStateUpdated(function ($set, $get) {
                                            // Update billing_address when house number is updated
                                            $set('billing_address', trim($get('billing_street_name') . ' ' . $get('billing_house_number')));
                                
                                            // Clear billing address if shipping fields are empty
                                            if (empty($get('billing_street_name')) && empty($get('billing_house_number'))) {
                                                $set('billing_street_name', '');
                                                $set('billing_house_number', '');
                                                $set('billing_address', ''); // Clear billing_address if both are empty
                                            }
                                        }),                   
                
                                    Forms\Components\TextInput::make('billing_postal_code')
                                        ->placeholder(__('Postal Code'))
                                        ->live()
                                        ->dehydrated()
                                        ->hiddenLabel(),
                
                                    Forms\Components\TextInput::make('billing_city')
                                        ->placeholder(__('City'))
                                        ->live()
                                        ->dehydrated()
                                        ->hiddenLabel(),
                
                                    Forms\Components\TextInput::make('billing_country')
                                        ->placeholder(__('Country'))
                                        ->live()
                                        ->dehydrated()
                                        ->hiddenLabel(),
                                ])->hidden(fn(Forms\Get $get) => $get('use_shipping_address'))
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make( name: 'name')
                    ->label(__('Full Name'))
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make( name: 'email')
                    ->label(__('Email Address'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make( name: 'email_verified_at')
                    ->label(__('Email Verified At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\SelectColumn::make( name: 'group')
                    ->label(__('Group'))
                    ->options([
                        'uncategorized' => 'Uncategorized',
                        'bol.com' => 'Bol.com',
                        'meddirect' => 'Meddirect',
                    ])
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('shipping_address')
                    ->label(__('Shipping Address'))
                    ->getStateUsing(function ($record) {
                        // Calculate shipping address from components
                        return trim($record->shipping_street_name . ' ' . $record->shipping_house_number);
                    })
                    ->badge()
                    ->color('success')                    
                    ->visible(true),

                Tables\Columns\TextColumn::make('billing_address')
                    ->label(__('Billing Address'))
                    ->getStateUsing(function ($record) {
                        if ($record->use_shipping_address) {
                            return 'Use shipping address';
                        }
                        // Return calculated billing address or blank if empty
                        $billingAddress = trim($record->billing_street_name . ' ' . $record->billing_house_number);
                        return !empty($billingAddress) ? $billingAddress : '';
                    })
                    ->badge()
                    ->color(function ($state) {
                        if ($state === 'Use shipping address') {
                            return 'info';
                        } else {
                            return 'success';
                        }
                    })
                    ->visible(true),

                Tables\Columns\TextColumn::make( name: 'created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make( name: 'updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            // OrdersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
