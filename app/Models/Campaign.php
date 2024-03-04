<?php

namespace App\Models;

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
}
