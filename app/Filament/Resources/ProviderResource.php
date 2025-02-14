<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProviderResource\Pages;
use App\Filament\Resources\ProviderResource\RelationManagers;
use App\Models\Provider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProviderResource extends Resource
{
    protected static ?string $model = Provider::class;
    protected static ?string $navigationGroup = 'Inventory Management';
    protected static ?string $activeNavigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationLabel = 'Providers';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getProviderFormSchema());  // Added () to call the method
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Full Name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email Address'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone_number')
                    ->label(__('Phone Number'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\SelectColumn::make('group')
                    ->label(__('Group'))
                    ->options([
                        'uncategorized' => 'Uncategorized',
                        'alibaba.com' => 'Alibaba.com',
                    ])
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('shipping_address')
                    ->label(__('Address'))
                    ->visible(true)
                    ->extraAttributes(['wire:poll' => '5s']),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProviders::route('/'),
            'create' => Pages\CreateProvider::route('/create'),
            'edit' => Pages\EditProvider::route('/{record}/edit'),
        ];
    }

    public static function getProviderFormSchema(): array  // Added return type hint
    {
        return [
            Forms\Components\Section::make('Provider Details')
                ->columnSpan(1)
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('first_name')
                        ->label(__('First Name'))
                        ->maxLength(50)
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set, $get) {
                            $lastName = $get('last_name') ?? '';
                            $set('name', trim("$state $lastName"));
                        }),
                    
                    Forms\Components\TextInput::make('last_name')
                        ->label(__('Last Name'))
                        ->maxLength(50)
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set, $get) {
                            $firstName = $get('first_name') ?? '';
                            $set('name', trim("$firstName $state"));
                        }),
                    
                    Forms\Components\TextInput::make('name')
                        ->label(__('Provider Name'))
                        ->maxLength(50)
                        ->disabled()
                        ->dehydrated(true),

                    Forms\Components\TextInput::make('email')
                        ->label(__('Email Address'))
                        ->email()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    Forms\Components\TextInput::make('company_name')
                        ->label(__('Company Name'))                              
                        ->maxLength(255),

                    Forms\Components\TextInput::make('phone_number')
                        ->label(__('Phone Number'))                                  
                        ->maxLength(20),

                    Forms\Components\Select::make('group')
                        ->label(__('Group'))  
                        ->options([                                            
                            'uncategorized' => 'Uncategorized',
                            'alibaba.com' => 'Alibaba.com',
                        ])
                        ->default('uncategorized'),
                ]),

            Forms\Components\Section::make('Address Details')
                ->columnSpan(1)
                ->columns(2)
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
                            if ($record) {
                                $shippingStreetName = $record->shipping_street_name ?? '';
                                $shippingHouseNumber = $record->shipping_house_number ?? '';
                    
                                $set('shipping_address', trim($shippingStreetName . ' ' . $shippingHouseNumber));
                            } else {
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
                            $set('shipping_address', trim($get('shipping_street_name') . ' ' . $get('shipping_house_number')));
                        }),
                    
                    Forms\Components\TextInput::make('shipping_house_number')
                        ->placeholder(__('House Number'))
                        ->hiddenLabel()
                        ->reactive()
                        ->debounce(300)
                        ->afterStateUpdated(function ($set, $get) {
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
                ]),

            Forms\Components\Section::make('Additional Details')
                ->columnSpan(1)
                ->columns(2)
                ->schema([
                    Forms\Components\KeyValue::make('data')
                        ->label(__('Additional Details'))
                        ->columnSpanFull(),
                ]),
        ];
    }
}