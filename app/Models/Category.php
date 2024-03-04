<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
        'api_id' => 'integer',
        'level' => 'integer',
        'parent_id' => 'integer',
        'cost_per_click' => 'decimal:2',
    ];

    public function promotion(): HasOne
    {
        return $this->hasOne(Promotion::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
