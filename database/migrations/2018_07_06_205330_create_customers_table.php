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
            $table->enum('gender', ['male', 'female']);
            $table->integer('user_id')->unsigned()->default(0);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('group_id')->unsigned()->default(0);
            $table->foreign('group_id')->references('id')->on('customer_groups')->onDelete('cascade');
            $table->string('customer_number', 6)->nullable();
            $table->string('business_name')->nullable();
            $table->string('business_manager')->nullable();
            $table->string('name', 100);
            $table->string('sur_name', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_code', 10)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('phone_mobil_code', 10)->nullable();
            $table->string('phone_mobil', 50)->nullable();
            $table->string('fax_code', 10)->nullable();
            $table->string('fax', 50)->nullable();
            $table->string('tax')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('iban')->nullable();
            $table->string('bic')->nullable();
            $table->string('sepa')->nullable();
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
