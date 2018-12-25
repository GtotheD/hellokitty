<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToTsProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_products', function (Blueprint $table) {
            $table->string('product_name', 510)->change();
            $table->index(['product_name', 'product_type_id', 'item_cd_right_2'], 'index_product_list');
            $table->index(['rental_product_cd']);
            $table->index(['service_id']);
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
            $table->dropIndex('index_product_list');
            $table->dropIndex(['rental_product_cd']);
            $table->dropIndex(['service_id']);
        });
        Schema::table('ts_products', function (Blueprint $table) {
            $table->text('product_name')->change();
        });
    }
}
