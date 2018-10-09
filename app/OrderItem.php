<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'id',
        'order_id',
        'product_id',
        'name',
        'amount',
        'unit',
        'price',
        'tax',
        'total_price',
        'status'
    ];
}
