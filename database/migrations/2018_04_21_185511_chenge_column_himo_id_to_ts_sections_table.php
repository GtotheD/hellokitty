<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChengeColumnHimoIdToTsSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_sections', function (Blueprint $table) {
            $table->renameColumn('himo_id', 'work_id');
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
            $table->renameColumn('work_id', 'himo_id');
        });
    }
}
