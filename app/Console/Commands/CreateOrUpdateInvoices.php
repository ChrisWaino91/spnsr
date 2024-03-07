<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Supplier;
use Illuminate\Console\Command;

class CreateOrUpdateInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:createOrUpdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the invoice content for a given month for a supplier. Processed daily.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $suppliers = Supplier::all();

        foreach ($suppliers as $supplier) {
            $invoiceDate = Carbon::now()->startOfMonth();

            $invoice = Invoice::firstOrNew(
                [
                    'supplier_id' => $supplier->id,
                    'invoice_date' => $invoiceDate
                ]
            );

            if (!$invoice->exists) {
                $invoice->total_amount = 0;
            } else {
                $invoice->total_amount = $invoice->calculateTotalAmount();
            }

            $invoice->save();

            $this->info("Invoice for supplier {$supplier->id} for month {$invoiceDate->format('Y-m')} processed.");
        }

        // todo, calculate the total amount for each invoice once the month has passed and finalise the invoice

        $this->info('All invoices have been created or updated.');
    }
}
