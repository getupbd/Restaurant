<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Order Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Customers';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Customer Information')->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('phone')->tel()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('email')->email()->nullable(),
                Forms\Components\Textarea::make('address')->rows(2)->columnSpanFull(),
            ])->columns(2),
            Forms\Components\Section::make('Loyalty')->schema([
                Forms\Components\TextInput::make('loyalty_points')
                    ->numeric()->default(0)->label('Loyalty Points'),
                Forms\Components\Toggle::make('is_active')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('phone')->searchable()->placeholder('—'),
                Tables\Columns\TextColumn::make('email')->placeholder('—'),
                Tables\Columns\TextColumn::make('orders_count')->counts('orders')->label('Orders'),
                Tables\Columns\TextColumn::make('loyalty_points')->label('Points')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
                Tables\Columns\TextColumn::make('created_at')->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([Tables\Filters\TernaryFilter::make('is_active')])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit'   => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
