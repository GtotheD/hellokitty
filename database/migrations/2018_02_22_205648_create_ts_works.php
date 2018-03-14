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
            $table->text('work_id');
            $table->text('work_title');
            $table->text('jacket_l');
            $table->dateTime('sale_start_date');
            $table->text('big_genre_id');
            $table->text('big_genre_name');
            $table->text('medium_genre_id');
            $table->text('medium_genre_name');
            $table->text('rating_name');
            $table->text('doc_text');
            $table->text('created_year');
            $table->text('created_countries');
            $table->text('work_title_orig');
            $table->text('work_type_id');
            $table->text('work_copyright');
            $table->text('sell_rental_flag');
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
        Schema::dropIfExists('ts_works');
    }
}
