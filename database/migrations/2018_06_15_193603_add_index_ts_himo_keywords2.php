<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexTsHimoKeywords2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_himo_keywords2', function (Blueprint $table) {
            $table->string('roman_alphabet',255)->change();
            $table->index('roman_alphabet');
            $table->string('hiragana',255)->change();
            $table->index('hiragana');
            $table->string('katakana',255)->change();
            $table->index('katakana');
            $table->index('roman_alphabet');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ts_himo_keywords2', function (Blueprint $table) {
            $table->text('roman_alphabet',255)->change();
            $table->dropIndex('roman_alphabet');
            $table->text('hiragana',255)->change();
            $table->dropIndex('hiragana');
            $table->text('katakana',255)->change();
            $table->dropIndex('katakana');
        });
    }
}
