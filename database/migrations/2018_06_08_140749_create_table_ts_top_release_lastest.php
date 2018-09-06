<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTsTopReleaseLastest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ts_top_release_lastest', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('month');
            $table->string('tap_genre_id',3);
            $table->unsignedInteger('sort');
            $table->string('work_id', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ts_top_release_lastest');
    }
}
