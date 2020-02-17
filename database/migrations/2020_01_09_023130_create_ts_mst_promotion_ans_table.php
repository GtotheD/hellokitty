<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTsMstPromotionAnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ts_mst_promotion_ans', function (Blueprint $table) {
            $table->string('promotion_id', 255);
            $table->unsignedInteger('sort_qes');
            $table->unsignedInteger('sort');
            $table->string('text', 255);
            $table->timestamps();
            $table->primary(['promotion_id', 'sort_qes', 'sort']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ts_mst_promotion_ans');
    }
}
