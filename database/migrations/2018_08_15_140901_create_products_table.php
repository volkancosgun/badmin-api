<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->default(0);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('category_id')->unsigned()->default(0);
            $table->integer('brand_id')->unsigned()->nullable();
            
            $table->string('product_number', 6)->nullable();
            $table->string('name');
            $table->text('description')->nullable();

            // Son kullanma tarihi
            $table->string('expiration_at')->nullable();

            // Net ağırlık
            $table->decimal('n_weight', 8, 3)->nullable();

            // Brüt ağırlık
            $table->decimal('g_weight', 8, 3)->nullable();

            // Depozito ücreti
            $table->decimal('deposit_fee', 8, 2)->nullable();

            // Alış fiyatı
            $table->decimal('purchase_price', 8, 2)->nullable();

            // Karton
            $table->integer('carton_total')->nullable();
            $table->decimal('carton_price', 8, 2)->nullable();

            // Palet
            $table->integer('palette_total')->nullable();
            $table->decimal('palette_price', 8, 2)->nullable();

            // Konteynır
            $table->integer('container_total')->nullable();
            $table->decimal('container_price', 8,2)->nullable();
            
            // Birim fiyat
            $table->decimal('price', 8, 2)->nullable();

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
        Schema::dropIfExists('products');
    }
}
