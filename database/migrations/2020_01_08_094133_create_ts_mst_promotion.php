<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTsMstPromotion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ts_mst_promotion', function (Blueprint $table) {
            $table->string('id', 255)->primary();
            $table->string('title', 255)->nullable();
            $table->string('main_image', 255)->nullable();
            $table->string('thumb_image', 255)->nullable();
            $table->string('outline', 255)->nullable();
            $table->string('target', 255)->nullable();
            $table->dateTime('promotion_start_date')->nullable();
            $table->dateTime('promotion_end_date')->nullable();
            $table->string('caution', 255)->nullable();
            $table->text('supplement')->nullable();
            $table->string('image', 255)->nullable();
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
        Schema::dropIfExists('ts_mst_promotion');
    }
}
