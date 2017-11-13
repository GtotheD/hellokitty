<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWidthHeightColumnTsStructuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_structures', function (Blueprint $table) {
            $table->unsignedSmallInteger('banner_width')->after('section_file_name');
            $table->unsignedSmallInteger('banner_height')->after('section_file_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ts_structures', function (Blueprint $table) {
            $table->dropColumn(['banner_width', 'banner_height']);
        });
    }
}
