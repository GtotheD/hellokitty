<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnIsPremiumNetInTsWorks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('ts_works', 'is_premium_net')) {
            Schema::table('ts_works', function (Blueprint $table) {
                $table->json('is_premium_net')->after('thousandtags');
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
        //
    }
}
