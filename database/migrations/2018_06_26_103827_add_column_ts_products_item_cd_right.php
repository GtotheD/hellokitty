<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddColumnTsProductsItemCdRight extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_products', function (Blueprint $table) {
            $table->string('item_cd_right_2', 2)->after('item_cd');
        });
        // テーブルのアップデート
        DB::table('ts_products')->update(['item_cd_right_2' => DB::raw('RIGHT(item_cd, 2)')]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ts_products', function (Blueprint $table) {
            $table->dropColumn('item_cd_right_2');
        });
    }
}
