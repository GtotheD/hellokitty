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
            $table->increments('id');//
            $table->text('product_unique_id'); // 商品ユニークID(product.id でproduct.product_idではない)
            $table->text('work_id'); // 作品ID
            $table->text('product_id'); // 商品ID
            $table->text('ccc_family_cd'); // 商品ファミリーコード
            $table->text('product_type_id'); // 販売タイプ（sell, rental）
            $table->text('product_type_name'); // 販売タイプ（sell, rental）
            $table->text('service_id'); //
            $table->text('service_name'); //
            $table->text('msdb_item'); //
            $table->text('item_cd'); //
            $table->text('item_name'); //
            $table->text('product_code'); // 商品番号
            $table->text('jan'); // JANコード
            $table->text('product_name'); // 商品名
            $table->text('jacket_l'); // ジャケ写
            $table->text('doc_text'); // 商品説明
            $table->dateTime('sale_start_date'); // 発売日
            $table->text('disc_info'); // 組数
            $table->text('subtitle'); // 字幕
            $table->text('subtitle_flg'); // 字幕（１：字幕/２：吹替/３：二ヶ国語/４：アニメ/５：デフォルト）
            $table->text('sound_spec'); // 音声
            $table->text('region_info'); // リージョンコード
            $table->text('price_tax_out'); // 定価（税抜）
            $table->text('play_time'); // 収録時間
            $table->text('contents'); // 内容 doc_textから取得
            $table->text('privilege'); // 特典内容 doc_textから取得
            $table->text('best_album_flg'); // ベストアルバムフラグ
            $table->text('is_double_album'); // ディスク枚数（0=1枚組、1=2枚組以上）
            $table->text('included_disk'); // 付属ディスク
            $table->text('imported_flg'); // 取扱区分（0：国内盤／1:インディーズ／2:輸入盤／3：インディーズ輸入盤）
            $table->text('book_page_number'); // ページ数
            $table->text('book_size'); // 大きさ
            $table->text('book_release_month'); // 本販売月
            $table->text('isbn10'); // ISBN-10
            $table->text('isbn13'); // ISBN-13
            $table->text('maker_name'); // メーカー
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
