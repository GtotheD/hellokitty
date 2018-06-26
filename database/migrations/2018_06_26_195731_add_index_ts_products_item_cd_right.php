<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexTsProductsItemCdRight extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_products', function (Blueprint $table) {
            $table->string('ccc_family_cd',255)->change();
            $table->index(['ccc_family_cd', 'product_type_id', 'item_cd_right_2']);
            $table->index('item_cd_right_2');
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
            $table->dropIndex(['ccc_family_cd', 'product_type_id', 'item_cd_right_2']);
            $table->dropIndex('item_cd_right_2');
            $table->text('ccc_family_cd')->change();
        });
    }
}
