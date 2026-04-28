<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\AddonGroupResource\Pages;
use App\Models\AddonGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AddonGroupResource extends Resource
{
    protected static ?string $model = AddonGroup::class;
    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';
    protected static ?string $navigationGroup = 'Food Management';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationLabel = 'Addons / Extras';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Addon Group')->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->placeholder('e.g. Extra Toppings, Sauces, Drinks'),
                Forms\Components\Toggle::make('is_required')
                    ->label('Required (customer must choose)')
                    ->default(false),
                Forms\Components\Toggle::make('is_multi_select')
                    ->label('Allow Multiple Selections')
                    ->default(true),
                Forms\Components\Toggle::make('is_active')->default(true),
            ])->columns(2),

            Forms\Components\Section::make('Addon Items')->schema([
                Forms\Components\Repeater::make('addons')
                    ->relationship()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->placeholder('e.g. Extra Cheese'),
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->prefix('৳')
                            ->default(0)
                            ->required(),
                        Forms\Components\Toggle::make('is_active')->default(true)->inline(false),
                    ])
                    ->columns(3)
                    ->addActionLabel('Add Addon Item')
                    ->defaultItems(1),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('addons_count')->counts('addons')->label('Items'),
                Tables\Columns\IconColumn::make('is_required')->boolean()->label('Required'),
                Tables\Columns\IconColumn::make('is_multi_select')->boolean()->label('Multi-Select'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAddonGroups::route('/'),
            'create' => Pages\CreateAddonGroup::route('/create'),
            'edit'   => Pages\EditAddonGroup::route('/{record}/edit'),
        ];
    }
}
