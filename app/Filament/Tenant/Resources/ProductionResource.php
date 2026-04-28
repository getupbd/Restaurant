<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\ProductionResource\Pages;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Production;
use App\Models\Variant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductionResource extends Resource
{
    protected static ?string $model = Production::class;
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationGroup = 'Production';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Set Production Unit';

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Production Details')->schema([
                Forms\Components\TextInput::make('production_no')
                    ->label('Production #')
                    ->disabled()
                    ->placeholder('Auto-generated'),
                Forms\Components\Select::make('product_id')
                    ->label('Food Item (Output)')
                    ->options(Product::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('variant_id')
                    ->label('Variant / Portion')
                    ->options(Variant::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                Forms\Components\DatePicker::make('production_date')
                    ->required()
                    ->default(now()),
                Forms\Components\TextInput::make('qty_produced')
                    ->label('Quantity Produced')
                    ->numeric()
                    ->default(1)
                    ->required()
                    ->suffix('units')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        self::recalculateTotals($set, $get);
                    }),
                Forms\Components\Select::make('status')
                    ->options(['completed' => 'Completed', 'wastage' => 'Wastage'])
                    ->default('completed')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->rows(2)
                    ->columnSpanFull(),
            ])->columns(3),

            Forms\Components\Section::make('Ingredients Used')->schema([
                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('ingredient_id')
                            ->label('Ingredient')
                            ->options(Ingredient::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state) {
                                    $ing = Ingredient::find($state);
                                    if ($ing) {
                                        $set('unit', $ing->unit);
                                        $set('current_rate', $ing->purchase_price);
                                    }
                                }
                            }),
                        Forms\Components\TextInput::make('qty_required')
                            ->label('Qty from Storage')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                $qty  = floatval($get('qty_required') ?? 0);
                                $rate = floatval($get('current_rate') ?? 0);
                                $set('total_cost', round($qty * $rate, 2));
                            }),
                        Forms\Components\TextInput::make('unit')
                            ->label('Unit')
                            ->required(),
                        Forms\Components\TextInput::make('current_rate')
                            ->label('Rate (৳/unit)')
                            ->numeric()
                            ->prefix('৳')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                $qty  = floatval($get('qty_required') ?? 0);
                                $rate = floatval($get('current_rate') ?? 0);
                                $set('total_cost', round($qty * $rate, 2));
                            }),
                        Forms\Components\TextInput::make('total_cost')
                            ->label('Total Cost (৳)')
                            ->numeric()
                            ->prefix('৳')
                            ->disabled()
                            ->dehydrated(true),
                    ])
                    ->columns(5)
                    ->addActionLabel('+ Add Ingredient')
                    ->defaultItems(1)
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        self::recalculateTotals($set, $get);
                    }),
            ]),

            Forms\Components\Section::make('Cost Summary')->schema([
                Forms\Components\TextInput::make('total_cost')
                    ->label('Total Production Cost (৳)')
                    ->prefix('৳')
                    ->disabled()
                    ->dehydrated(true),
                Forms\Components\TextInput::make('cost_per_unit')
                    ->label('Cost Per Unit (৳)')
                    ->prefix('৳')
                    ->disabled()
                    ->dehydrated(true),
            ])->columns(2),

        ]);
    }

    protected static function recalculateTotals(Set $set, Get $get): void
    {
        $items   = $get('items') ?? [];
        $total   = collect($items)->sum(fn($i) => floatval($i['total_cost'] ?? 0));
        $qty     = floatval($get('qty_produced') ?? 1);
        $perUnit = $qty > 0 ? round($total / $qty, 4) : 0;

        $set('total_cost', round($total, 2));
        $set('cost_per_unit', $perUnit);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('production_no')->label('PRD #')->searchable(),
                Tables\Columns\TextColumn::make('product.name')->label('Food Item')->searchable(),
                Tables\Columns\TextColumn::make('variant.name')->label('Variant')->placeholder('—'),
                Tables\Columns\TextColumn::make('production_date')->date()->sortable()->label('Date'),
                Tables\Columns\TextColumn::make('qty_produced')->label('Qty Produced')->suffix(' units'),
                Tables\Columns\TextColumn::make('items_count')->counts('items')->label('Ingredients'),
                Tables\Columns\TextColumn::make('total_cost')->money('BDT')->sortable()->label('Total Cost'),
                Tables\Columns\TextColumn::make('cost_per_unit')->money('BDT')->label('Cost/Unit'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['success' => 'completed', 'danger' => 'wastage']),
            ])
            ->defaultSort('production_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('product_id')
                    ->label('Food Item')
                    ->options(Product::pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('status')
                    ->options(['completed' => 'Completed', 'wastage' => 'Wastage']),
                Tables\Filters\Filter::make('production_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From'),
                        Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q, $d) => $q->whereDate('production_date', '>=', $d))
                            ->when($data['until'], fn($q, $d) => $q->whereDate('production_date', '<=', $d));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProductions::route('/'),
            'create' => Pages\CreateProduction::route('/create'),
            'view'   => Pages\ViewProduction::route('/{record}'),
        ];
    }
}
