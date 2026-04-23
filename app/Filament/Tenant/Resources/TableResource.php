<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\TableResource\Pages;
use App\Filament\Tenant\Resources\TableResource\RelationManagers;
use App\Models\Table as TableModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Tables\Actions\Action;
use Illuminate\Support\HtmlString;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableResource extends Resource
{
    protected static ?string $model = TableModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('area_id')
                    ->relationship('area', 'name')
                    ->required()
                    ->preload(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('capacity')
                    ->numeric()
                    ->default(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('area.name')
                    ->badge(),
                Tables\Columns\TextColumn::make('capacity')
                    ->numeric(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('area_id')
                    ->relationship('area', 'name'),
            ])
            ->actions([
                Action::make('qrCode')
                    ->label('Print QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->color('primary')
                    ->modalHeading('Table Ordering QR')
                    ->modalWidth('md')
                    ->modalContent(fn ($record) => new HtmlString(
                        '<div class="flex flex-col items-center justify-center p-8 text-center bg-gray-50/50 rounded-[2rem]">
                            <div class="bg-white p-6 rounded-[2.5rem] shadow-2xl border border-gray-100 mb-6 group hover:scale-105 transition-transform duration-500">
                                ' . QrCode::size(240)->margin(1)->generate(url("/guest-order/{$record->id}")) . '
                            </div>
                            <h3 class="text-xl font-black uppercase tracking-tight text-gray-900 mb-2">'. $record->name .'</h3>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest leading-relaxed max-w-[200px] mb-4">
                                Scan this code to browse menu & order directly from table.
                            </p>
                            <div class="text-[10px] text-primary-600 font-black bg-primary-50 px-4 py-2 rounded-full border border-primary-100">
                                ' . str_replace(['http://', 'https://'], '', url("/guest-order/{$record->id}")) . '
                            </div>
                        </div>'
                    ))
                    ->modalSubmitActionLabel('Print Tag')
                    ->extraAttributes([
                        'onclick' => 'window.print()',
                    ]),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTables::route('/'),
            'create' => Pages\CreateTable::route('/create'),
            'edit' => Pages\EditTable::route('/{record}/edit'),
        ];
    }
}
