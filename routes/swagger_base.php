<?php
/**
 * @SWG\Swagger(
 *     basePath="/tapp/api/v1",
 *     schemes={"http", "https"},
 * 		produces={"application/json"},
 * 		consumes={"application/json"},
 *      @SWG\Info(
 *         version="1.0",
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
 *      description="作品ID",
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
 *      name="saleType",
 *      description="販売タイプ（レンタルかセルのどちからの取得）",
 *      in="query",
 *      required=false,
 *      type="string",
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
