<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTsProductsUniqeIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_products', function (Blueprint $table) {
            $table->dropUnique(['product_unique_id']);
            $table->unique(['work_id', 'product_unique_id']);
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
            $table->dropUnique(['work_id', 'product_unique_id']);
            $table->unique(['product_unique_id']);
        });
    }
}
