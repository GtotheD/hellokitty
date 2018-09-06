<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeopleRelatedWorks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ts_people_related_works', function (Blueprint $table) {
            $table->increments('id');
            $table->text('person_id');
            $table->string('work_id',255)->index();
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
        Schema::dropIfExists('ts_people_related_works');
    }
}
