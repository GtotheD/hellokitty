<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTsSectionsTmpTextAmdSubtitle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_sections_tmp', function (Blueprint $table) {
            $table->json('data')->after('supplement');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ts_sections_tmp', function (Blueprint $table) {
            $table->dropColumn('data');
        });
    }
}
