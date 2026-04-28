<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\ProductResource\Pages;
use App\Models\AddonGroup;
use App\Models\Category;
use App\Models\Kitchen;
use App\Models\Product;
use App\Models\Variant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cake';
    protected static ?string $navigationGroup = 'Food Management';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Food Items';

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Basic Information')->schema([
                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->options(Category::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('kitchen_id')
                    ->label('Kitchen Station')
                    ->options(Kitchen::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('code')
                    ->label('Food Code')
                    ->placeholder('e.g. BURGER-001'),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('৳')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->label('Kitchen Notes')
                    ->rows(2)
                    ->placeholder('Special preparation instructions for kitchen...'),
                Forms\Components\Textarea::make('description')
                    ->rows(3),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->directory('products')
                    ->imageEditor()
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Variants (Sizes/Portions)')->schema([
                Forms\Components\Repeater::make('variants')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('variant_id')
                            ->label('Variant')
                            ->options(Variant::where('is_active', true)->pluck('name', 'id'))
                            ->required(),
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->prefix('৳')
                            ->required(),
                        Forms\Components\Toggle::make('is_active')->default(true),
                    ])
                    ->columns(3)
                    ->addActionLabel('Add Variant')
                    ->defaultItems(0),
            ]),

            Forms\Components\Section::make('Addon Groups')->schema([
                Forms\Components\CheckboxList::make('addonGroups')
                    ->relationship('addonGroups', 'name')
                    ->columns(3)
                    ->label('Attach Addon Groups'),
            ]),

            Forms\Components\Section::make('Inventory & Production')->schema([
                Forms\Components\TextInput::make('storage_unit')
                    ->label('Storage Unit')
                    ->placeholder('kg, pcs, litre...'),
                Forms\Components\TextInput::make('conversion_qty')
                    ->label('Conversion Qty')
                    ->numeric()
                    ->placeholder('e.g. 4 (1 kg = 4 portions)'),
                Forms\Components\Toggle::make('is_stock_validate')
                    ->label('Validate Stock Before Sale'),
                Forms\Components\Toggle::make('without_production')
                    ->label('Without Production'),
                Forms\Components\Toggle::make('add_as_ingredient')
                    ->label('Can Be Used As Ingredient'),
            ])->columns(3),

            Forms\Components\Section::make('Marketing & Sales')->schema([
                Forms\Components\TextInput::make('offer_rate')
                    ->numeric()
                    ->suffix('%')
                    ->label('Offer Rate'),
                Forms\Components\DatePicker::make('offer_start_date')->label('Offer Start'),
                Forms\Components\DatePicker::make('offer_end_date')
                    ->label('Offer End')
                    ->afterOrEqual('offer_start_date'),
                Forms\Components\TextInput::make('vat_rate')
                    ->numeric()
                    ->suffix('%')
                    ->default(0)
                    ->label('VAT Rate'),
                Forms\Components\TextInput::make('cooking_time')
                    ->numeric()
                    ->suffix('mins')
                    ->label('Cooking Time'),
                Forms\Components\TextInput::make('position')
                    ->numeric()
                    ->default(0)
                    ->label('Display Position'),
                Forms\Components\Toggle::make('is_special')->label("Chef's Special"),
                Forms\Components\Toggle::make('allow_custom_qty')->label('Allow Custom Quantity'),
                Forms\Components\Toggle::make('is_visible_on_web')->label('Visible on Website')->default(true),
                Forms\Components\Toggle::make('is_price_editable')->label('Price Editable at POS'),
                Forms\Components\Toggle::make('is_active')->label('Active')->default(true),
            ])->columns(3),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->circular(),
                Tables\Columns\TextColumn::make('code')->label('Code')->placeholder('—'),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category.name')->label('Category')->badge(),
                Tables\Columns\TextColumn::make('kitchen.name')->label('Kitchen')->placeholder('—'),
                Tables\Columns\TextColumn::make('price')->money('BDT')->sortable(),
                Tables\Columns\TextColumn::make('vat_rate')->suffix('%')->label('VAT'),
                Tables\Columns\IconColumn::make('is_special')->boolean()->label('Special'),
                Tables\Columns\IconColumn::make('is_visible_on_web')->boolean()->label('Web'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
                Tables\Columns\TextColumn::make('position')->sortable()->label('Order'),
            ])
            ->defaultSort('position')
            ->reorderable('position')
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(Category::pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('kitchen_id')
                    ->label('Kitchen')
                    ->options(Kitchen::pluck('name', 'id')),
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
                Tables\Filters\TernaryFilter::make('is_special')->label('Special'),
                Tables\Filters\TernaryFilter::make('is_visible_on_web')->label('On Web'),
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
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
