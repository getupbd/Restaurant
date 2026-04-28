<?php

namespace App\Filament\Tenant\Pages;

use App\Models\ReservationSetting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ReservationSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Reservations';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = 'Reservation Settings';
    protected static string $view = 'filament.tenant.pages.reservation-settings';
    protected static ?string $title = 'Reservation Settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = ReservationSetting::current();
        $this->form->fill([
            'advance_booking_days'  => $settings->advance_booking_days,
            'max_party_size'        => $settings->max_party_size,
            'slot_duration_minutes' => $settings->slot_duration_minutes,
            'auto_confirm'          => $settings->auto_confirm,
            'allow_online_booking'  => $settings->allow_online_booking,
            'time_slots'            => implode(', ', $settings->time_slots ?? []),
            'blocked_days_of_week'  => $settings->blocked_days_of_week ?? [],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Booking Configuration')->schema([
                Forms\Components\TextInput::make('advance_booking_days')
                    ->label('Advance Booking (Days)')
                    ->numeric()
                    ->required()
                    ->helperText('How many days in advance can a guest book?'),
                Forms\Components\TextInput::make('max_party_size')
                    ->label('Max Party Size')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('slot_duration_minutes')
                    ->label('Time Slot Duration (minutes)')
                    ->numeric()
                    ->required(),
                Forms\Components\Toggle::make('auto_confirm')
                    ->label('Auto-Confirm Bookings')
                    ->helperText('If enabled, bookings are confirmed immediately without manual approval.'),
                Forms\Components\Toggle::make('allow_online_booking')
                    ->label('Allow Online Bookings'),
            ])->columns(2),

            Forms\Components\Section::make('Time Slots & Schedule')->schema([
                Forms\Components\TextInput::make('time_slots')
                    ->label('Available Time Slots')
                    ->helperText('Comma-separated times in HH:MM format. e.g., 12:00, 13:00, 18:00, 19:30')
                    ->columnSpanFull(),
                Forms\Components\CheckboxList::make('blocked_days_of_week')
                    ->label('Closed Days of the Week')
                    ->options([
                        0 => 'Sunday',
                        1 => 'Monday',
                        2 => 'Tuesday',
                        3 => 'Wednesday',
                        4 => 'Thursday',
                        5 => 'Friday',
                        6 => 'Saturday',
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
            ]),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $settings = ReservationSetting::current();
        $settings->update([
            'advance_booking_days'  => $data['advance_booking_days'],
            'max_party_size'        => $data['max_party_size'],
            'slot_duration_minutes' => $data['slot_duration_minutes'],
            'auto_confirm'          => $data['auto_confirm'] ?? false,
            'allow_online_booking'  => $data['allow_online_booking'] ?? true,
            'time_slots'            => array_map('trim', explode(',', $data['time_slots'])),
            'blocked_days_of_week'  => $data['blocked_days_of_week'] ?? [],
        ]);

        Notification::make()->title('Settings saved successfully!')->success()->send();
    }
}
