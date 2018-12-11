<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->default(0);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('customer_id')->unsigned()->default(0);
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->integer('location_id')->unsigned()->default(0);
            $table->foreign('location_id')->references('id')->on('customer_locations');
            $table->string('bill_address')->nullable();
            $table->string('bill_number')->nullable();
            $table->string('order_number', 6)->nullable();
            $table->string('delivered_date')->nullable();
            $table->decimal('price', 8, 2)->default(0);
            $table->decimal('tax_price', 8, 2)->default(0);
            $table->decimal('total_price', 8, 2)->default(0);
            $table->integer('sevdesk_order_id')->default(0);
            $table->string('sevdesk_doc_id')->nullable();
            $table->string('sevdesk_doc_pdf')->nullable();
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('orders');
    }
}
