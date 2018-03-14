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
        Schema::create('ts_products', function (Blueprint $table) {
            $table->increments('id');// アイテム名（products.item_cdに応じたアイテム名表示）
            $table->text('product_id');// アイテム名（products.item_cdに応じたアイテム名表示）
            $table->text('product_type_id');// アイテム名（products.item_cdに応じたアイテム名表示）
            $table->text('product_type_name');// アイテム名（products.item_cdに応じたアイテム名表示）
            $table->text('service_id');// アイテム名（products.item_cdに応じたアイテム名表示）
            $table->text('service_name');// アイテム名（products.item_cdに応じたアイテム名表示）
            $table->text('msdb_item');// アイテム名（products.item_cdに応じたアイテム名表示）
            $table->text('item_cd');// アイテム名（products.item_cdに応じたアイテム名表示）
            $table->text('item_name');// アイテム名（products.item_cdに応じたアイテム名表示）
            $table->text('product_name');// アイテム名（products.item_cdに応じたアイテム名表示）
            $table->text('disc_info');// 組数（products.disc_info）
            $table->text('subtitle');// 字幕（products.subtitle）
            // 吹き替え（）
            $table->text('sound_spec');// 音声（products.sound_spec）
            $table->text('region_info');// リージョンコード（region_info）
            $table->text('price_tax_out');// 定価（税抜）（products.price_tax_out）
            $table->text('play_time');// 収録時間（products.play_time）
            $table->text('product_code');// 商品番号（products.product_code）
            $table->text('jan');// JANコード（products.jan）
            $table->text('jacket_l');// ジャケ写（products.jacket_l）
            $table->dateTime('sale_start_date');// 発売日（products.sale_start_date）
            $table->text('contents');// 収録内容（docs.doc_text[doc_type_id=“04”]）
            $table->text('privilege');// 特典内容（docs.doc_text[doc_type_id=“11”]）
            $table->text('best_album_flg');// products.best_album_flg
            $table->text('maker_name');// products.maker_name
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ts_products');
    }
}
