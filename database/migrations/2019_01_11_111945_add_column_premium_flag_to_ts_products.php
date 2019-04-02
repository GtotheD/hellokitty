<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnPremiumFlagToTsProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_products', function (Blueprint $table) {
            $table->tinyInteger('is_premium_shop')->after('media_format_id');
            $table->tinyInteger('is_premium_net')->after('is_premium_shop');
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
            $table->dropColumn('is_premium_shop');
            $table->dropColumn('is_premium_net');
        });
    }
}
