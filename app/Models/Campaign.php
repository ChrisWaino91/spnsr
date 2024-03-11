<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campaign extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'budget' => 'decimal:2',
        'supplier_id' => 'integer',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class);
    }

    public function url(): string
    {
        return route('filament.admin.resources.campaigns.edit', $this);
    }

    public function getTotalClicksAttribute()
    {
        return DB::table('campaigns')
            ->join('promotions', 'campaigns.id', '=', 'promotions.campaign_id')
            ->join('clicks', 'promotions.id', '=', 'clicks.promotion_id')
            ->where('campaigns.id', $this->id)
            ->count();
    }

    public function getTotalImpressionsAttribute()
    {
        return DB::table('campaigns')
            ->join('promotions', 'campaigns.id', '=', 'promotions.campaign_id')
            ->join('impressions', 'promotions.id', '=', 'impressions.promotion_id')
            ->where('campaigns.id', $this->id)
            ->count();
    }

    public function getTotalOrdersAttribute()
    {
        return DB::table('campaigns')
            ->join('promotions', 'campaigns.id', '=', 'promotions.campaign_id')
            ->join('orders', 'promotions.id', '=', 'orders.promotion_id')
            ->where('campaigns.id', $this->id)
            ->count();
    }

    public function spend($month = null)
    {
        $totalSpend = 0.00;

        $this->promotions->each(function ($promotion) use (&$totalSpend, $month) {
            $clicks = $promotion->clicks;
            if ($month) {
                $startOfMonth = $month->copy()->startOfMonth();
                $endOfMonth = $month->copy()->endOfMonth();

                $clicks = $clicks->filter(function ($click) use ($startOfMonth, $endOfMonth) {
                    $clickDate = Carbon::parse($click->created_at);
                    return $clickDate->between($startOfMonth, $endOfMonth);
                });
            }

            $clicksCount = $clicks->count();
            $totalSpend += $clicksCount * $promotion->cost_per_click;
        });

        return number_format($totalSpend, 2);
    }
}
