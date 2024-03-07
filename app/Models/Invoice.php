<?php

namespace App\Models;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $casts = [
        'invoice_date' => 'datetime:Y-m-d',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function calculateTotalAmount()
    {
        $invoiceDate = $this->invoice_date;
        $monthEnd = $invoiceDate->copy()->endOfMonth();
        $supplierId = $this->supplier_id;

        $totalAmount = \App\Models\Promotion::whereHas('campaign.supplier', function ($query) use ($supplierId) {
            $query->where('id', $supplierId);
        })->whereHas('clicks', function ($query) use ($invoiceDate, $monthEnd) {
            $query->whereBetween('created_at', [$invoiceDate, $monthEnd]);
        })->with(['clicks' => function ($query) use ($invoiceDate, $monthEnd) {
            $query->whereBetween('created_at', [$invoiceDate, $monthEnd]);
        }])->get()->sum(function ($promotion) {
            return $promotion->clicks->count() * $promotion->cost_per_click;
        });

        return $totalAmount;
    }

}
