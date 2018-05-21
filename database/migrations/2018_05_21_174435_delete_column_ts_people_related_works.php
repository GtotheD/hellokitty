<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteColumnTsPeopleRelatedWorks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ts_people_related_works', function (Blueprint $table) {
            $table->dropColumn('url_cd');
            $table->dropColumn('ccc_work_cd');
            $table->dropColumn('work_type_id');
            $table->dropColumn('work_format_id');
            $table->dropColumn('work_format_name');
            $table->dropColumn('work_title');
            $table->dropColumn('work_title_orig');
            $table->dropColumn('copyright');
            $table->dropColumn('jacket_l');
            $table->dropColumn('sale_start_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ts_people_related_works', function (Blueprint $table) {
            $table->text('url_cd');
            $table->text('ccc_work_cd');
            $table->text('work_type_id');
            $table->text('work_format_id');
            $table->text('work_format_name');
            $table->text('work_title');
            $table->text('work_title_orig');
            $table->text('copyright');
            $table->text('jacket_l');
            $table->dateTime('sale_start_date');
        });
    }
}
