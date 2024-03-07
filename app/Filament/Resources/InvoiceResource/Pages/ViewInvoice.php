<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use Filament\Actions;
use App\Models\Campaign;
use Filament\Tables\Contracts\HasTable;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\InvoiceResource;
use Filament\Tables\Concerns\InteractsWithTable;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected static string $view = 'filament.resources.invoice.pages.view';

    public $campaigns;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        $invoiceMonthStart = $this->record->invoice_date->copy()->startOfMonth();
        $invoiceMonthEnd = $this->record->invoice_date->copy()->endOfMonth();

        $this->campaigns = Campaign::where('supplier_id', $this->record->supplier_id)
        ->whereHas('promotions.clicks', function ($query) use ($invoiceMonthStart, $invoiceMonthEnd) {
            $query->whereBetween('created_at', [$invoiceMonthStart, $invoiceMonthEnd]);
        })
        ->with(['promotions' => function ($query) use ($invoiceMonthStart, $invoiceMonthEnd) {
            $query->whereHas('clicks', function ($q) use ($invoiceMonthStart, $invoiceMonthEnd) {
                $q->whereBetween('created_at', [$invoiceMonthStart, $invoiceMonthEnd]);
            });
        }, 'promotions.clicks' => function ($query) use ($invoiceMonthStart, $invoiceMonthEnd) {
            $query->whereBetween('created_at', [$invoiceMonthStart, $invoiceMonthEnd]);
        },
            'promotions.impressions' => function ($query) use ($invoiceMonthStart, $invoiceMonthEnd) {
            $query->whereBetween('created_at', [$invoiceMonthStart, $invoiceMonthEnd]);
        }
        ])
        ->get();
    }

}
