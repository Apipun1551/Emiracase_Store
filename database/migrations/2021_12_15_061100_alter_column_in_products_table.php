<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnInProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            //Mengaktifkan null field di table products
            $table->decimal('width',15,2)->nullable()->change();
            $table->decimal('height',15,2)->nullable()->change();
            $table->decimal('length',15,2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //mengembalikan yang di atas
            $table->decimal('width',15,2)->nullable(false)->change();
            $table->decimal('height',15,2)->nullable(false)->change();
            $table->decimal('length',15,2)->nullable(false)->change();
        });
    }
}
