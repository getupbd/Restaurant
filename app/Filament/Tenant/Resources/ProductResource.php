<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cake';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General Info')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->preload()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state)))
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->required()
                            ->prefix(fn () => tenant('currency_symbol'))
                            ->columnSpan(1),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->disk('public')
                            ->directory('products/tenants/' . tenant('id'))
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false),
                    ])->columns(2),

                Forms\Components\Section::make('Inventory')
                    ->schema([
                        Forms\Components\Toggle::make('track_stock')
                            ->label('Track Inventory')
                            ->live()
                            ->inline(false),
                        Forms\Components\TextInput::make('stock_quantity')
                            ->label('Current Stock')
                            ->numeric()
                            ->default(0)
                            ->visible(fn ($get) => $get('track_stock')),
                        Forms\Components\TextInput::make('min_stock_level')
                            ->label('Low Stock Threshold')
                            ->numeric()
                            ->default(5)
                            ->visible(fn ($get) => $get('track_stock')),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->description(fn ($record) => $record->category?->name),
                Tables\Columns\TextColumn::make('price')
                    ->money(fn () => tenant('currency'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($record): string =>
                        $record->track_stock && $record->stock_quantity <= $record->min_stock_level ? 'danger' : 'success'
                    )
                    ->icon(fn ($record): ?string =>
                        $record->track_stock && $record->stock_quantity <= $record->min_stock_level ? 'heroicon-m-exclamation-triangle' : null
                    ),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('track_stock')
                    ->label('Track Inventory')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
                Tables\Filters\TernaryFilter::make('track_stock')
                    ->label('Inventory Tracking')
                    ->trueLabel('Tracked only')
                    ->falseLabel('Untracked only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modalWidth('4xl'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Set Active')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Set Inactive')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('enable_tracking')
                        ->label('Enable Inventory Tracking')
                        ->icon('heroicon-o-archive-box')
                        ->color('info')
                        ->action(fn ($records) => $records->each->update(['track_stock' => true]))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('disable_tracking')
                        ->label('Disable Inventory Tracking')
                        ->icon('heroicon-o-archive-box-x-mark')
                        ->color('gray')
                        ->action(fn ($records) => $records->each->update(['track_stock' => false]))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
        ];
    }
}
