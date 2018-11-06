<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToTsHimoReleaseOrdersMonthAndTapGenreId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_himo_release_orders', function (Blueprint $table) {
            $table->index(['month']);
            $table->index(['tap_genre_id']);
            $table->index(['month', 'tap_genre_id']);
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
            $table->dropIndex(['month']);
            $table->dropIndex(['tap_genre_id']);
            $table->dropIndex(['month', 'tap_genre_id']);
        });
    }
}
