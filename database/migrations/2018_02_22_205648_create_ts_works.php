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
        Schema::create('ts_structures', function (Blueprint $table) {
            // work_title
            // jacket_l
            // sale_start_date
            // big_genre_name
            // medium_genre_name
            // rating_name アダルト表示
            //　doc_text
            // created_year
            // created_countries
            // work_title_orig
            // work_type_id
            // work_copyright
            // sell_rental_flag
            $table->increments('id');
            $table->unsignedInteger('sort');
            $table->unsignedTinyInteger('goods_type');
            $table->unsignedTinyInteger('sale_type');
            $table->unsignedTinyInteger('section_type');
            $table->dateTime('display_start_date');
            $table->dateTime('display_end_date');
            $table->text('title');
            $table->text('link_url');
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
        //
    }
}
