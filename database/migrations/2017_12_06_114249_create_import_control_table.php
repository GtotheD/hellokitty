<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImportControlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ts_import_control', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('goods_type');
            $table->unsignedTinyInteger('sale_type');
            $table->text('file_name');
            $table->unsignedInteger('unix_timestamp');
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
        Schema::dropIfExists('ts_import_control');
    }
}
