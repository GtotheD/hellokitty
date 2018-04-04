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
            $table->text('work_id'); // 商品ID
            $table->text('product_unique_id'); // 商品ID(product.id でproduct.product_idではない)
            $table->text('sale_type'); // 販売タイプ（sell, rental）
            $table->text('product_code'); // 商品番号
            $table->text('jan'); // JANコード
            $table->text('itemCd'); // アイテムコード
            $table->text('jacketL'); // ジャケ写
            $table->text('docText'); // 商品説明
            $table->dateTime('saleStartDate'); // 発売日
            $table->text('newFlg'); // NEW表示フラグ
            $table->text('discInfo'); // 組数
            $table->text('subtitle'); // 字幕
            $table->text('subtitleFlg'); // 字幕（１：字幕/２：吹替/３：二ヶ国語/４：アニメ/５：デフォルト）
            $table->text('soundSpec'); // 音声
            $table->text('regionInfo'); // リージョンコード
            $table->text('priceTaxOut'); // 定価（税抜）
            $table->text('playTime'); // 収録時間
            $table->text('contents'); // 内容 doc_textから取得
            $table->text('privilege'); // 特典内容 doc_textから取得
            $table->text('bestAlbumFlg'); // ベストアルバムフラグ
            $table->text('isDoubleAlbum'); // ディスク枚数（0=1枚組、1=2枚組以上）
            $table->text('includedDisk'); // 付属ディスク
            $table->text('importedFlg'); // 取扱区分（0：国内盤／1:インディーズ／2:輸入盤／3：インディーズ輸入盤）
            $table->text('bookPageNumber'); // ページ数
            $table->text('bookSize'); // 大きさ
            $table->text('isbn10'); // ISBN-10
            $table->text('isbn13'); // ISBN-13
            $table->text('makerName'); // メーカー
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
