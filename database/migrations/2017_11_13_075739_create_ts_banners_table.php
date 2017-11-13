<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTsBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ts_banners', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ts_structure_id');
            $table->text('link_url');
            $table->unsignedTinyInteger('is_tap_on');
            $table->text('image_url', 255);
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
        Schema::dropIfExists('ts_banners');
    }
}
