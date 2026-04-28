<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\PurchaseOrderResource\Pages;
use App\Models\Ingredient;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Purchase & Inventory';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Purchase Orders';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Purchase Order Details')->schema([
                Forms\Components\TextInput::make('po_number')
                    ->label('PO Number')
                    ->disabled()
                    ->placeholder('Auto-generated')
                    ->dehydrated(false),
                Forms\Components\Select::make('supplier_id')
                    ->label('Supplier')
                    ->options(Supplier::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\DatePicker::make('order_date')
                    ->required()
                    ->default(now()),
                Forms\Components\DatePicker::make('expected_delivery_date')
                    ->label('Expected Delivery'),
                Forms\Components\TextInput::make('product_type')
                    ->label('Product Type')
                    ->placeholder('e.g. Dry Goods, Beverages, Meat'),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'received'  => 'Received',
                        'partial'   => 'Partial',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('pending')
                    ->required(),
            ])->columns(2),

            Forms\Components\Section::make('Order Items')->schema([
                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('ingredient_id')
                            ->label('Item / Ingredient')
                            ->options(Ingredient::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state) {
                                    $ingredient = Ingredient::find($state);
                                    if ($ingredient) {
                                        $set('unit', $ingredient->unit);
                                        $set('rate', $ingredient->purchase_price);
                                    }
                                }
                            }),
                        Forms\Components\TextInput::make('qty')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->reactive()
                            ->afterStateUpdated(fn(Set $set, Get $get) => $set('total', round($get('qty') * $get('rate') * (1 + ($get('vat_rate') ?? 0) / 100), 2))),
                        Forms\Components\TextInput::make('unit')
                            ->required()
                            ->placeholder('kg, pcs...'),
                        Forms\Components\TextInput::make('rate')
                            ->numeric()
                            ->prefix('৳')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn(Set $set, Get $get) => $set('total', round($get('qty') * $get('rate') * (1 + ($get('vat_rate') ?? 0) / 100), 2))),
                        Forms\Components\Select::make('vat_type')
                            ->options(['exclusive' => 'Exclusive', 'inclusive' => 'Inclusive'])
                            ->default('exclusive'),
                        Forms\Components\TextInput::make('vat_rate')
                            ->numeric()
                            ->suffix('%')
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn(Set $set, Get $get) => $set('total', round($get('qty') * $get('rate') * (1 + ($get('vat_rate') ?? 0) / 100), 2))),
                        Forms\Components\TextInput::make('total')
                            ->numeric()
                            ->prefix('৳')
                            ->disabled()
                            ->dehydrated(true),
                    ])
                    ->columns(4)
                    ->addActionLabel('+ Add Item')
                    ->defaultItems(1)
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        $items = $get('items') ?? [];
                        $subtotal = collect($items)->sum(fn($i) => floatval($i['total'] ?? 0));
                        $vat = collect($items)->sum(fn($i) => floatval($i['vat_amount'] ?? 0));
                        $set('subtotal', round($subtotal / (1 + collect($items)->avg(fn($i) => ($i['vat_rate'] ?? 0) / 100)), 2));
                        $set('vat_amount', round($subtotal - $subtotal / (1 + collect($items)->avg(fn($i) => ($i['vat_rate'] ?? 0) / 100)), 2));
                        $set('grand_total', $subtotal);
                    }),
            ]),

            Forms\Components\Section::make('Order Summary')->schema([
                Forms\Components\TextInput::make('subtotal')->numeric()->prefix('৳')->disabled()->dehydrated(true),
                Forms\Components\TextInput::make('vat_amount')->numeric()->prefix('৳')->disabled()->dehydrated(true)->label('VAT'),
                Forms\Components\TextInput::make('grand_total')->numeric()->prefix('৳')->disabled()->dehydrated(true)->label('Grand Total'),
                Forms\Components\Textarea::make('notes')->rows(2)->columnSpanFull()->label('Customer Notes'),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('po_number')->label('PO #')->searchable(),
                Tables\Columns\TextColumn::make('supplier.name')->label('Supplier')->searchable(),
                Tables\Columns\TextColumn::make('order_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('items_count')->counts('items')->label('Items'),
                Tables\Columns\TextColumn::make('grand_total')->money('BDT')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'received',
                        'primary' => 'partial',
                        'danger'  => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('supplier_id')
                    ->label('Supplier')
                    ->options(Supplier::pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending', 'received' => 'Received',
                        'partial' => 'Partial', 'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\Filter::make('order_date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q, $d) => $q->whereDate('order_date', '>=', $d))
                            ->when($data['until'], fn($q, $d) => $q->whereDate('order_date', '<=', $d));
                    }),
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
            'index'  => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'edit'   => Pages\EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
