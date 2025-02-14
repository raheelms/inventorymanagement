<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\OrdersRelationManager;
use App\Models\User;
use Filament\Infolists\Infolist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextForm;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreatUser;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationGroup = 'User Management';
    // protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $activeNavigationIcon = 'heroicon-o-check-badge'; //CHANGE THE ICON IN FRONT OF THE MENU ITEM AS THE ACTIVE MENU ITEM- comment by raheel
    protected static ?string $navigationLabel = 'Users'; //CHANGE THE LABEL OF THE MENU ITEM- comment by raheel
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(heading: 'User Details')
                    ->columnSpan(1)
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make(name: 'name')
                            ->label(__('User Name'))
                            ->maxValue(value: 50)
                            ->required(),

                        Forms\Components\TextInput::make(name: 'email')
                            ->label(__('Email Adddress'))
                            ->email()
                            ->maxlength(255)
                            ->unique(ignoreRecord: true )
                            ->required(),
                        
                        Forms\Components\DateTimePicker::make(name: 'email_verified_at')
                            ->label(__('Email Verified At'))
                            ->default(now()),
                        
                        Forms\Components\TextInput::make(name: 'password')
                            ->label(__('Password Name'))
                            ->required()
                            ->password()
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->visible(fn($livewire) => $livewire instanceof CreateUser)
                            ->rule(Password::default()),
                            ]),

                Forms\Components\Section::make(heading: 'New User Password')
                    ->visible(fn($livewire) => $livewire instanceof EditUser)
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
                    ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make( name: 'name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make( name: 'email')
                    ->label(__('Email Address'))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Email copied to clipboard.'),

                Tables\Columns\TextColumn::make( name: 'email_verified_at')
                    ->label(__('Email Verified At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
