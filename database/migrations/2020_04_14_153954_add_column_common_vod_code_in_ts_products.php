<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnCommonVodCodeInTsProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('ts_products', 'common_vod_code')) {
            Schema::table('ts_products', function (Blueprint $table) {
                $table->string('common_vod_code', 15)->nullable()->default(null)->after('jan');
            });
        }
        if (!Scheam::hasColumn('ts_products', 'product_sub_title')) {
            Schema::table('ts_products', function (Blueprint $table) {
                $table->string('product_sub_title', 255)->nullable()->default(null)->after('product_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ts_products', function (Blueprint $table) {
            //
        });
    }
}
