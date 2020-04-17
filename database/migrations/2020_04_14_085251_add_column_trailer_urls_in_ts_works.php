<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTrailerUrlsInTsWorks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        if (!Schema::hasColumn('ts_works', 'trailer_urls')) {
            Schema::table('ts_works', function (Blueprint $table) {
                $table->json('trailer_urls')->nullable()->default(null)->after('doc_text');
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
