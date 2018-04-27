<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToTsPeoplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_people', function (Blueprint $table) {
            $table->string('product_unique_id',255)->change();
            $table->index('product_unique_id');
            $table->index('person_id');
            $table->index(['product_unique_id', 'role_id']);
            $table->index(['product_unique_id', 'person_id']);
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
            $table->dropIndex('product_unique_id');
            $table->dropIndex('person_id');
            $table->dropIndex(['product_unique_id', 'role_id']);
            $table->dropIndex(['product_unique_id', 'person_id']);
        });
    }
}
