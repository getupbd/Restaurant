<?php

namespace App\Filament\Tenant\Resources\OrderResource\Pages;

use App\Filament\Tenant\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('mark_paid')
                ->label('Mark as Paid')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn() => $this->record->payment_status !== 'paid')
                ->action(function () {
                    $this->record->update([
                        'payment_status' => 'paid',
                        'status'         => 'paid',
                        'is_paid'        => true,
                        'paid_at'        => now(),
                    ]);
                    $this->redirect($this->getResource()::getUrl('index'));
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
