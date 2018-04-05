<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTsWorks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ts_works', function (Blueprint $table) {
            $table->increments('id');
            $table->string('work_id',255);
            $table->text('url_cd');
            $table->text('ccc_work_cd');
            $table->text('work_type_id');
            $table->text('work_format_id');
            $table->text('work_format_name');
            $table->text('work_title');
            $table->text('work_title_orig');
            $table->text('copyright');
            $table->text('jacket_l');
            $table->dateTime('sale_start_date');
            $table->text('big_genre_id');
            $table->text('big_genre_name');
            $table->text('medium_genre_id');
            $table->text('medium_genre_name');
            $table->text('small_genre_id');
            $table->text('small_genre_name');
            $table->text('rating_id');
            $table->text('rating_name');
            $table->text('adult_flg');
            $table->text('doc_text');
            $table->text('created_year');
            $table->text('created_countries');
            $table->text('book_series_name');
            $table->timestamps();

            $table->unique('work_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ts_works');
    }
}
