<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->default(0);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('group_id')->unsigned()->default(0);
            $table->foreign('group_id')->references('id')->on('customer_groups')->onDelete('cascade');
            $table->string('customer_number', 6);
            $table->string('name', 100);
            $table->string('sur_name', 100);
            $table->text('description')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('phone_mobil', 50)->nullable();
            $table->string('fax', 50)->nullable();
            $table->string('adr_address')->nullable();
            $table->string('adr_city', 100)->nullable();
            $table->string('adr_country', 100)->nullable();
            $table->decimal('adr_lat', 10, 8)->nullable();
            $table->decimal('adr_lng', 11, 8)->nullable();
            $table->string('adr_locality', 100)->nullable();
            $table->string('adr_place_id')->nullable();
            $table->string('adr_postal_code', 50)->nullable();
            $table->string('adr_route', 100)->nullable();
            $table->string('adr_street_number', 50)->nullable();
            $table->tinyInteger('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
