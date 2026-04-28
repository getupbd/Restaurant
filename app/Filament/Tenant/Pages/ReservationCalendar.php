<?php

namespace App\Filament\Tenant\Pages;

use App\Models\Reservation;
use App\Models\UnavailableDay;
use Carbon\Carbon;
use Filament\Pages\Page;

class ReservationCalendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Reservations';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Reservation Dashboard';
    protected static string $view = 'filament.tenant.pages.reservation-calendar';
    protected static ?string $title = 'Reservation Calendar';

    public int $year;
    public int $month;

    public array $calendarDays = [];
    public array $todayStats   = [];

    public function mount(): void
    {
        $this->year  = now()->year;
        $this->month = now()->month;
        $this->buildCalendar();
    }

    public function previousMonth(): void
    {
        $date = Carbon::createFromDate($this->year, $this->month, 1)->subMonth();
        $this->year  = $date->year;
        $this->month = $date->month;
        $this->buildCalendar();
    }

    public function nextMonth(): void
    {
        $date = Carbon::createFromDate($this->year, $this->month, 1)->addMonth();
        $this->year  = $date->year;
        $this->month = $date->month;
        $this->buildCalendar();
    }

    protected function buildCalendar(): void
    {
        $start = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        // Get reservations for this month
        $reservations = Reservation::whereBetween('booking_date', [$start, $end])
            ->get()
            ->groupBy(fn($r) => $r->booking_date->format('Y-m-d'));

        // Get unavailable days for this month
        $blockedDates = UnavailableDay::whereBetween('date', [$start, $end])
            ->pluck('reason', 'date')
            ->mapWithKeys(fn($reason, $date) => [Carbon::parse($date)->format('Y-m-d') => $reason])
            ->toArray();

        $days = [];
        $current = $start->copy()->startOfWeek(Carbon::SUNDAY);
        $calendarEnd = $end->copy()->endOfWeek(Carbon::SATURDAY);

        while ($current->lte($calendarEnd)) {
            $dateKey = $current->format('Y-m-d');
            $dayReservations = $reservations[$dateKey] ?? collect();
            $days[] = [
                'date'           => $dateKey,
                'day'            => $current->day,
                'is_current_month' => $current->month === $this->month,
                'is_today'       => $current->isToday(),
                'is_blocked'     => isset($blockedDates[$dateKey]),
                'block_reason'   => $blockedDates[$dateKey] ?? null,
                'reservation_count' => $dayReservations->count(),
                'pending_count'  => $dayReservations->where('status', 'pending')->count(),
                'confirmed_count'=> $dayReservations->where('status', 'confirmed')->count(),
            ];
            $current->addDay();
        }

        $this->calendarDays = $days;

        // Today's stats
        $today = now()->format('Y-m-d');
        $todayRes = $reservations[$today] ?? collect();
        $this->todayStats = [
            'total'     => $todayRes->count(),
            'pending'   => $todayRes->where('status', 'pending')->count(),
            'confirmed' => $todayRes->where('status', 'confirmed')->count(),
            'seated'    => $todayRes->where('status', 'seated')->count(),
        ];
    }
}
