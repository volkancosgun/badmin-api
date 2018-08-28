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
        'carton_barcode',
        'palette_total',
        'palette_price',
        'palette_barcode',
        'container_total',
        'container_price',
        'container_barcode',
        'price',
        'status'
    ];
}
