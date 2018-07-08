<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    //
    protected $fillable = ['user_id', 'name', 'description', 'status'];
}
