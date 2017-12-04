<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTsBannerTableDispDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_banners', function (Blueprint $table) {
            $table->dateTime('display_start_date')->after('image_url');
            $table->dateTime('display_end_date')->after('display_start_date');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ts_banners', function (Blueprint $table) {
            $table->dropColumn([ 'display_start_date']);
            $table->dropColumn([ 'display_end_date']);
        });
    }
}
