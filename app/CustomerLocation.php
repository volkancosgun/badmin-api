<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerLocation extends Model
{
    protected $fillable = [
        'customer_id',
        'location_type',
        'description',
        'address',
        'city',
        'country',
        'lat',
        'lng',
        'locality',
        'place_id',
        'postal_code',
        'route',
        'street_number',
        'status',
    ];
}
