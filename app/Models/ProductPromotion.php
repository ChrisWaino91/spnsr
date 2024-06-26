<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPromotion extends Model
{
    use SoftDeletes;

    protected $table = 'product_promotion';

    protected $guarded = [];
}
