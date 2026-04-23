<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\InventoryLogResource\Pages;
use App\Filament\Tenant\Resources\InventoryLogResource\RelationManagers;
use App\Models\InventoryLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InventoryLogResource extends Resource
{
    protected static ?string $model = InventoryLog::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Manual Stock Adjustment')
                    ->description('Manually adjust the stock level for a specific product. Positive numbers increase stock, negative numbers decrease stock.')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->hint(fn ($get) => 
                                $get('product_id') 
                                    ? 'Current stock: ' . (\App\Models\Product::find($get('product_id'))->stock_quantity ?? 0)
                                    : null
                            ),
                        Forms\Components\TextInput::make('change_amount')
                            ->label('Adjustment Quantity')
                            ->numeric()
                            ->required()
                            ->helperText('Use positive for additions (e.g., 50) and negative for subtractions (e.g., -10)'),
                        Forms\Components\Textarea::make('reason')
                            ->required()
                            ->columnSpanFull()
                            ->placeholder('e.g., Damaged item, Restocking, Seasonal adjustment'),
                    ])->columns(2),
            ]);
    }

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('old_quantity')
                    ->label('Previous')
                    ->numeric()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('change_amount')
                    ->label('Adjustment')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => $state > 0 ? "+{$state}" : $state),
                Tables\Columns\TextColumn::make('new_quantity')
                    ->label('Final Stock')
                    ->numeric()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('reason')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                // Read-only
            ])
            ->bulkActions([
                // Read-only
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
            'index' => Pages\ListInventoryLogs::route('/'),
            'create' => Pages\CreateInventoryLog::route('/create'),
            'view' => Pages\ViewInventoryLog::route('/{record}'),
            'edit' => Pages\EditInventoryLog::route('/{record}/edit'),
        ];
    }
}
