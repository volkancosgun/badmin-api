<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    protected $fillable = [
        'brand_id',
        'name',
        'status'
    ];
}
