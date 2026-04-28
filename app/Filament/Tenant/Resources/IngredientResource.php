<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\IngredientResource\Pages;
use App\Models\Ingredient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class IngredientResource extends Resource
{
    protected static ?string $model = Ingredient::class;
    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = 'Purchase & Inventory';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Ingredients / Stock';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Ingredient Details')->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->label('Item Code')
                    ->placeholder('e.g. ING-001'),
                Forms\Components\TextInput::make('unit')
                    ->required()
                    ->placeholder('kg, litre, pcs, g, ml')
                    ->datalist(['kg', 'g', 'litre', 'ml', 'pcs', 'dozen', 'bag']),
                Forms\Components\TextInput::make('purchase_price')
                    ->numeric()
                    ->prefix('৳')
                    ->label('Purchase Price (per unit)')
                    ->default(0),
            ])->columns(2),

            Forms\Components\Section::make('Stock Settings')->schema([
                Forms\Components\TextInput::make('opening_stock')
                    ->numeric()
                    ->label('Opening Stock Quantity')
                    ->default(0),
                Forms\Components\TextInput::make('stock_qty')
                    ->numeric()
                    ->label('Current Stock')
                    ->default(0)
                    ->hint('Updated automatically via purchases'),
                Forms\Components\TextInput::make('min_stock_level')
                    ->numeric()
                    ->label('Minimum Stock Alert Level')
                    ->default(0),
                Forms\Components\Toggle::make('is_active')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label('Code')->placeholder('—'),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('unit')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('stock_qty')
                    ->label('Stock')
                    ->sortable()
                    ->color(fn($record) => $record->isLowStock() ? 'danger' : 'success')
                    ->formatStateUsing(fn($state, $record) => $state . ' ' . $record->unit),
                Tables\Columns\TextColumn::make('min_stock_level')->label('Min Level')
                    ->formatStateUsing(fn($state, $record) => $state . ' ' . $record->unit),
                Tables\Columns\TextColumn::make('purchase_price')->money('BDT')->label('Price/Unit'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
                Tables\Filters\Filter::make('low_stock')
                    ->label('Low Stock Only')
                    ->query(fn($query) => $query->whereColumn('stock_qty', '<=', 'min_stock_level')),
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
            'index'  => Pages\ListIngredients::route('/'),
            'create' => Pages\CreateIngredient::route('/create'),
            'edit'   => Pages\EditIngredient::route('/{record}/edit'),
        ];
    }
}
