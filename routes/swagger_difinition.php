<?php
/**
 *  @SWG\Definition(
 *          definition="Work",
 *          required={
 *              "work_id", "work_title"
 *          },
 *          @SWG\Property(
 *              property="work_id",
 *              type="string",
 *              description="作品タイトル"
 *          ),
 *          @SWG\Property(
 *              property="work_title",
 *              type="string",
 *              description="作品タイトル"
 *          ),
 *          @SWG\Property(
 *              property="work_title_orig",
 *              type="string",
 *              description="タイトル原題"
 *          ),
 *          @SWG\Property(
 *              property="jacket_l",
 *              type="string",
 *              description="ジャケ写"
 *          ),
 *          @SWG\Property(
 *              property="sale_start_date",
 *              type="string",
 *              description="レンタル開始日"
 *          ),
 *          @SWG\Property(
 *              property="big_genre_name",
 *              type="string",
 *              description="大ジャンル"
 *          ),
 *          @SWG\Property(
 *              property="medium_genre_name",
 *              type="string",
 *              description="中ジャンル"
 *          ),
 *          @SWG\Property(
 *              property="rating_name",
 *              type="string",
 *              description="年齢制限表示"
 *          ),
 *          @SWG\Property(
 *              property="doc_text",
 *              type="string",
 *              description="説明"
 *          ),
 *          @SWG\Property(
 *              property="created_year",
 *              type="string",
 *              description="作成年"
 *          ),
 *          @SWG\Property(
 *              property="created_countries",
 *              type="string",
 *              description="作成国"
 *          )
 *  )
 * */
/**
 *  @SWG\Definition(
 *          definition="Review",
 *          @SWG\Property(
 *              property="rating",
 *              type="string",
 *              description="レーティング"
 *          ),
 *          @SWG\Property(
 *              property="contributor",
 *              type="string",
 *              description="投稿者"
 *          ),
 *          @SWG\Property(
 *              property="contribute_date",
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
 *              property="work_title",
 *              type="string",
 *              description="作品名"
 *          ),
 *          @SWG\Property(
 *              property="product_name",
 *              type="string",
 *              description="商品名"
 *          ),
 *          @SWG\Property(
 *              property="product_code",
 *              type="string",
 *              description="商品番号"
 *          ),
 *          @SWG\Property(
 *              property="jan",
 *              type="string",
 *              description="JANコード"
 *          ),*
 *          @SWG\Property(
 *              property="item_cd",
 *              type="string",
 *              description="アイテムコード"
 *          ),
 *          @SWG\Property(
 *              property="jacket_l",
 *              type="string",
 *              description="ジャケ写"
 *          ),
 *          @SWG\Property(
 *              property="sale_start_date",
 *              type="string",
 *              description="発売日"
 *          ),
 *          @SWG\Property(
 *              property="disc_info",
 *              type="string",
 *              description="組数"
 *          ),
 *          @SWG\Property(
 *              property="subtitle",
 *              type="string",
 *              description="字幕"
 *          ),
 *          @SWG\Property(
 *              property="sound_spec",
 *              type="string",
 *              description="音声"
 *          ),
 *          @SWG\Property(
 *              property="region_info",
 *              type="string",
 *              description="リージョンコード"
 *          ),
 *          @SWG\Property(
 *              property="price_tax_out",
 *              type="string",
 *              description="定価（税抜）"
 *          ),
 *          @SWG\Property(
 *              property="play_time",
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
 *              property="best_album_flg",
 *              type="string",
 *              description="特典内容"
 *          ),
 *          @SWG\Property(
 *              property="maker_name",
 *              type="string",
 *              description="メーカー"
 *          ),
 *  )
 * */
/**
 *  @SWG\Definition(
 *          definition="Cast",
 *          @SWG\Property(
 *              property="work_id",
 *              type="string",
 *              description="作品ID"
 *          ),
 *          @SWG\Property(
 *              property="work_title",
 *              type="string",
 *              description="作品タイトル"
 *          ),
 *          @SWG\Property(
 *              property="jacket_l",
 *              type="string",
 *              description="ジャケ写"
 *          ),
 *  )
 * */
