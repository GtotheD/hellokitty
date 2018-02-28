<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTsProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // アイテム名（products.item_cdに応じたアイテム名表示）
        // 組数（products.disc_info）
        // 字幕（products.subtitle）
        // 吹き替え（）
        // 音声（products.sound_spec）
        // リージョンコード（region_info）
        // 定価（税抜）（products.price_tax_out）
        // 収録時間（products.play_time）
        // 商品番号（products.product_code）
        // JANコード（products.jan）
        // ジャケ写（products.jacket_l）
        // タイトル（products.work_title）
        // NEWアイコン：リリースから2週間以内の作品に対して表示
        // 在庫状況：TWSから取得した店舗毎の在庫状況を表示
        // 発売日（products.sale_start_date）
        // 収録内容（docs.doc_text[doc_type_id=“04”]）
        // 特典内容（docs.doc_text[doc_type_id=“11”]）
        // products.best_album_flg
        // products.maker_name
        //

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
