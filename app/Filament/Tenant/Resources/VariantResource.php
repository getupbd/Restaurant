<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\VariantResource\Pages;
use App\Models\Variant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VariantResource extends Resource
{
    protected static ?string $model = Variant::class;
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationGroup = 'Food Management';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = 'Food Variants';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->placeholder('e.g. Small, Medium, Large')
                ->maxLength(255),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVariants::route('/'),
            'create' => Pages\CreateVariant::route('/create'),
            'edit'   => Pages\EditVariant::route('/{record}/edit'),
        ];
    }
}
