<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueIndexTsPeople extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_people', function (Blueprint $table) {
            $table->unique(['product_unique_id', 'person_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ts_people', function (Blueprint $table) {
            $table->dropUnique(['product_unique_id', 'person_id', 'role_id']);
        });
    }
}
