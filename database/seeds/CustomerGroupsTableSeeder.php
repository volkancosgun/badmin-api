<?php

use Illuminate\Database\Seeder;

class CustomerGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $value = Config::get('balamir.seeder.customer_groups');

        DB::table('customer_groups')->insert([
            'user_id' => $value['user_id'],
            'name' => $value['name'],
            'description' => $value['description'],
            'status' => 1
        ]);
    }
}
