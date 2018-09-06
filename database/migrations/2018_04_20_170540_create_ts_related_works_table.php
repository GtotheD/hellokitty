<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTsRelatedWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ts_related_works', function (Blueprint $table) {
            $table->string('work_id', 255); // ts_works.work_id
            $table->string('related_work_id', 255); // ts_works.work_id
            $table->timestamps();
            $table->index(['work_id', 'related_work_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ts_related_works');
    }
}
