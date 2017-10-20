<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStructuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('structures', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sort');
            $table->unsignedTinyInteger('goods_type');
            $table->unsignedTinyInteger('sale_type');
            $table->unsignedTinyInteger('section_type');
            $table->dateTime('display_start_date');
            $table->dateTime('display_end_date');
            $table->text('title', 255);
            $table->text('link_url', 255);
            $table->unsignedTinyInteger('is_tap_on');
            $table->unsignedTinyInteger('is_ranking');
            $table->text('api_url');
            $table->text('section_file_name');
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
        Schema::dropIfExists('structures');
    }
}
