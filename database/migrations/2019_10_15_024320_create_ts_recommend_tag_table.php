<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTsRecommendTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ts_recommend_tag', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tag', 255);
            $table->string('tag_title', 255)->nullable()->default(null);
            $table->string('tag_message', 255)->nullable()->default(null);
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
        Schema::dropIfExists('ts_recommend_tag');
    }
}
