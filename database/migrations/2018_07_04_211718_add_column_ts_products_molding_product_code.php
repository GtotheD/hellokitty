<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTsProductsMoldingProductCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_products', function (Blueprint $table) {
            $table->string('base_product_code', 255)->after('product_code');
            $table->tinyInteger('is_dummy')->after('base_product_code');
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
            $table->dropColumn('base_product_code');
            $table->dropColumn('is_dummy');
        });
    }
}
