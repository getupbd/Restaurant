<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\UnavailableDayResource\Pages;
use App\Models\UnavailableDay;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UnavailableDayResource extends Resource
{
    protected static ?string $model = UnavailableDay::class;
    protected static ?string $navigationIcon = 'heroicon-o-no-symbol';
    protected static ?string $navigationGroup = 'Reservations';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Unavailable Days';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\DatePicker::make('date')
                ->required()
                ->minDate(now())
                ->label('Blocked Date'),
            Forms\Components\TextInput::make('reason')
                ->label('Reason')
                ->placeholder('Public Holiday, Private Event, Staff Training...')
                ->nullable(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')->date('D, d M Y')->sortable(),
                Tables\Columns\TextColumn::make('reason')->placeholder('No reason given'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date')
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUnavailableDays::route('/'),
            'create' => Pages\CreateUnavailableDay::route('/create'),
            'edit'   => Pages\EditUnavailableDay::route('/{record}/edit'),
        ];
    }
}
