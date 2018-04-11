<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTsHimoKeywords2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ts_himo_keywords2', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('keyword', 20)->index();
            $table->unsignedInteger('weight');
            $table->text('roman_alphabet', 255);
            $table->text('hiragana', 255);
            $table->text('katakana', 255);
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
        Schema::dropIfExists('ts_himo_keywords2');
    }
}
