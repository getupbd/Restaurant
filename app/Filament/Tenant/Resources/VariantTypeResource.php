<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\VariantTypeResource\Pages;
use App\Filament\Tenant\Resources\VariantTypeResource\RelationManagers;
use App\Models\VariantType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VariantTypeResource extends Resource
{
    protected static ?string $model = VariantType::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';
    protected static ?string $navigationGroup = 'Catalog';
    protected static ?string $navigationLabel = 'Variants';
    protected static ?string $modelLabel = 'Variant';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Variant Type Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Size, Color, Material'),
                        Forms\Components\Select::make('input_type')
                            ->options([
                                'dropdown' => 'Dropdown / Select',
                                'button' => 'Radio Buttons',
                                'color_picker' => 'Color Picker',
                            ])
                            ->required()
                            ->live()
                            ->default('dropdown'),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Variant Options')
                    ->description('Add the choices for this variant (e.g., Small, Medium, Large or Red, Blue)')
                    ->schema([
                        Forms\Components\Repeater::make('options')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->placeholder('e.g. Small, Red'),
                                Forms\Components\TextInput::make('value')
                                    ->type(fn (\Filament\Forms\Get $get) => $get('../../input_type') === 'color_picker' ? 'color' : 'text')
                                    ->placeholder('e.g. #FF0000 or SM')
                                    ->hint('Optional: used for color codes or short values'),
                                Forms\Components\Toggle::make('is_active')
                                    ->default(true),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->addActionLabel('Add Option')
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('input_type')
                    ->badge()
                    ->colors([
                        'primary' => 'dropdown',
                        'success' => 'button',
                        'warning' => 'color_picker',
                    ]),
                Tables\Columns\TextColumn::make('options_count')
                    ->counts('options')
                    ->label('Total Options'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVariantTypes::route('/'),
            'create' => Pages\CreateVariantType::route('/create'),
            'edit' => Pages\EditVariantType::route('/{record}/edit'),
        ];
    }
}
