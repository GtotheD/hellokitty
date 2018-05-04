<?php
/**
 * @SWG\Get(
 *     path="/version",
 *     description="バージョンを管理する為の情報を取得する",
 *     produces={"application/json"},
 *     tags={"Utility"},
 *     security={{"api_key":{}}},
 *     @SWG\Response(
 *          response=200,
 *          description="取得成功",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="appinfo",
 *                  type="object",
 *                  description="処理結果",
 *                  @SWG\Property(
 *                      property="latestVersion",
 *                      type="string",
 *                      description="最新バージョン",
 *                  ),
 *                  @SWG\Property(
 *                      property="lowestVersion",
 *                      type="string",
 *                      description="アップデートなしでサポートする最低バージョン",
 *                  ),
 *                  @SWG\Property(
 *                      property="alert",
 *                      type="string",
 *                      description="更新必須の場合のアラート",
 *                  ),
 *                  @SWG\Property(
 *                      property="nugde",
 *                      type="string",
 *                      description="アップデートを促すメッセージ",
 *                  ),
 *                  @SWG\Property(
 *                      property="infomation",
 *                      type="array",
 *                      description="アプリ新機能紹介ページ。アプリの状態でのメッセージに関しては、API側でアプリバージョン番号を受けて切り替えるか、アプリ内ので固定メッセージを出し分ける。",
 *                  ),
 *              ),
 *              @SWG\Property(
 *                  property="update",
 *                  type="string",
 *                  description="更新日時",
 *              ),
 *              @SWG\Property(
 *                  property="version",
 *                  type="string",
 *                  description="API出力時にバージョン制御のシリアル値",
 *              )
 *          )
 *      ),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/structure/dvd/rental",
 *     description="DVD-レンタルのTOP構造を返却する",
 *     produces={"application/json"},
 *     tags={"Top"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="limit",
 *       in="query",
 *       description="表示件数",
 *       type="integer"
 *     ),
 *     @SWG\Parameter(
 *       name="offset",
 *       in="query",
 *       description="オフセット",
 *       type="integer"
 *     ),
 *     @SWG\Response(response=200, description="Success"),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/structure/dvd/sell",
 *     description="DVD-セルのTOP構造を返却する",
 *     produces={"application/json"},
 *     tags={"Top"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="limit",
 *       in="query",
 *       description="表示件数",
 *       type="integer"
 *     ),
 *     @SWG\Parameter(
 *       name="offset",
 *       in="query",
 *       description="オフセット",
 *       type="integer"
 *     ),
 *     @SWG\Response(response=200, description="Success"),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/structure/book/rental",
 *     description="DVD-セルのTOP構造を返却する",
 *     produces={"application/json"},
 *     tags={"Top"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="limit",
 *       in="query",
 *       description="表示件数",
 *       type="integer"
 *     ),
 *     @SWG\Parameter(
 *       name="offset",
 *       in="query",
 *       description="オフセット",
 *       type="integer"
 *     ),
 *     @SWG\Response(response=200, description="Success"),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/structure/book/sell",
 *     description="DVD-セルのTOP構造を返却する",
 *     produces={"application/json"},
 *     tags={"Top"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="limit",
 *       in="query",
 *       description="表示件数",
 *       type="integer"
 *     ),
 *     @SWG\Parameter(
 *       name="offset",
 *       in="query",
 *       description="オフセット",
 *       type="integer"
 *     ),
 *     @SWG\Response(response=200, description="Success"),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/structure/cd/rental",
 *     description="DVD-セルのTOP構造を返却する",
 *     produces={"application/json"},
 *     tags={"Top"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="limit",
 *       in="query",
 *       description="表示件数",
 *       type="integer"
 *     ),
 *     @SWG\Parameter(
 *       name="offset",
 *       in="query",
 *       description="オフセット",
 *       type="integer"
 *     ),
 *     @SWG\Response(response=200, description="Success"),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/structure/cd/sell",
 *     description="DVD-セルのTOP構造を返却する",
 *     produces={"application/json"},
 *     tags={"Top"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="limit",
 *       in="query",
 *       description="表示件数",
 *       type="integer"
 *     ),
 *     @SWG\Parameter(
 *       name="offset",
 *       in="query",
 *       description="オフセット",
 *       type="integer"
 *     ),
 *     @SWG\Response(response=200, description="Success"),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/structure/game/sell",
 *     description="DVD-セルのTOP構造を返却する",
 *     produces={"application/json"},
 *     tags={"Top"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="limit",
 *       in="query",
 *       description="表示件数",
 *       type="integer"
 *     ),
 *     @SWG\Parameter(
 *       name="offset",
 *       in="query",
 *       description="オフセット",
 *       type="integer"
 *     ),
 *     @SWG\Response(response=200, description="Success"),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */


/**
 * @SWG\Get(
 *     path="/fixed/banner",
 *     description="固定のバナー枠内情報の返却",
 *     produces={"application/json"},
 *     tags={"Top"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="isLoggedIn",
 *       in="query",
 *       description="ログイン状態",
 *       type="boolean"
 *     ),
 *     @SWG\Parameter(
 *       name="limit",
 *       in="query",
 *       description="表示件数",
 *       type="integer"
 *     ),
 *     @SWG\Parameter(
 *       name="offset",
 *       in="query",
 *       description="オフセット",
 *       type="integer"
 *     ),
 *     @SWG\Response(response=200, description="Success"),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */


/**
 * @SWG\Get(
 *     path="/section/dvd/rental/{sectionName}",
 *     description="DVD-レンタルのリスト情報を返却する",
 *     produces={"application/json"},
 *     tags={"Top"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="sectionName",
 *       in="path",
 *       description="セクション名",
 *       required=true,
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="limit",
 *       in="query",
 *       description="表示件数",
 *       type="integer"
 *     ),
 *     @SWG\Parameter(
 *       name="offset",
 *       in="query",
 *       description="オフセット",
 *       type="integer"
 *     ),
 *     @SWG\Response(response=200, description="Success"),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */

/**
 * @SWG\Get(
 *     path="/section/dvd/sell/{sectionName}",
 *     description="DVD-セルのリスト情報を返却する",
 *     produces={"application/json"},
 *     tags={"Top"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="sectionName",
 *       in="path",
 *       description="セクション名",
 *       required=true,
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="limit",
 *       in="query",
 *       description="表示件数",
 *       type="integer"
 *     ),
 *     @SWG\Parameter(
 *       name="offset",
 *       in="query",
 *       description="オフセット",
 *       type="integer"
 *     ),
 *     @SWG\Response(response=200, description="Success"),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */

/**
 * @SWG\Get(
 *     path="/section/book/rental/{sectionName}",
 *     description="DVD-セルのリスト情報を返却する",
 *     produces={"application/json"},
 *     tags={"Top"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="sectionName",
 *       in="path",
 *       description="セクション名",
 *       required=true,
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="limit",
 *       in="query",
 *       description="表示件数",
 *       type="integer"
 *     ),
 *     @SWG\Parameter(
 *       name="offset",
 *       in="query",
 *       description="オフセット",
 *       type="integer"
 *     ),
 *     @SWG\Response(response=200, description="Success"),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */

/**
 * @SWG\Get(
 *     path="/section/book/sell/{sectionName}",
 *     description="DVD-セルのTOP構造を返却する",
 *     produces={"application/json"},
 *     tags={"Top"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="sectionName",
 *       in="path",
 *       description="セクション名",
 *       required=true,
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="limit",
 *       in="query",
 *       description="表示件数",
 *       type="integer"
 *     ),
 *     @SWG\Parameter(
 *       name="offset",
 *       in="query",
 *       description="オフセット",
 *       type="integer"
 *     ),
 *     @SWG\Response(response=200, description="Success"),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */

/**
 * @SWG\Get(
 *     path="/section/cd/rental/{sectionName}",
 *     description="DVD-セルのTOP構造を返却する",
 *     produces={"application/json"},
 *     tags={"Top"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="sectionName",
 *       in="path",
 *       description="セクション名",
 *       required=true,
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="limit",
 *       in="query",
 *       description="表示件数",
 *       type="integer"
 *     ),
 *     @SWG\Parameter(
 *       name="offset",
 *       in="query",
 *       description="オフセット",
 *       type="integer"
 *     ),
 *     @SWG\Response(response=200, description="Success"),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */

/**
 * @SWG\Get(
 *     path="/section/cd/sell/{sectionName}",
 *     description="DVD-セルのTOP構造を返却する",
 *     produces={"application/json"},
 *     tags={"Top"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="sectionName",
 *       in="path",
 *       description="セクション名",
 *       required=true,
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="limit",
 *       in="query",
 *       description="表示件数",
 *       type="integer"
 *     ),
 *     @SWG\Parameter(
 *       name="offset",
 *       in="query",
 *       description="オフセット",
 *       type="integer"
 *     ),
 *     @SWG\Response(response=200, description="Success"),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */

/**
 * @SWG\Get(
 *     path="/section/game/sell/{sectionName}",
 *     description="DVD-セルのTOP構造を返却する",
 *     produces={"application/json"},
 *     tags={"Top"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="sectionName",
 *       in="path",
 *       description="セクション名",
 *       required=true,
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="limit",
 *       in="query",
 *       description="表示件数",
 *       type="integer"
 *     ),
 *     @SWG\Parameter(
 *       name="offset",
 *       in="query",
 *       description="オフセット",
 *       type="integer"
 *     ),
 *     @SWG\Response(response=200, description="Success"),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */

/**
 * @SWG\Get(
 *     path="/section/banner/{sectionName}",
 *     description="DVD-セルのTOP構造を返却する",
 *     produces={"application/json"},
 *     tags={"Top"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="sectionName",
 *       in="path",
 *       description="セクション名",
 *       required=true,
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="isLoggedIn",
 *       in="query",
 *       description="ログイン状態",
 *       type="boolean"
 *     ),
 *     @SWG\Parameter(
 *       name="limit",
 *       in="query",
 *       description="表示件数",
 *       type="integer"
 *     ),
 *     @SWG\Parameter(
 *       name="offset",
 *       in="query",
 *       description="オフセット",
 *       type="integer"
 *     ),
 *     @SWG\Response(response=200, description="Success"),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/section/ranking/{codeType}/{code}/{period}",
 *     description="DVD-セルのTOP構造を返却する（hasNextは最終レスポンスがlimit以下だった場合にfalseとする）",
 *     produces={"application/json"},
 *     tags={"Top"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="codeType",
 *       in="path",
 *       description="コード種別(himo|agg)：Himoジャンルコードもしくは、ランキング集約コード",
 *       required=true,
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="code",
 *       in="path",
 *       description="HimoジャンルID（例：EXT0000005F2）もしくは、ランキング集約コード（例：M086　月間コミックレンタル）",
 *       required=true,
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="period",
 *       in="path",
 *       description="集計期間：実行月からn月前の月頭から月終わりまで",
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="supplementVisible",
 *       in="query",
 *       description="出演者・アーティスト・著者・機種等を表示/非表示を切り替える為のフラグ。trueにすると非表示になる。",
 *       type="boolean"
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="aggregationPeriod",
 *                  type="string",
 *                  description="集計期間（デイリー：yyyy/mm/dd集計 週間：yyyy/mm/dd～yyyy/mm/dd集計 月間：yyyy/mm集計）",
 *              ),
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/WorkNarrowRelease"),
 *                  description="作品情報",
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/section/release/manual/{tapCategoryId}/{releaseDateTo}",
 *     description="手動運用のリリースカレンダー情報を返却する（TAP API経由）",
 *     produces={"application/json"},
 *     tags={"Top"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="tapCategoryId",
 *       in="path",
 *       description="TAPで指定するカテゴリ識別子（例：01）",
 *       required=true,
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="releaseDateTo",
 *       in="path",
 *       description="リリース開始起算日（例：20171022）",
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="supplementVisible",
 *       in="query",
 *       description="出演者・アーティスト・著者・機種等を表示/非表示を切り替える為のフラグ。trueにすると非表示になる。",
 *       type="boolean"
 *     ),
 *     @SWG\Response(response=200, description="Success"),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/section/release/auto/{largeGenreCd}/{storeProductItemCd}",
 *     description="自動取得のリリースカレンダー情報を返却する（TWSのサーチを利用）",
 *     produces={"application/json"},
 *     tags={"Top"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="largeGenreCd",
 *       in="path",
 *       description="大ジャンルコード（25）",
 *       required=true,
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="storeProductItemCd",
 *       in="path",
 *       description="店舗取扱いアイテムコード（221）",
 *       required=true,
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="supplementVisible",
 *       in="query",
 *       description="出演者・アーティスト・著者・機種等を表示/非表示を切り替える為のフラグ。trueにすると非表示になる。",
 *       type="boolean"
 *     ),
 *     @SWG\Response(response=200, description="Success"),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/work/{workId}",
 *     description="作品基本情報を取得する",
 *     tags={"Work"},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Parameter(
 *       name="saleType",
 *       in="query",
 *       description="販売タイプ（sell, rental） ※デフォルトはrental",
 *       type="string"
 *     ),
 *     @SWG\Parameter(ref="#/parameters/ageLimitCheck"),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="data",
 *                  type="object",
 *                  ref="#/definitions/Work",
 *                  description="作品情報",
 *              ),
 *          )
 *      ),
 *     @SWG\Response(
 *          response=202,
 *          description="Age Limit Error Response",
 *          @SWG\Property(
 *              property="rows",
 *              type="object",
 *              @SWG\Property(property="status",type="string",description="ステータスコード（error）"),
 *              @SWG\Property(property="message",type="string",description="ステータスコードの内容"),
 *              description="エラー情報",
 *          ),
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/work/{workId}/products",
 *     description="商品一覧情報取得",
 *     tags={"Work"},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Parameter(ref="#/parameters/saleType"),
 *     @SWG\Parameter(ref="#/parameters/sort"),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/ProductNarrow"),
 *                  description="作品情報",
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/work/{workId}/products/rental",
 *     description="商品一覧情報取得（DVDレンタル時のグルーピング（問い合わせ時のLimit数がおかしくなる為にグルーピングが必要））",
 *     tags={"Work"},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/ProductGroup"),
 *                  description="作品情報",
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/work/{workId}/products/has",
 *     description="Himo作品ID作品検索",
 *     tags={"Work"},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/ageLimitCheck"),
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="data",
 *                  type="object",
 *                  ref="$/definitions/WorkNarrowSearch",
 *                  description="作品情報",
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/work/{workId}/people",
 *     description="キャストスタッフ一覧取得",
 *     tags={"Work"},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Parameter(ref="#/parameters/saleType"),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(
 *                       @SWG\Property(property="personId",type="string"),
 *                       @SWG\Property(property="personName",type="string"),
 *                       @SWG\Property(property="roleId",type="string"),
 *                       @SWG\Property(property="roleName",type="string"),
 *                  ),
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/work/{workId}/series",
 *     description="シリーズ作品取得",
 *     tags={"Work"},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Parameter(ref="#/parameters/saleType"),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/WorkNarrow"),
 *                  description="作品情報",
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/work/{workId}/review/filmarks",
 *     description="Filmarksレビュー取得",
 *     tags={"Work"},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ReviewFormat",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  description="レビュー",
 *                  @SWG\Items(ref="#/definitions/Review"),
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/work/{workId}/review/discas",
 *     description="DISCASレビュー取得",
 *     tags={"Work"},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ReviewFormat",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  description="レビュー",
 *                  @SWG\Items(ref="#/definitions/Review"),
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/work/{workId}/review/tol",
 *     description="TOLレビュー取得",
 *     tags={"Work"},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ReviewFormat",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  description="レビュー",
 *                  @SWG\Items(ref="#/definitions/Review"),
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/work/{workId}/relation/works",
 *     description="関連作品取得",
 *     tags={"Work"},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/WorkNarrow"),
 *                  description="作品情報",
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/work/{workId}/relation/pics",
 *     description="関連画像取得",
 *     tags={"Work"},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(
 *                       type="string",
 *                  ),
 *                  description="作品情報",
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/work/{workId}/relation/artist",
 *     description="関連アーティスト一覧取得",
 *     tags={"Work"},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(
 *                       @SWG\Property(property="personId",type="string"),
 *                       @SWG\Property(property="personName",type="string"),
 *                  ),
 *                  description="アーティスト情報",
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/people/{personId}",
 *     description="人物関連作品取得",
 *     tags={"People"},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/personId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Parameter(ref="#/parameters/saleType"),
 *     @SWG\Parameter(ref="#/parameters/itemType"),
 *     @SWG\Parameter(ref="#/parameters/sort"),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/WorkNarrow"),
 *                  description="作品情報",
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/genre/{genreId}",
 *     description="ジャンルからの作品一覧取得",
 *     tags={"Genre"},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/genreId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Parameter(ref="#/parameters/saleType"),
 *     @SWG\Parameter(
 *       name="sort",
 *       in="path",
 *       description="並び順（お薦め(デフォルト)、新しい順 = new、 古い順 = old）",
 *       type="string"
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/WorkNarrow"),
 *                  description="作品情報",
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/work/{workId}/recommend/other",
 *     description="お薦め作品一覧取得",
 *     tags={"Work"},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Parameter(ref="#/parameters/saleType"),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/WorkNarrow"),
 *                  description="作品情報",
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/work/{workId}/recommend/author",
 *     description="著者作品一覧取得",
 *     tags={"Work"},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Parameter(ref="#/parameters/sort"),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/WorkNarrow"),
 *                  description="作品情報",
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/work/{workId}/recommend/artist",
 *     description="アーティスト作品一覧取得",
 *     tags={"Work"},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Parameter(ref="#/parameters/sort"),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/WorkNarrow"),
 *                  description="作品情報",
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/convert/work/{idType}/{id}",
 *     description="HimoID取得 (CCC商品IDと商品IDから作品ID取得を取得する)",
 *     tags={"Work"},
 *     produces={"application/json"},
 *     @SWG\Parameter(
 *       name="idType",
 *       in="path",
 *       description="IDタイプ=Himo作品ID = workId、CCC作品ID= cccWorkCd、JAN= jan、レンタル商品コード= rentalProductId、URLCD= urlCd",
 *       required=true,
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="id",
 *       in="path",
 *       description="各種ID",
 *       type="string"
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="workId",
 *                  type="string",
 *                  description="作品ID",
 *              ),
 *              @SWG\Property(
 *                  property="itemType",
 *                  type="string",
 *                  description="アイテム種別　（cd, dvd, book, game）",
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/product/{productUniqueId}",
 *     description="商品詳細情報取得",
 *     tags={"Product"},
 *     produces={"application/json"},
 *     @SWG\Parameter(
 *       name="productUniqueId",
 *       in="path",
 *       description="商品ID(product.id でproduct.product_idではない)",
 *       type="string",
 *       required=true
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="data",
 *                  type="object",
 *                  ref="#/definitions/Product",
 *                  description="商品詳細情報",
 *              ),
 *          )
 *     ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/product/stock/{storeCd}/{productKey}",
 *     description="在庫確認",
 *     tags={"Product"},
 *     produces={"application/json"},
 *     @SWG\Parameter(
 *       name="storeCd",
 *       in="path",
 *       description="店舗コード",
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="productKey",
 *       in="path",
 *       description="レンタルの場合はレンタル商品コード、セルの場合はJANコード",
 *       type="string"
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="stockStatus",
 *                  type="integer",
 *                  description="在庫ステータス（0=取り扱いなし、1=在庫なし、2=在庫あり）",
 *              ),
 *              @SWG\Property(
 *                  property="message",
 *                  type="string",
 *                  description="メッセージ",
 *              ),
 *              @SWG\Property(
 *                  property="lastUpdate",
 *                  type="string",
 *                  description="最終更新日時（yyyy-mm-dd hh:ii:ss）",
 *              ),
 *          )
 *     ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/search/{keyword}",
 *     description="キーワード検索",
 *     tags={"Search"},
 *     produces={"application/json"},
 *     @SWG\Parameter(
 *       name="keyword",
 *       in="path",
 *       description="キーワード",
 *       required=true,
 *       type="string"
 *     ),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Parameter(
 *       name="sort",
 *       in="query",
 *       description="並び順（お薦め(デフォルト)、新しい順 = new、 古い順 = old）",
 *       type="string"
 *     ),
 *     @SWG\Parameter(ref="#/parameters/itemType"),
 *     @SWG\Parameter(
 *       name="periodType",
 *       in="query",
 *       description="期間指定（rental3, rental12, sell3, sell12）※未指定でALL",
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="adultFlg",
 *       in="query",
 *       description="アダルト取得フラグ（デフォルト=false）",
 *       type="boolean"
 *     ),
 *     @SWG\Parameter(ref="#/parameters/ageLimitCheck"),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="counts",
 *                  type="object",
 *                  ref="#/definitions/Count",
 *                  description="カウント関連",
 *              ),
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/WorkNarrowSearch"),
 *                  description="作品情報",
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/search/suggest/{keyword}",
 *     description="キーワードサジェスト",
 *     tags={"Search"},
 *     produces={"application/json"},
 *     @SWG\Parameter(
 *       name="keyword",
 *       in="path",
 *       description="",
 *       type="string"
 *     ),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(
 *                       type="string",
 *                  ),
 *                  description="作品情報",
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */

/**
 * @SWG\Get(
 *     path="/release/{month}/{genreId}",
 *     description="リリース情報（商品名を出す為、対象の商品を特定時に、ジャンルIDからセルレンタル区分を判別し特定する）",
 *     tags={"Release"},
 *     produces={"application/json"},
 *     @SWG\Parameter(
 *       name="month",
 *       in="path",
 *       description="月（前月=last、今月=this、来月=next）",
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="genreId",
 *       in="path",
 *       description="ジャンルID（このAPI独自のID）",
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="onlyReleased",
 *       in="query",
 *       description="リリース済みのみ",
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="cdFormatType",
 *       in="path",
 *       description="CDのアルバム種別（single, album）",
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="sort",
 *       in="query",
 *       description="並び順（お薦め(デフォルト)、新しい順 = new、 古い順 = old）",
 *       type="string"
 *     ),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="baseMonth",
 *                  type="string",
 *                  description="基準月（yyyy-m 2018-09）",
 *              ),
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/WorkNarrowRelease"),
 *                  description="作品情報",
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/ranking/{codeType}/{code}/{period}",
 *     description="DVD-セルのTOP構造を返却する（hasNextは最終レスポンスがlimit以下だった場合にfalseとする）",
 *     produces={"application/json"},
 *     tags={"ranking"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="codeType",
 *       in="path",
 *       description="コード種別(himo|agg)：Himoジャンルコードもしくは、ランキング集約コード",
 *       required=true,
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="code",
 *       in="path",
 *       description="HimoジャンルID（例：EXT0000005F2）もしくは、ランキング集約コード（例：M086　月間コミックレンタル）",
 *       required=true,
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="period",
 *       in="path",
 *       description="集計期間：実行月からn月前の月頭から月終わりまで",
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="supplementVisible",
 *       in="query",
 *       description="出演者・アーティスト・著者・機種等を表示/非表示を切り替える為のフラグ。trueにすると非表示になる。",
 *       type="boolean"
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="aggregationPeriod",
 *                  type="string",
 *                  description="集計期間（デイリー：yyyy/mm/dd集計 週間：yyyy/mm/dd～yyyy/mm/dd集計 月間：yyyy/mm集計）",
 *              ),
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/WorkNarrowRelease"),
 *                  description="作品情報",
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
