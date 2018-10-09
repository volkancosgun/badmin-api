<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned()->default(0);
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

            // Ürün Id
            $table->integer('product_id')->unsigned()->default(0);
            $table->foreign('product_id')->references('id')->on('products');

            // Ürün Adı
            $table->string('name')->nullable();

            // Ürün Miktarı 
            $table->decimal('amount', 8,2)->default(0);

            // Ürün Birimi
            $table->enum('unit', ['DEFAULT', 'CARTON', 'PALETTE', 'CONTAINER'])->default('DEFAULT');

            // Ürün Fiyatı
            $table->decimal('price', 8,2)->default(0);

            // Ürün KDV
            $table->decimal('tax', 8,2)->default(0);

            // Ürün Toplam Fiyatı
            $table->decimal('total_price', 8,2)->default(0);

            // Durum
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
        Schema::dropIfExists('order_items');
    }
}
