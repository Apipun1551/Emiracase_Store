<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParentIdAndTypeToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            //product tipe simple product maka parent id null
            $table->unsignedBigInteger('parent_id')->after('id')->nullable();
            $table->string('type')->after('sku');//devinisi tipe data
            //relasi parent id di table product reference dari id table products
            $table->foreign('parent_id')->references('id')->on('products');
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
            $table->dropForeign('products_parent_id_foreign');
            $table->dropColumn('parent_id');
            $table->dropColumn('type');
        });
    }
}
