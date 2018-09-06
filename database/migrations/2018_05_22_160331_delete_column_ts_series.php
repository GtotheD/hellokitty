<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteColumnTsSeries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_series', function (Blueprint $table) {
            $table->dropIndex(['small_series_id', 'work_id']);
            $table->dropColumn('small_series_id');
            $table->string('related_work_id', 255)->after('work_id');
            $table->index('work_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ts_series', function (Blueprint $table) {
            $table->string('small_series_id', 255)->after('id');
            $table->index(['small_series_id', 'work_id']);
            $table->dropIndex('work_id');
            $table->dropColumn('related_work_id');
        });
    }
}
