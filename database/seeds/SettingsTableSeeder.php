<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $value = Config::get('balamir.seeder.settings');

        DB::table('settings')->insert([
            'sevdesk_status' => $value['sevdesk_status'],
            'sevdesk_apikey' => $value['sevdesk_apikey'],
            'sevdesk_userid' => $value['sevdesk_userid'],
            'sevdesk_fullname' => $value['sevdesk_fullname'],
            'sevdesk_email' => $value['sevdesk_email'],
        ]);
    }
}
