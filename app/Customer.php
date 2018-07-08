<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['user_id', 'group_id', 'customer_number', 'name', 'sur_name', 'description', 'email', 'phone', 'phone_mobil', 'fax', 'adr_address', 'adr_city', 'adr_country', 'adr_lat', 'adr_lng', 'adr_locality', 'adr-place_id', 'adr_postal_code', 'adr_route', 'adr_street_number', 'status'];
}
