<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTsWorksMsdbItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_works', function (Blueprint $table) {
            $table->string('msdb_item', 5)->after('work_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ts_works', function (Blueprint $table) {
            $table->dropColumn('msdb_item');
        });
    }
}
