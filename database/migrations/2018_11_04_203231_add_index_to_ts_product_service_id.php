<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToTsProductServiceId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_products', function (Blueprint $table) {
            $table->string('service_id',10)->change();
            $table->index(['service_id']);
            $table->index(['ccc_family_cd', 'sale_start_date', 'item_cd', 'item_cd_right_2', 'product_type_id', 'service_id'], 'ts_products_all_index');
            $table->index(['ccc_family_cd', 'sale_start_date', 'item_cd_right_2', 'product_type_id', 'service_id'], 'ts_products_all_index2');
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
            $table->dropIndex(['service_id']);
            $table->dropIndex('ts_products_all_index');
            $table->dropIndex('ts_products_all_index2');
        });
        Schema::table('ts_products', function (Blueprint $table) {
            $table->text('service_id')->change();
        });
    }
}
