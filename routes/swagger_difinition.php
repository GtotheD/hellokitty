<?php
/**
 *  @SWG\Definition(
 *          definition="Work",
 *          @SWG\Property(
 *              property="workId",
 *              type="string",
 *              description="作品ID"
 *          ),
 *          @SWG\Property(
 *              property="urlCd",
 *              type="string",
 *              description="URLコード"
 *          ),
 *          @SWG\Property(
 *              property="cccWorkCd",
 *              type="string",
 *              description="ccc作品コード"
 *          ),
 *          @SWG\Property(
 *              property="workTitle",
 *              type="string",
 *              description="作品タイトル"
 *          ),
 *          @SWG\Property(
 *              property="workTitleOrig",
 *              type="string",
 *              description="タイトル原題"
 *          ),
 *          @SWG\Property(
 *              property="supplement",
 *              type="string",
 *              description="作者・著者・アーティスト・機種"
 *          ),
 *          @SWG\Property(
 *              property="saleType",
 *              type="string",
 *              description="販売タイプ（sell, rental）"
 *          ),
 *          @SWG\Property(
 *              property="itemType",
 *              type="string",
 *              description="アイテム種別　（cd, dvd, book, game)"
 *          ),
 *          @SWG\Property(
 *              property="jacketL",
 *              type="string",
 *              description="ジャケ写"
 *          ),
 *          @SWG\Property(
 *              property="saleStartDate",
 *              type="string",
 *              description="販売・レンタル開始日"
 *          ),
 *          @SWG\Property(
 *              property="newFlg",
 *              type="boolean",
 *              description="NEW表示フラグ"
 *          ),
 *          @SWG\Property(
 *              property="bigGenreId",
 *              type="string",
 *              description="大ジャンルID"
 *          ),
 *          @SWG\Property(
 *              property="bigGenreName",
 *              type="string",
 *              description="大ジャンル"
 *          ),
 *          @SWG\Property(
 *              property="mediumGenreId",
 *              type="string",
 *              description="中ジャンルID"
 *          ),
 *          @SWG\Property(
 *              property="mediumGenreName",
 *              type="string",
 *              description="中ジャンル"
 *          ),
 *          @SWG\Property(
 *              property="smallGenreId",
 *              type="string",
 *              description="小ジャンルID"
 *          ),
 *          @SWG\Property(
 *              property="smallGenreName",
 *              type="string",
 *              description="小ジャンル"
 *          ),
 *          @SWG\Property(
 *              property="ratingName",
 *              type="string",
 *              description="年齢制限表示"
 *          ),
 *          @SWG\Property(
 *              property="docText",
 *              type="string",
 *              description="説明"
 *          ),
 *          @SWG\Property(
 *              property="createdYear",
 *              type="string",
 *              description="作成年"
 *          ),
 *          @SWG\Property(
 *              property="createdCountries",
 *              type="string",
 *              description="作成国"
 *          ),
 *          @SWG\Property(
 *              property="copyright",
 *              type="string",
 *              description="コピーライト"
 *          ),
 *          @SWG\Property(
 *              property="makerName",
 *              type="string",
 *              description="メーカー"
 *          ),
 *          @SWG\Property(
 *              property="workFormatName",
 *              type="string",
 *              description="種別（アルバム／マキシシングル）"
 *          ),
 *          @SWG\Property(
 *              property="bookSeriesName",
 *              type="string",
 *              description="掲載雑誌名・文庫名"
 *          ),
 *          @SWG\Property(
 *              property="bookReleaseMonth",
 *              type="string",
 *              description="出版年月"
 *          ),
 *          @SWG\Property(
 *              property="videoFlg",
 *              type="boolean",
 *              description="関連動画表示フラグ"
 *          ),
 *          @SWG\Property(
 *              property="adultFlg",
 *              type="boolean",
 *              description="アダルトフラグ"
 *          ),
 *          @SWG\Property(
 *              property="musicDownloadUrl",
 *              type="boolean",
 *              description="音楽ダウンロード用のURL"
 *          ),
 *          @SWG\Property(
 *              property="saleTypeHas",
 *              type="object",
 *              @SWG\Property(
 *                  property="sell",
 *                  type="boolean",
 *                  description="セルの有無",
 *              ),
 *              @SWG\Property(
 *                  property="rental",
 *                  type="boolean",
 *                  description="レンタルの有無",
 *              ),
 *          ),
 * )
 * */
/**
 *  @SWG\Definition(
 *          definition="WorkNarrow",
 *          @SWG\Property(
 *              property="workId",
 *              type="string",
 *              description="作品ID"
 *          ),
 *          @SWG\Property(
 *              property="urlCd",
 *              type="string",
 *              description="URLコード"
 *          ),
 *          @SWG\Property(
 *              property="cccWorkCd",
 *              type="string",
 *              description="ccc作品コード"
 *          ),
 *          @SWG\Property(
 *              property="workTitle",
 *              type="string",
 *              description="作品タイトル"
 *          ),
 *          @SWG\Property(
 *              property="newFlg",
 *              type="boolean",
 *              description="NEW表示フラグ"
 *          ),
 *          @SWG\Property(
 *              property="jacketL",
 *              type="string",
 *              description="ジャケ写"
 *          ),
 *          @SWG\Property(
 *              property="supplement",
 *              type="string",
 *              description="著者・作者"
 *          ),
 *          @SWG\Property(
 *              property="saleType",
 *              type="string",
 *              description="販売タイプ（sell, rental）",
 *          ),
 *          @SWG\Property(
 *              property="itemType",
 *              type="string",
 *              description="アイテム種別　（cd, dvd, book, game）"
 *          ),
 *          @SWG\Property(
 *              property="adultFlg",
 *              type="boolean",
 *              description="アダルトフラグ"
 *          ),
 *  )
 * */
/**
 *  @SWG\Definition(
 *          definition="WorkNarrowSearch",
 *          @SWG\Property(
 *              property="workId",
 *              type="string",
 *              description="作品ID"
 *          ),
 *          @SWG\Property(
 *              property="urlCd",
 *              type="string",
 *              description="URLコード"
 *          ),
 *          @SWG\Property(
 *              property="cccWorkCd",
 *              type="string",
 *              description="ccc作品コード"
 *          ),
 *          @SWG\Property(
 *              property="workTitle",
 *              type="string",
 *              description="作品タイトル"
 *          ),
 *          @SWG\Property(
 *              property="newFlg",
 *              type="boolean",
 *              description="NEW表示フラグ"
 *          ),
 *          @SWG\Property(
 *              property="jacketL",
 *              type="string",
 *              description="ジャケ写"
 *          ),
 *          @SWG\Property(
 *              property="supplement",
 *              type="string",
 *              description="著者・作者"
 *          ),
 *          @SWG\Property(
 *              property="saleType",
 *              type="string",
 *              description="販売タイプ（sell, rental）",
 *          ),
 *          @SWG\Property(
 *              property="itemType",
 *              type="string",
 *              description="アイテム種別　（cd, dvd, book, game）"
 *          ),
 *          @SWG\Property(
 *              property="saleTypeHas",
 *              type="object",
 *              @SWG\Property(
 *                  property="sell",
 *                  type="boolean",
 *                  description="セルの有無",
 *              ),
 *              @SWG\Property(
 *                  property="rental",
 *                  type="boolean",
 *                  description="レンタルの有無",
 *              ),
 *          ),
 *          @SWG\Property(
 *              property="adultFlg",
 *              type="boolean",
 *              description="アダルトフラグ"
 *          ),
 *  )
 * */
/**
 *  @SWG\Definition(
 *          definition="WorkNarrowRelease",
 *          @SWG\Property(
 *              property="workId",
 *              type="string",
 *              description="作品ID"
 *          ),
 *          @SWG\Property(
 *              property="urlCd",
 *              type="string",
 *              description="URLコード"
 *          ),
 *          @SWG\Property(
 *              property="cccWorkCd",
 *              type="string",
 *              description="ccc作品コード"
 *          ),
 *          @SWG\Property(
 *              property="rankNo",
 *              type="string",
 *              description="順位"
 *          ),
 *          @SWG\Property(
 *              property="comparison",
 *              type="string",
 *              description="前月比（up, down, keep, new）"
 *          ),
 *          @SWG\Property(
 *              property="workTitle",
 *              type="string",
 *              description="作品タイトル"
 *          ),
 *          @SWG\Property(
 *              property="productTitle",
 *              type="string",
 *              description="商品タイトル"
 *          ),
 *          @SWG\Property(
 *              property="newFlg",
 *              type="boolean",
 *              description="NEW表示フラグ"
 *          ),
 *          @SWG\Property(
 *              property="jacketL",
 *              type="string",
 *              description="ジャケ写"
 *          ),
 *          @SWG\Property(
 *              property="supplement",
 *              type="string",
 *              description="著者・作者"
 *          ),
 *          @SWG\Property(
 *              property="saleType",
 *              type="string",
 *              description="販売タイプ（sell, rental）",
 *          ),
 *          @SWG\Property(
 *              property="itemType",
 *              type="string",
 *              description="アイテム種別　（cd, dvd, book, game）"
 *          ),
 *          @SWG\Property(
 *              property="adultFlg",
 *              type="boolean",
 *              description="アダルトフラグ"
 *          ),
 *  )
 * */
/**
 *  @SWG\Definition(
 *          definition="Review",
 *          @SWG\Property(
 *              property="rating",
 *              type="number",
 *              format="float",
 *              description="レーティング"
 *          ),
 *          @SWG\Property(
 *              property="contributor",
 *              type="string",
 *              description="投稿者"
 *          ),
 *          @SWG\Property(
 *              property="contributeDate",
 *              type="string",
 *              description="投稿日時"
 *          ),
 *          @SWG\Property(
 *              property="contents",
 *              type="string",
 *              description="内容"
 *          ),
 *  )
 * */
/**
 *  @SWG\Definition(
 *          definition="Product",
 *          @SWG\Property(
 *              property="productName",
 *              type="string",
 *              description="商品名"
 *          ),
 *          @SWG\Property(
 *              property="saleType",
 *              type="string",
 *              description="販売タイプ（sell, rental）"
 *          ),
 *          @SWG\Property(
 *              property="productCode",
 *              type="string",
 *              description="商品番号"
 *          ),
 *          @SWG\Property(
 *              property="productUniqueId",
 *              type="string",
 *              description="商品ID(product.id でproduct.product_idではない)"
 *          ),
 *          @SWG\Property(
 *              property="jan",
 *              type="string",
 *              description="JANコード"
 *          ),
 *          @SWG\Property(
 *              property="itemCd",
 *              type="string",
 *              description="アイテムコード"
 *          ),
 *          @SWG\Property(
 *              property="itemName",
 *              type="string",
 *              description="アイテム名"
 *          ),
 *          @SWG\Property(
 *              property="jacketL",
 *              type="string",
 *              description="ジャケ写"
 *          ),
 *          @SWG\Property(
 *              property="docText",
 *              type="string",
 *              description="商品説明"
 *          ),
 *          @SWG\Property(
 *              property="saleStartDate",
 *              type="string",
 *              description="発売日"
 *          ),
 *          @SWG\Property(
 *              property="newFlg",
 *              type="boolean",
 *              description="NEW表示フラグ"
 *          ),
 *          @SWG\Property(
 *              property="discInfo",
 *              type="string",
 *              description="組数"
 *          ),
 *          @SWG\Property(
 *              property="subtitle",
 *              type="string",
 *              description="字幕"
 *          ),
 *          @SWG\Property(
 *              property="dub",
 *              type="string",
 *              description="字幕（「吹替」もしくは「二ヶ国語」の文字列返却。該当がない場合はnull）"
 *          ),
 *          @SWG\Property(
 *              property="soundSpec",
 *              type="string",
 *              description="音声"
 *          ),
 *          @SWG\Property(
 *              property="regionInfo",
 *              type="string",
 *              description="リージョンコード"
 *          ),
 *          @SWG\Property(
 *              property="priceTaxOut",
 *              type="string",
 *              description="定価（税抜）"
 *          ),
 *          @SWG\Property(
 *              property="playTime",
 *              type="string",
 *              description="収録時間"
 *          ),
 *          @SWG\Property(
 *              property="contents",
 *              type="string",
 *              description="内容"
 *          ),
 *          @SWG\Property(
 *              property="privilege",
 *              type="string",
 *              description="特典内容"
 *          ),
 *          @SWG\Property(
 *              property="bestAlbum",
 *              type="string",
 *              description="ベストアルバム（ベスト盤の場合は文字列で「ベスト盤」を返却）"
 *          ),
 *          @SWG\Property(
 *              property="isDoubleAlbum",
 *              type="string",
 *              description="ディスク枚数（0=1枚組、1=2枚組以上）"
 *          ),
 *          @SWG\Property(
 *              property="includedDisk",
 *              type="string",
 *              description="付属ディスク"
 *          ),
 *          @SWG\Property(
 *              property="imported",
 *              type="string",
 *              description="取扱区分（国内盤／インディーズ／輸入盤／インディーズ輸入盤の文字列返却）"
 *          ),
 *          @SWG\Property(
 *              property="bookPageNumber",
 *              type="string",
 *              description="ページ数"
 *          ),
 *          @SWG\Property(
 *              property="bookSize",
 *              type="string",
 *              description="大きさ"
 *          ),
 *          @SWG\Property(
 *              property="isbn10",
 *              type="string",
 *              description="ISBN-10"
 *          ),
 *          @SWG\Property(
 *              property="isbn13",
 *              type="string",
 *              description="ISBN-13"
 *          ),
 *          @SWG\Property(
 *              property="makerName",
 *              type="string",
 *              description="メーカー"
 *          ),
 *  )
 * */
/**
 *  @SWG\Definition(
 *          definition="ProductNarrow",
 *          @SWG\Property(
 *              property="productName",
 *              type="string",
 *              description="商品名"
 *          ),
 *          @SWG\Property(
 *              property="productUniqueId",
 *              type="string",
 *              description="商品ID(product.id でproduct.product_idではない)"
 *          ),
 *          @SWG\Property(
 *              property="productKey",
 *              type="string",
 *              description="レンタルの場合はレンタル商品コード、セルの場合はJANコード"
 *          ),
 *          @SWG\Property(
 *              property="itemCd",
 *              type="string",
 *              description="アイテムコード"
 *          ),
 *          @SWG\Property(
 *              property="itemName",
 *              type="string",
 *              description="アイテムコードから変換したアイテム名(dvd, bluray, cd, book, game)"
 *          ),
 *          @SWG\Property(
 *              property="jacketL",
 *              type="string",
 *              description="ジャケ写"
 *          ),
 *          @SWG\Property(
 *              property="saleStartDate",
 *              type="string",
 *              description="発売日"
 *          ),
 *          @SWG\Property(
 *              property="newFlg",
 *              type="boolean",
 *              description="NEW表示フラグ"
 *          )
 *  )
 * */
/**
 *  @SWG\Definition(
 *          definition="ProductGroup",
 *          @SWG\Property(
 *              property="productName",
 *              type="string",
 *              description="商品名"
 *          ),
 *          @SWG\Property(
 *              property="productUniqueId",
 *              type="string",
 *              description="商品ID(product.id でproduct.product_idではない)"
 *          ),
 *          @SWG\Property(
 *              property="productKeys",
 *              type="object",
 *              @SWG\Property(
 *                  property="dvd",
 *                  type="string",
 *                  description="dvdのレンタルコード（ccc_rental_cd）",
 *              ),
 *              @SWG\Property(
 *                  property="bluray",
 *                  type="string",
 *                  description="blu-rayのレンタルコード（ccc_rental_cd）",
 *              ),
 *          ),
 *          @SWG\Property(
 *              property="jacketL",
 *              type="string",
 *              description="ジャケ写"
 *          ),
 *          @SWG\Property(
 *              property="saleStartDate",
 *              type="string",
 *              description="発売日"
 *          ),
 *          @SWG\Property(
 *              property="newFlg",
 *              type="boolean",
 *              description="NEW表示フラグ"
 *          )
 *  )
 * */

/**
 *  @SWG\Definition(
 *          definition="Count",
 *          @SWG\Property(
 *              property="dvd",
 *              type="integer",
 *              description="DVDのカウント"
 *          ),
 *          @SWG\Property(
 *              property="cd",
 *              type="integer",
 *              description="CDのカウント"
 *          ),
 *          @SWG\Property(
 *              property="book",
 *              type="integer",
 *              description="BOOKのカウント"
 *          ),
 *          @SWG\Property(
 *              property="game",
 *              type="integer",
 *              description="GAMEのカウント"
 *          ),
 *  )
 * */
/**
 *  @SWG\Definition(
 *          definition="favorite",
 *          @SWG\Property(
 *              property="work_id",
 *              type="string",
 *              description="Himo作品ID"
 *          ),
 *          @SWG\Property(
 *              property="item_type",
 *              type="string",
 *              description="Himo作品ID"
 *          ),
 *          @SWG\Property(
 *              property="created_at",
 *              type="string",
 *              description="最終更新日時"
 *          ),
 *  )
 * */
/**
 *  @SWG\Definition(
 *          definition="favorite_status",
 *          @SWG\Property(
 *              property="status",
 *              type="string",
 *              description="success or error"
 *          ),
 *          @SWG\Property(
 *              property="message",
 *              type="string",
 *              description="メッセージ（処理が成功しました等のメッセージ）"
 *          )
 *  )
 * */