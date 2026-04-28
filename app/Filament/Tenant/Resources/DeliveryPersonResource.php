<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\DeliveryPersonResource\Pages;
use App\Models\DeliveryPerson;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeliveryPersonResource extends Resource
{
    protected static ?string $model = DeliveryPerson::class;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'Order Management';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Delivery Persons';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('phone')->tel(),
            Forms\Components\TextInput::make('commission_rate')
                ->numeric()
                ->suffix('%')
                ->default(0)
                ->label('Commission Rate'),
            Forms\Components\Toggle::make('is_active')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('phone')->placeholder('—'),
                Tables\Columns\TextColumn::make('commission_rate')->suffix('%')->label('Commission'),
                Tables\Columns\TextColumn::make('orders_count')->counts('orders')->label('Deliveries'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDeliveryPersons::route('/'),
            'create' => Pages\CreateDeliveryPerson::route('/create'),
            'edit'   => Pages\EditDeliveryPerson::route('/{record}/edit'),
        ];
    }
}
