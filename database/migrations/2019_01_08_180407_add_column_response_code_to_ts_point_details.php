<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnResponseCodeToTsPointDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_point_details', function (Blueprint $table) {
            $table->string('response_code', 2)->after('mem_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ts_point_details', function (Blueprint $table) {
            Schema::dropIfExists('response_code');
        });
    }
}
