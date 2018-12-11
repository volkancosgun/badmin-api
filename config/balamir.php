<?php
return [

    // TPanel Varsayılan DB Verileri
    'seeder' => [
        'users' => [
            'name' => 'Tpanel Admin',
            'email' => 'demo@tpanel.de',
            'password' => 'adw123adw'
        ],
        'customer_groups' => [
            'user_id' => 1,
            'name' => 'Varsayılan',
            'description' => 'Varsayılan müşteri grubu',
        ],
        'settings' => [
            'sevdesk_status' => 0,
            'sevdesk_apikey' => NULL,
            'sevdesk_userid' => NULL,
            'sevdesk_fullname' => NULL,
            'sevdesk_email' => NULL
        ]
    ]


];