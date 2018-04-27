<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToTsRelatedPeoplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_related_people', function (Blueprint $table) {
            $table->string('people_id',255)->change();
            $table->index('people_id');
            $table->index(['people_id', 'person_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ts_related_people', function (Blueprint $table) {
            $table->dropIndex('people_id');
            $table->dropIndex(['people_id', 'person_id']);
        });
    }
}
