<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\InventoryAdjustmentResource\Pages;
use App\Models\Ingredient;
use App\Models\InventoryAdjustment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InventoryAdjustmentResource extends Resource
{
    protected static ?string $model = InventoryAdjustment::class;
    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?string $navigationGroup = 'Purchase & Inventory';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationLabel = 'Inventory Adjustment';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Adjustment Details')->schema([
                Forms\Components\Select::make('ingredient_id')
                    ->label('Ingredient / Item')
                    ->options(Ingredient::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if ($state) {
                            $ing = Ingredient::find($state);
                            if ($ing) $set('qty_before', $ing->stock_qty);
                        }
                    }),
                Forms\Components\DatePicker::make('adjustment_date')
                    ->required()
                    ->default(now()),
                Forms\Components\TextInput::make('qty_before')
                    ->numeric()
                    ->label('Stock Before')
                    ->disabled()
                    ->dehydrated(true),
                Forms\Components\TextInput::make('qty_adjusted')
                    ->numeric()
                    ->label('Adjustment Quantity (+/-)')
                    ->helperText('Use positive to add, negative to deduct')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        $before = floatval($get('qty_before') ?? 0);
                        $adj = floatval($get('qty_adjusted') ?? 0);
                        $set('qty_after', round($before + $adj, 4));
                    }),
                Forms\Components\TextInput::make('qty_after')
                    ->numeric()
                    ->label('Stock After')
                    ->disabled()
                    ->dehydrated(true),
                Forms\Components\Select::make('reason')
                    ->options([
                        'damage'   => 'Damage / Spoilage',
                        'shrinkage'=> 'Shrinkage',
                        'recount'  => 'Physical Recount',
                        'theft'    => 'Theft',
                        'other'    => 'Other',
                    ])
                    ->required(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ingredient.name')->label('Ingredient')->searchable(),
                Tables\Columns\TextColumn::make('adjustment_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('qty_before')->label('Before'),
                Tables\Columns\TextColumn::make('qty_adjusted')
                    ->label('Adjustment')
                    ->color(fn($state) => $state >= 0 ? 'success' : 'danger')
                    ->formatStateUsing(fn($state) => ($state >= 0 ? '+' : '') . $state),
                Tables\Columns\TextColumn::make('qty_after')->label('After'),
                Tables\Columns\TextColumn::make('reason')->badge()->color('gray'),
            ])
            ->defaultSort('adjustment_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('ingredient_id')
                    ->label('Ingredient')
                    ->options(Ingredient::pluck('name', 'id')),
            ])
            ->actions([Tables\Actions\ViewAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListInventoryAdjustments::route('/'),
            'create' => Pages\CreateInventoryAdjustment::route('/create'),
        ];
    }
}
