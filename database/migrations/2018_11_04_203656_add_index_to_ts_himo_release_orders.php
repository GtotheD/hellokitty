<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToTsHimoReleaseOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_himo_release_orders', function (Blueprint $table) {
            $table->index(['month', 'tap_genre_id', 'work_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ts_himo_release_orders', function (Blueprint $table) {
            $table->dropIndex(['month', 'tap_genre_id', 'work_id']);
        });
    }
}
