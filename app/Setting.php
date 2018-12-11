<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'sevdesk_status',
        'sevdesk_apikey',
        'sevdesk_userid',
        'sevdesk_user',
    ];
}
