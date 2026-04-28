<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\KitchenResource\Pages;
use App\Models\Kitchen;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KitchenResource extends Resource
{
    protected static ?string $model = Kitchen::class;
    protected static ?string $navigationIcon = 'heroicon-o-fire';
    protected static ?string $navigationGroup = 'Food Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Kitchen Setup';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->nullable()
                    ->rows(2),
                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->label('Active'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('description')->limit(50)->placeholder('—'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
                Tables\Columns\TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Foods'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
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
            'index'  => Pages\ListKitchens::route('/'),
            'create' => Pages\CreateKitchen::route('/create'),
            'edit'   => Pages\EditKitchen::route('/{record}/edit'),
        ];
    }
}
