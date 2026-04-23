<?php

namespace App\Filament\Tenant\Pages;

use App\Models\Invoice;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class Billing extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static string $view = 'filament.tenant.pages.billing';

    protected static ?string $title = 'Billing & Subscription';

    public $invoices = [];

    public function mount()
    {
        $this->loadInvoices();
    }

    public function loadInvoices()
    {
        $this->invoices = Invoice::where('tenant_id', tenant('id'))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function payMock($invoiceId)
    {
        $invoice = Invoice::find($invoiceId);
        
        if ($invoice) {
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            $tenant = tenant();
            $tenant->update([
                'trial_ends_at' => now()->addMonth(),
                'is_active' => true,
            ]);

            Notification::make()
                ->title('Payment Successful (Mock)')
                ->success()
                ->send();

            $this->loadInvoices();
            
            return redirect()->to(Page::getUrl(panel: 'tenant'));
        }
    }
}
