<?php
/**
 * @SWG\Swagger(
 *     basePath="/tapp/api/v2",
 *     schemes={"http", "https"},
 * 		produces={"application/json"},
 * 		consumes={"application/json"},
 *      @SWG\Info(
 *         version="2.0",
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
 *      required=false,
 *      type="string"
 * )
 * @SWG\Parameter(
 *      name="castId",
 *      in="path",
 *      description="キャストID",
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
 *      description="並び順（お薦め(デフォルト)、新しい順、古い順）",
 *      in="query",
 *      required=false,
 *      type="string",
 * )
 * @SWG\Parameter(
 *      name="saleType",
 *      description="販売タイプ（1=sell, 2=rental） 指定しないと両方取得",
 *      in="query",
 *      required=false,
 *      type="integer",
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
 *   name="Cast",
 *   description="キャスト情報",
 * )
 * @SWG\Tag(
 *   name="Search",
 *   description="検索関連",
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
