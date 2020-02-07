<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTtvContentsCdInTsWorks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('ts_works', 'ttv_contents_cd')) {
            Schema::table('ts_works', function (Blueprint $table) {
                $table->string('ttv_contents_cd', 10)->nullable()->default(null)->after('ccc_work_cd');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ts_works', function (Blueprint $table) {
            //
        });
    }
}
