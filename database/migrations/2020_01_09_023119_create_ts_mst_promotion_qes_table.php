<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTsMstPromotionQesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ts_mst_promotion_qes', function (Blueprint $table) {
            $table->string('promotion_id', 255);
            $table->unsignedInteger('sort');
            $table->string('text', 255);
            $table->string('format', 255);
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
        Schema::dropIfExists('ts_mst_promotion_qes');
    }
}
