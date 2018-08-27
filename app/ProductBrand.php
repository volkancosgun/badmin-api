<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductBrand extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'status'
    ];
}
