<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToTsProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_products', function (Blueprint $table) {
            $table->string('product_unique_id',255)->change();
            $table->string('work_id',255)->change();
            $table->string('rental_product_cd',255)->change();
            $table->string('product_type_id',255)->change();
            $table->string('item_cd',255)->change();
            $table->string('jan',255)->change();
            $table->index('product_unique_id');
            $table->index('work_id');
            $table->index(['work_id','rental_product_cd']);
            $table->index(['rental_product_cd','product_type_id', 'item_cd']);
            $table->index(['rental_product_cd','product_type_id']);
            $table->index(['jan','rental_product_cd']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ts_products', function (Blueprint $table) {
            $table->text('product_unique_id')->change();
            $table->text('work_id')->change();
            $table->text('rental_product_cd')->change();
            $table->text('product_type_id')->change();
            $table->text('item_cd')->change();
            $table->text('jan')->change();
            $table->dropIndex('product_unique_id');
            $table->dropIndex('work_id');
            $table->dropIndex(['work_id','rental_product_cd']);
            $table->dropIndex(['rental_product_cd','product_type_id', 'item_cd']);
            $table->dropIndex(['rental_product_cd','product_type_id']);
            $table->dropIndex(['jan','rental_product_cd']);
        });
    }
}
