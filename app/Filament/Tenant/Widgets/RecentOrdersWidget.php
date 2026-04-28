<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\Order;
use App\Models\OrderItem;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected static ?string $heading = 'Recent Orders';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::with(['table', 'items'])->latest()->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->formatStateUsing(fn ($state) => '#' . str_pad($state, 4, '0', STR_PAD_LEFT))
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('table.name')
                    ->label('Table')
                    ->placeholder('—')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items'),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total')
                    ->money('BDT')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        'preparing' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Time')
                    ->since()
                    ->sortable(),
            ])
            ->paginated(false)
            ->striped();
    }
}
