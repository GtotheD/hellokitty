<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTsActiveReference extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ts_active_reference', function (Blueprint $table) {
            $table->string('view', 20);
            $table->unsignedInteger('active_table');
            $table->timestamps();
        });

        DB::table('ts_active_reference')->insert([
            ['view' => 'ts_himo_keywords', 'active_table' => 0, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['view' => 'ts_bk2_recommends', 'active_table' => 0, 'created_at' => new DateTime(), 'updated_at' => new DateTime()]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ts_active_reference');
    }
}
