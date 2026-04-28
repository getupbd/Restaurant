<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\ReservationResource\Pages;
use App\Models\Reservation;
use App\Models\Table as RestaurantTable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Reservations';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Booking List';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Guest Information')->schema([
                Forms\Components\TextInput::make('customer_name')
                    ->label('Guest Name')
                    ->required(),
                Forms\Components\TextInput::make('customer_phone')
                    ->label('Phone')
                    ->tel()
                    ->required(),
                Forms\Components\TextInput::make('customer_email')
                    ->label('Email')
                    ->email()
                    ->nullable(),
                Forms\Components\TextInput::make('party_size')
                    ->label('Party Size')
                    ->numeric()
                    ->required()
                    ->minValue(1),
            ])->columns(2),

            Forms\Components\Section::make('Booking Details')->schema([
                Forms\Components\DatePicker::make('booking_date')
                    ->label('Date')
                    ->required()
                    ->minDate(now()),
                Forms\Components\TimePicker::make('booking_time')
                    ->label('Time')
                    ->required()
                    ->seconds(false),
                Forms\Components\Select::make('table_id')
                    ->label('Assign Table')
                    ->options(RestaurantTable::pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                Forms\Components\Select::make('status')
                    ->options(Reservation::statusOptions())
                    ->default('pending')
                    ->required(),
                Forms\Components\Textarea::make('special_request')
                    ->label('Guest Special Request')
                    ->rows(2)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('notes')
                    ->label('Internal Staff Notes')
                    ->rows(2)
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_ref')->label('Ref #')->searchable(),
                Tables\Columns\TextColumn::make('customer_name')->label('Guest')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('customer_phone')->label('Phone'),
                Tables\Columns\TextColumn::make('booking_date')->date()->sortable()->label('Date'),
                Tables\Columns\TextColumn::make('booking_time')->time('H:i')->label('Time'),
                Tables\Columns\TextColumn::make('party_size')->label('Guests')->suffix(' pax'),
                Tables\Columns\TextColumn::make('table.name')->label('Table')->placeholder('—'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'primary' => 'seated',
                        'gray'    => 'completed',
                        'danger'  => fn($state) => in_array($state, ['cancelled', 'no_show']),
                    ]),
            ])
            ->defaultSort('booking_date')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Reservation::statusOptions()),
                Tables\Filters\Filter::make('booking_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From'),
                        Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q, $d) => $q->whereDate('booking_date', '>=', $d))
                            ->when($data['until'], fn($q, $d) => $q->whereDate('booking_date', '<=', $d));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->label('Confirm')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn(Reservation $r) => $r->status === 'pending')
                    ->action(fn(Reservation $r) => $r->update(['status' => 'confirmed'])),
                Tables\Actions\Action::make('seat')
                    ->label('Seat Guest')
                    ->icon('heroicon-o-user')
                    ->color('primary')
                    ->visible(fn(Reservation $r) => $r->status === 'confirmed')
                    ->action(fn(Reservation $r) => $r->update(['status' => 'seated'])),
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn(Reservation $r) => !in_array($r->status, ['cancelled', 'completed']))
                    ->action(fn(Reservation $r) => $r->update(['status' => 'cancelled'])),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit'   => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}
