<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnFilmarksIdTableWorks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_works', function (Blueprint $table) {
            $table->text('filmarks_id')->after('small_genre_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ts_works', function (Blueprint $table) {
            $table->dropColumn([ 'filmarks_id']);
        });
    }
}
