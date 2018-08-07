<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTsOneTimeCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ts_one_time_coupons', function (Blueprint $table) {
            $table->string('company_id',4);
            $table->string('store_cd',4);
            $table->string('delivery_id',10);
            $table->unsignedInteger('tokuban');
            $table->datetime('delivery_start_date');
            $table->datetime('delivery_end_date');
            $table->timestamps();
            $table->primary(['company_id','store_cd','tokuban']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ts_one_time_coupons');
    }
}
