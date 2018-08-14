<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
//use Illuminate\Support\Facades\Config;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $value = Config::get('balamir.seeder.users');

        DB::table('users')->insert([
            'name' => $value['name'],
            'email' => $value['email'],
            'password' => Hash::make($value['password'])
        ]);
    }
}
