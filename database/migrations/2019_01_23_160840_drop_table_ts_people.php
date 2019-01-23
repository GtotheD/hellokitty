<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropTableTsPeople extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('ts_people');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('ts_people', function (Blueprint $table) {
            $table->increments('id');
            $table->string('product_unique_id'); // 商品ユニークID(product.id でproduct.product_idではない)
            $table->string('person_id', 255);
            $table->string('person_name', 255);
            $table->string('role_id', 255);
            $table->string('role_name', 255);
            $table->timestamps();
        });
    }
}
