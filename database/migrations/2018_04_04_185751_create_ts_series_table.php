<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTsSeriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ts_series', function (Blueprint $table) {
            $table->increments('id');
            $table->string('small_series_id', 255);
            $table->string('work_id', 255); // ts_works.work_id
            $table->timestamps();
            $table->index(['small_series_id', 'work_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('ts_series', function (Blueprint $table)
        {
            $table->dropIndex(['small_series_id', 'work_id']);
        });
        Schema::dropIfExists('ts_series');
    }
}
