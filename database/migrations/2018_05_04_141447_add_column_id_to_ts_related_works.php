<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnIdToTsRelatedWorks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_related_works', function (Blueprint $table) {
            $table->bigIncrements('id')->first();
            $table->dropIndex(['work_id', 'related_work_id']);
            $table->unique(['work_id', 'related_work_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ts_related_works', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropUnique(['work_id', 'related_work_id']);
            $table->index(['work_id', 'related_work_id']);
        });
    }
}
