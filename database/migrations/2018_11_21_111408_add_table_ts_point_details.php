<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableTsPointDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ts_point_details', function (Blueprint $table) {
            $table->integer('mem_id');
            $table->unsignedSmallInteger('membership_type');
            $table->unsignedInteger('point');
            $table->unsignedInteger('fixed_point_total');
            $table->dateTime('fixed_point_min_limit_time');
            $table->timestamps();
            $table->primary('mem_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ts_point_details');
    }
}
