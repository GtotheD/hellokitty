<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTsMstPromotionWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ts_mst_promotion_works', function (Blueprint $table) {
            $table->string('promotion_id', 255);
            $table->unsignedInteger('sort');
            $table->string('work_title', 255)->nullable();
            $table->string('work_id', 255)->nullable();
            $table->string('jan', 255);
            $table->timestamps();
            $table->primary(['promotion_id', 'sort']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ts_mst_promotion_works');
    }
}
