<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\OrderResource\Pages;
use App\Filament\Tenant\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('table_id')
                    ->relationship('table', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'cooking' => 'Cooking',
                        'ready' => 'Ready',
                        'served' => 'Served',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->default('pending'),
                Forms\Components\Select::make('payment_status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'paid' => 'Paid',
                    ])
                    ->required()
                    ->default('unpaid'),
                Forms\Components\TextInput::make('total_price')
                    ->numeric()
                    ->required()
                    ->prefix(fn () => tenant('currency_symbol')),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order #')
                    ->searchable(),
                Tables\Columns\TextColumn::make('table.name')
                    ->badge(),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'cooking' => 'Cooking',
                        'ready' => 'Ready',
                        'served' => 'Served',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'unpaid' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('total_price')
                    ->money(fn () => tenant('currency')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'cooking' => 'Cooking',
                        'ready' => 'Ready',
                        'served' => 'Served',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('printReceipt')
                    ->label('Print Receipt')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->modalHeading('Order Receipt')
                    ->modalWidth('sm')
                    ->modalContent(fn ($record) => view('filament.tenant.resources.order.receipt', ['order' => $record]))
                    ->modalSubmitActionLabel('Print')
                    ->extraAttributes([
                        'onclick' => 'window.print()',
                    ]),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('export')
                        ->label('Export CSV')
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(function (\Illuminate\Support\Collection $records) {
                            return response()->streamDownload(function () use ($records) {
                                $handle = fopen('php://output', 'w');
                                fputcsv($handle, ['Order #', 'Table', 'Status', 'Total Price', 'Created At']);

                                foreach ($records as $record) {
                                    fputcsv($handle, [
                                        $record->id,
                                        $record->table?->name ?? 'N/A',
                                        $record->status,
                                        $record->total_price,
                                        $record->created_at->format('Y-m-d H:i:s'),
                                    ]);
                                }

                                fclose($handle);
                            }, 'orders.csv');
                        }),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
