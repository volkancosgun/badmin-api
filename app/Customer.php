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
        'phone',
        'phone_lang',
        'phone_mobil',
        'phone_mobil_lang',
        'fax',
        'fax_lang',
        'tax',
        'tax_number',
        'iban',
        'bic',
        'sepa',
        'status'
    ];
}
