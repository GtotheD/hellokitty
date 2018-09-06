<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnMakerCdToTsProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_products', function (Blueprint $table) {
            $table->string('maker_cd', 15)->after('isbn13');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ts_products', function (Blueprint $table) {
            $table->dropColumn('maker_cd');
        });
    }
}
