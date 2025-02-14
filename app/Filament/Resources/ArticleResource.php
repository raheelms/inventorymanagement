<?php

namespace App\Filament\Resources;

use App\Enums\ArticleStatus;
use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
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

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;
    protected static ?string $navigationGroup = 'Content Management';
    // protected static ?string $navigationIcon = 'heroicon-o-newspaper'; 
    protected static ?string $activeNavigationIcon = 'heroicon-o-check-badge'; //CHANGE THE ICON IN FRONT OF THE MENU ITEM AS THE ACTIVE MENU ITEM- comment by raheel
    protected static ?string $navigationLabel = 'Posts'; //CHANGE THE LABEL OF THE MENU ITEM- comment by raheel
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                ->schema([
                    Forms\Components\Tabs\Tab::make('Content')
                    ->schema([
                        Forms\Components\Section::make('Manage Articles')
                        ->collapsible()
                        ->collapsed(false)
                        ->description('')
                        ->schema([
                            Forms\Components\TextInput::make(name: 'title')
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
                                ->unique(table: Article::class, column: 'slug', ignoreRecord: true ),
        
                            Forms\Components\ColorPicker::make(name: 'color')
                                ->required(),
        
                            Forms\Components\Select::make(name: 'categories')
                                ->preload()
                                ->multiple()
                                ->relationship(name: 'categories', titleAttribute: 'name')
                                ->required()
                                ->searchable(),
        
                            TiptapEditor::make(name: 'content')
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
                                Forms\Components\Checkbox::make(name: 'published'),
    
                                Forms\Components\Select::make('status')
                                    ->options(ArticleStatus::options())
                                    ->default(ArticleStatus::DRAFT->value),
                                
                                Forms\Components\Toggle::make(name: 'is_visible')
                                ->label(label: 'Visibility')
                                ->helperText(text: 'Enable or disable article visibility')
                                ->default(state: true),
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
                            ->directory(directory: 'images')
                            ->multiple()
                            ->reorderable(),

                            CuratorPicker::make('media_id')
                                ->label('Media ID')
                                ->helperText(text: 'Add Media')
                                ->color('primary')
                                ->disk('public')
                                ->directory(directory: 'media'),

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
                    ->defaultImageUrl('/images/default_image.png')
                    ->toggleable(isToggledHiddenByDefault: false),
                    
                Tables\Columns\TextColumn::make(name: 'id')
                    ->label(label: 'ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make(name: 'title')
                    ->label(label: 'Title')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make(name: 'slug')
                    ->label(label: 'Slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\ColorColumn::make(name: 'color')
                    ->label(label: 'Color')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make(name: 'tags')
                    ->label(label: 'Tags')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\CheckboxColumn::make(name: 'published')
                    ->label(label: 'Published At'),
                
                SelectColumn::make('status')
                    ->label('Status')
                    ->options(ArticleStatus::options())
                    ->default(ArticleStatus::DRAFT->value),
                
                Tables\Columns\TextColumn::make(name: 'created_at')
                    ->label(label: 'Created Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make(name: 'updated_at')
                    ->label(label: 'Updated Date')
                    ->sortable()
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categories')
                    ->searchable()
                    ->multiple()
                    ->relationship('categories', 'name'),

                Tables\Filters\SelectFilter::make('status')
                    ->options(ArticleStatus::options()),

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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
