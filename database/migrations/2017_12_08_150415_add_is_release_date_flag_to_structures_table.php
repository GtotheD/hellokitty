<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsReleaseDateFlagToStructuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_structures', function (Blueprint $table) {
            $table->unsignedTinyInteger('is_release_date')->after('is_ranking');
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
            $table->dropColumn([ 'is_release_date']);
        });
    }
}
