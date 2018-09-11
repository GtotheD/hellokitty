<?php
/**
 * @SWG\Swagger(
 *     basePath="/tapp/api/v2",
 *     schemes={"https", "http"},
 * 		produces={"application/json"},
 * 		consumes={"application/json"},
 *      @SWG\Info(
 *         version="2.1",
 *         title="TSUTAYAアプリ(TAP) - APIドキュメント",
 *         @SWG\License(name="TSUTAYA")
 *      ),
 * )
 */


/**
 * 認証関連
 * @SWG\SecurityScheme(
 *      securityDefinition="api_key",
 *      type="apiKey",
 *      in="header",
 *      name="Authorization",
 *      description="アクセス用Key (k8AJR0NxM114Ogdl)",
 * )
 *
 * API全体で共通なクエリパラメータ
 * 認証関連
 * @SWG\Parameter(
 *      name="api_key",
 *      description="アクセス用Key",
 *      in="header",
 *      required=true,
 *      type="string"
 * )
 * @SWG\Parameter(
 *      name="workId",
 *      in="path",
 *      description="作品ID",
 *      required=true,
 *      type="string"
 * )
 * @SWG\Parameter(
 *      name="personId",
 *      in="path",
 *      description="パーソンID",
 *      required=false,
 *      type="string"
 * )
 * @SWG\Parameter(
 *      name="itemType",
 *      in="query",
 *      description="アイテム種別　（cd, dvd, book, game）※未指定でALL",
 *      required=false,
 *      type="string"
 * )
 * @SWG\Parameter(
 *      name="genreId",
 *      in="path",
 *      description="ジャンルID",
 *      required=false,
 *      type="string"
 * )
 * @SWG\Parameter(
 *      name="cccFamilyCode",
 *      in="path",
 *      description="CCCファミリーコード",
 *      required=false,
 *      type="string"
 * )
 * @SWG\Parameter(
 *      name="limit",
 *      description="取得件数",
 *      in="query",
 *      required=false,
 *      type="integer",
 *      format="int32"
 * )
 * @SWG\Parameter(
 *      name="offset",
 *      description="取得開始位置",
 *      in="query",
 *      required=false,
 *      type="integer",
 *      format="int32"
 * )
 * @SWG\Parameter(
 *      name="sort",
 *      description="並び順（新しい順(デフォルト) = new、 古い順 = old）",
 *      in="query",
 *      required=false,
 *      type="string",
 * )
 * @SWG\Parameter(
 *      name="saleType",
 *      description="販売タイプ（sell, rental, theater(上映映画)）",
 *      in="query",
 *      required=false,
 *      type="string",
 * )
 * @SWG\Parameter(
 *      name="ageLimitCheck",
 *      description="既に年齢認証済みかどうか。認証済み=true、未認証=false",
 *      in="query",
 *      required=false,
 *      type="boolean",
 * )
 * /

/**
 * @SWG\Tag(
 *   name="Top",
 *   description="トップ構成関連",
 * )
 * @SWG\Tag(
 *   name="Work",
 *   description="作品関連"
 * )
 * @SWG\Tag(
 *   name="Product",
 *   description="商品関連",
 * )
 * @SWG\Tag(
 *   name="Utility",
 *   description="",
 * )
 * @SWG\Tag(
 *   name="People",
 *   description="人材情報",
 * )
 * @SWG\Tag(
 *   name="Search",
 *   description="検索関連",
 * )
 * @SWG\Tag(
 *   name="Favorite",
 *   description="お気に入り",
 * )
 */

/**
 * @SWG\Response(
 *      response="ListJson",
 *      description="リストレスポンス",
 *      @SWG\Schema(
 *          @SWG\Property(
 *              type="boolean",
 *              property="hasNext"
 *          ),
 *          @SWG\Property(
 *              type="integer",
 *              property="totalCount"
 *          ),
 *      )
 * )
 */
/**
 * @SWG\Response(
 *      response="ReviewFormat",
 *      description="リストレスポンス",
 *      @SWG\Schema(
 *              @SWG\Property(
 *                  property="totalCount",
 *                  type="integer",
 *                  description="総件数",
 *              ),
 *              @SWG\Property(
 *                  property="averageRating",
 *                  type="number",
 *                  format="float",
 *                  description="平均スコア",
 *              ),
 *      )
 * )
 */

/**
 * @SWG\Response(
 *      response="SaleTypeHas",
 *      description="セル・レンタルの商品を保持しているか",
 *      @SWG\Schema(
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
 *      )
 * )
 */
