<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\OrderResource\Pages;
use App\Models\Customer;
use App\Models\DeliveryPerson;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table as RestaurantTable;
use App\Models\User;
use App\Models\Variant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Order Management';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Order List';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Order Details')->schema([
                Forms\Components\TextInput::make('invoice_no')
                    ->label('Invoice #')
                    ->disabled()
                    ->placeholder('Auto-generated'),
                Forms\Components\Select::make('order_type')
                    ->options(Order::orderTypeOptions())
                    ->default('dine_in')
                    ->required()
                    ->reactive(),
                Forms\Components\Select::make('table_id')
                    ->label('Table')
                    ->options(RestaurantTable::pluck('name', 'id'))
                    ->searchable()
                    ->nullable()
                    ->visible(fn(Get $get) => in_array($get('order_type'), ['dine_in'])),
                Forms\Components\Select::make('customer_id')
                    ->label('Customer')
                    ->options(Customer::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                Forms\Components\Select::make('waiter_id')
                    ->label('Waiter')
                    ->options(User::pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                Forms\Components\Select::make('delivery_person_id')
                    ->label('Delivery Person')
                    ->options(DeliveryPerson::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->nullable()
                    ->visible(fn(Get $get) => $get('order_type') === 'delivery'),
                Forms\Components\Select::make('status')
                    ->options(Order::statusOptions())
                    ->default('pending')
                    ->required(),
                Forms\Components\Select::make('payment_status')
                    ->options(['unpaid' => 'Unpaid', 'paid' => 'Paid', 'partial' => 'Partial'])
                    ->default('unpaid')
                    ->required(),
                Forms\Components\Select::make('payment_method')
                    ->options(['cash' => 'Cash', 'card' => 'Card', 'mobile_banking' => 'Mobile Banking', 'online' => 'Online'])
                    ->nullable(),
                Forms\Components\TextInput::make('event_code')->nullable()->label('Event Code'),
                Forms\Components\TextInput::make('voucher_no')->nullable()->label('Voucher No'),
                Forms\Components\Textarea::make('notes')->rows(2)->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Order Items')->schema([
                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Food Item')
                            ->options(Product::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state) {
                                    $product = Product::find($state);
                                    if ($product) $set('unit_price', $product->price);
                                }
                            }),
                        Forms\Components\Select::make('variant_id')
                            ->label('Variant')
                            ->options(Variant::where('is_active', true)->pluck('name', 'id'))
                            ->nullable(),
                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn(Set $set, Get $get) => $set('subtotal', round(floatval($get('quantity')) * floatval($get('unit_price') ?? 0), 2))),
                        Forms\Components\TextInput::make('unit_price')
                            ->numeric()
                            ->prefix('৳')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn(Set $set, Get $get) => $set('subtotal', round(floatval($get('quantity')) * floatval($get('unit_price') ?? 0), 2))),
                        Forms\Components\TextInput::make('subtotal')
                            ->numeric()
                            ->prefix('৳')
                            ->disabled()
                            ->dehydrated(true),
                        Forms\Components\Textarea::make('notes')->rows(1)->label('Note')->placeholder('Spicy, no onion...'),
                    ])
                    ->columns(3)
                    ->addActionLabel('+ Add Food Item')
                    ->defaultItems(1),
            ]),

            Forms\Components\Section::make('Pricing Summary')->schema([
                Forms\Components\TextInput::make('subtotal')->numeric()->prefix('৳')->dehydrated(true),
                Forms\Components\Select::make('discount_type')
                    ->options(['flat' => 'Flat (৳)', 'percentage' => 'Percentage (%)'])
                    ->nullable()
                    ->reactive(),
                Forms\Components\TextInput::make('discount_amount')
                    ->numeric()
                    ->default(0)
                    ->label('Discount'),
                Forms\Components\TextInput::make('coupon_code')->nullable()->label('Coupon Code'),
                Forms\Components\TextInput::make('vat_amount')->numeric()->prefix('৳')->default(0)->label('VAT'),
                Forms\Components\TextInput::make('total_price')->numeric()->prefix('৳')->required()->label('Grand Total'),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_no')->label('Invoice #')->searchable()->sortable(),
                Tables\Columns\BadgeColumn::make('order_type')
                    ->label('Type')
                    ->colors([
                        'primary' => 'dine_in',
                        'success' => 'takeaway',
                        'warning' => 'delivery',
                        'gray'    => 'online',
                    ])
                    ->formatStateUsing(fn($state) => Order::orderTypeOptions()[$state] ?? $state),
                Tables\Columns\TextColumn::make('customer.name')->label('Customer')->placeholder('Walk-in'),
                Tables\Columns\TextColumn::make('waiter.name')->label('Waiter')->placeholder('—'),
                Tables\Columns\TextColumn::make('table.name')->label('Table')->placeholder('—'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'cooking',
                        'info'    => 'ready',
                        'success' => fn($state) => in_array($state, ['served', 'paid']),
                        'danger'  => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('total_price')->money('BDT')->sortable()->label('Total'),
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->colors(['danger' => 'unpaid', 'success' => 'paid', 'warning' => 'partial']),
                Tables\Columns\TextColumn::make('event_code')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('voucher_no')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->label('Time'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Order::statusOptions()),
                Tables\Filters\SelectFilter::make('order_type')
                    ->label('Order Type')
                    ->options(Order::orderTypeOptions()),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options(['unpaid' => 'Unpaid', 'paid' => 'Paid', 'partial' => 'Partial']),
                Tables\Filters\SelectFilter::make('customer_id')
                    ->label('Customer')
                    ->options(Customer::pluck('name', 'id')),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From Date'),
                        Forms\Components\DatePicker::make('until')->label('Until Date'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                            ->when($data['until'], fn($q, $d) => $q->whereDate('created_at', '<=', $d));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_paid')
                    ->label('Mark Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Order $record) => $record->payment_status !== 'paid')
                    ->action(fn(Order $record) => $record->update([
                        'payment_status' => 'paid',
                        'status'         => 'paid',
                        'is_paid'        => true,
                        'paid_at'        => now(),
                    ])),
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn(Order $record) => !in_array($record->status, ['paid', 'cancelled']))
                    ->action(fn(Order $record) => $record->update(['status' => 'cancelled'])),
                Tables\Actions\EditAction::make(),
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
            'index'   => Pages\ListOrders::route('/'),
            'pending' => Pages\PendingOrders::route('/pending'),
            'complete'=> Pages\CompleteOrders::route('/complete'),
            'cancelled'=> Pages\CancelledOrders::route('/cancelled'),
            'create'  => Pages\CreateOrder::route('/create'),
            'edit'    => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
