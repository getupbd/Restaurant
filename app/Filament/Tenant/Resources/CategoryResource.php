<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Food Management';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Categories';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Basic Info')->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\ColorPicker::make('color')
                    ->label('Display Color'),
                Forms\Components\TextInput::make('icon')
                    ->label('Icon (heroicon name)')
                    ->placeholder('heroicon-o-shopping-cart'),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->directory(fn () => 'tenants/' . tenant('id') . '/categories')
                    ->imageEditor(),
            ])->columns(2),

            Forms\Components\Section::make('Promotional Offer')->schema([
                Forms\Components\TextInput::make('offer_rate')
                    ->numeric()
                    ->suffix('%')
                    ->label('Discount Rate'),
                Forms\Components\DatePicker::make('offer_start_date')
                    ->label('Offer Start'),
                Forms\Components\DatePicker::make('offer_end_date')
                    ->label('Offer End')
                    ->afterOrEqual('offer_start_date'),
            ])->columns(3),

            Forms\Components\Section::make('Display Settings')->schema([
                Forms\Components\TextInput::make('position')
                    ->numeric()
                    ->default(0)
                    ->label('Listing Position'),
                Forms\Components\Toggle::make('show_on_web')
                    ->default(true)
                    ->label('Show on Website'),
                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->label('Active'),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ColorColumn::make('color')->label('Color'),
                Tables\Columns\ImageColumn::make('image')->circular()->label(''),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('products_count')->counts('products')->label('Foods')->sortable(),
                Tables\Columns\TextColumn::make('offer_rate')->suffix('%')->label('Offer')->placeholder('—'),
                Tables\Columns\IconColumn::make('show_on_web')->boolean()->label('Web'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
                Tables\Columns\TextColumn::make('position')->sortable()->label('Order'),
            ])
            ->defaultSort('position')
            ->reorderable('position')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
                Tables\Filters\TernaryFilter::make('show_on_web')->label('On Web'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit'   => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
