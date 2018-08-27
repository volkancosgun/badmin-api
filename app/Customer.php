<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'user_id',
        'group_id',
        'customer_number',
        'business_name',
        'business_manager',
        'name',
        'sur_name',
        'description',
        'email',
        'phone_code',
        'phone',
        'phone_mobil_code',
        'phone_mobil',
        'fax_code',
        'fax',
        'tax',
        'tax_number',
        'iban',
        'bic',
        'sepa',
        'status'
    ];
}
