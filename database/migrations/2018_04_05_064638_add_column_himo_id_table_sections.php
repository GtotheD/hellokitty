<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnHimoIdTableSections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_sections', function (Blueprint $table) {
            $table->text('himo_id', 255)->after('url_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ts_sections', function (Blueprint $table) {
            $table->dropColumn([ 'himo_id']);
        });
    }
}
