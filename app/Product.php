<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'brand_id',
        'product_number',
        'code',
        'name',
        'description',
        'expiration_at',
        'n_weight',
        'g_weight',
        'deposit_fee',
        'purchase_price',
        'carton_total',
        'carton_price',
        'palette_total',
        'palette_price',
        'container_total',
        'container_price',
        'status'
    ];
}
