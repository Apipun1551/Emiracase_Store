<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_inventories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            //dihapus agar bisa dijalankan seeder untuk roles&permission
            //$table->unsignedBigInteger('product_attribute_value_id');
            $table->integer('qty');
            $table->timestamps();

            //ketika produk terhapus maka data di inventori di hapus
            $table -> foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            //dihapus agar bisa dijalankan seeder untuk roles&permission
            //$table -> foreign('product_attribute_value_id')->references('id')->on('product_attribute_values')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_inventories');
    }
}
