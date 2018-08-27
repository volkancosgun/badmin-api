<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $fillable = [
        'parent_id',
        'user_id',
        'name',
        'description',
        'status'
    ]; 
}
