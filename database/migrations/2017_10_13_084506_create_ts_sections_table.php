<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTsSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ts_sections', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ts_structure_id');
            $table->unsignedTinyInteger('code_type');
            $table->text('code', 255);
            $table->text('url_code', 255);
            $table->dateTime('display_start_date');
            $table->dateTime('display_end_date');
            $table->dateTime('rental_start_date');
            $table->dateTime('rental_end_date');
            $table->dateTime('sale_start_date');
            $table->dateTime('sale_end_date');
            $table->text('image_url', 255);
            $table->text('title', 255);
            $table->text('supplement', 255);
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
        Schema::dropIfExists('ts_sections');
    }

}
