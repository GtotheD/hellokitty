<?php
/**
 * @SWG\Swagger(
 *     basePath="/tapp/api/v1",
 *     schemes={"http", "https"},
 *     @SWG\Info(
 *         version="1.0",
 *         title="TSUTAYAアプリ(TAP) - APIドキュメント",
 *         @SWG\License(name="TSUTAYA")
 *     ),
 * )
 *
 * 認証関連
 * @SWG\SecurityScheme(
 *   securityDefinition="api_key",
 *   type="apiKey",
 *   in="header",
 *   name="Authorization: Bearer"
 * )
 *
 * API全体で共通なクエリパラメータ
 * 認証関連
 * @SWG\Parameter(
 *     name="Authorization: Bearer",
 *     description="アクセス用Token",
 *     in="header",
 *     required=true,
 *     type="string"
 * )
 * @SWG\Parameter(
 *     name="limit",
 *     description="取得件数",
 *     in="query",
 *     required=false,
 *     type="integer",
 *     format="int32"
 * )
 * @SWG\Parameter(
 *     name="offset",
 *     description="取得開始位置",
 *     in="query",
 *     required=false,
 *     type="integer",
 *     format="int32"
 * )
 * @SWG\Response(
 *      response=407,
 *      description="Success"
 * )
 *
 */