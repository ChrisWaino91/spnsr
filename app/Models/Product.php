<?php

namespace App\Models;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
        'brand_id' => 'integer',
        'price' => 'decimal:3',
        'sale_price' => 'decimal:3',
        'rrp_price' => 'decimal:3',
        'images' => 'array',
        'category_id' => 'integer',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class);
    }

    public function getThumbnailImageUrlAttribute()
    {
        return $this->images['thumbnail'] ?? asset('/images/products/no-image-thumb.jpg');
    }

    public function getMediumImageUrlAttribute()
    {
        return $this->images['medium'] ?? asset('/images/products/no-image-medium.jpg');
    }
}
