<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnDocTextToDocsFromTsProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_products', function (Blueprint $table) {
            $table->renameColumn('doc_text', 'docs');
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
            $table->renameColumn('docs', 'doc_text');
        });
    }
}
