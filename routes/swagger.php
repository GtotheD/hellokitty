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
 *                      type="string",
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
 *     @SWG\Parameter(
 *       name="premium",
 *       in="query",
 *       description="プレミアムセクション出力有無",
 *       type="boolean"
 *     ),
 *     @SWG\Parameter(
 *       name="thousandTag",
 *       in="query",
 *       description="1000タグ",
 *       type="boolean"
 *     ),
 *     @SWG\Parameter(
 *       name="version",
 *       in="query",
 *       description="appバージョン(x.x.x)",
 *       type="string"
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
 *     path="/structure/premium/dvd/rental",
 *     description="プレミアム用のDVD-セルのTOP構造を返却する",
 *     produces={"application/json"},
 *     tags={"Top"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="version",
 *       in="query",
 *       description="appバージョン(x.x.x)",
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
 *     @SWG\Parameter(
 *       name="recommend",
 *       in="query",
 *       description="recommendセクション表示フラグ",
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
 *     @SWG\Parameter(
 *       name="recommend",
 *       in="query",
 *       description="recommendセクション表示フラグ",
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
 *     @SWG\Parameter(
 *       name="thousandTag",
 *       in="query",
 *       description="1000タグ",
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
 *     @SWG\Parameter(
 *       name="premium",
 *       in="query",
 *       description="プレミアムセクション出力有無",
 *       type="boolean"
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/WorkForSection"),
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
 *     path="/section/premium/dvd/rental/{sectionName}",
 *     description="プレミアムDVD-レンタルのリスト情報を返却する",
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
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/WorkForSection"),
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
 *     path="/section/premium/dvd/rental/movie/{sectionName}",
 *     description="プレミアム用　プレミアムで映画漬け情報を返却。単一の商品のみ返却。",
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
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/WorkForPickled"),
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
 * @SWG\Post(
 *     path="/section/premium/dvd/rental/recommend",
 *     description="プレミアム用　SectionType=6のコンテンツ時に、対応する作品を返する。",
 *     produces={"application/json"},
 *     tags={"Top"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="body",
 *       in="body",
 *       description="レンタルしたurlCd。ここで指定されたurlCdの作品は返却から除外される。",
 *       type="array",
 *       @SWG\Schema(
 *         @SWG\Property(
 *             property="urlCd",
 *             type="array",
 *             description="「urlCd」を渡し、該当のurlCdは返却リストから除外する",
 *             @SWG\Items(
 *                  type="string",
 *             ),
 *         )
 *       )
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
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/WorkForSection"),
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
 *     path="/section/premium/dvd/rental/recommend/net",
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
 *     path="/section/banner/recommend",
 *     description="【API】レコメンドバナー用API",
 *     produces={"application/json"},
 *     tags={"Top"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="image",
 *       required=true,
 *       in="query",
 *       description="BOOKレコメンド用バナー画像名",
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="imageNew",
 *       in="query",
 *       description="BOOKレコメンド用バナー画像名（新着あり）",
 *       type="string"
 *     ),
 *     @SWG\Response(response=200, description="Success"),
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
 *     @SWG\Parameter(
 *       name="premium",
 *       in="query",
 *       description="プレミアムフラグの出力要否。
 * 出力しない：false（デフォルト）、出力する：true",
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
 *                  @SWG\Items(ref="#/definitions/WorkNarrowRanking"),
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
 *     @SWG\Parameter(
 *       name="premium",
 *       in="query",
 *       description="プレミアムフラグの出力要否。
 * 出力しない：false（デフォルト）
 * 出力する：true",
 *       type="boolean"
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/WorkForSection"),
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
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/WorkForReleaseAuto"),
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
 *     path="/section/release/himo/{periodType}/{tapGenreId}",
 *     description="自動取得のリリースカレンダー情報を返却する（HiMOを利用）",
 *     produces={"application/json"},
 *     tags={"Top"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="periodType",
 *       in="path",
 *       description="リリカレ表示種別（newest|lastest）",
 *       required=true,
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="tapGenreId",
 *       in="path",
 *       description="リリカレ用ジャンルID（newest：1|9|17|22|28|39|51、lastest：11|12|13|24|25）",
 *       required=true,
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="supplementVisible",
 *       in="query",
 *       description="出演者・アーティスト・著者・機種等を表示/非表示を切り替える為のフラグ。trueにすると非表示になる。",
 *       type="boolean"
 *     ),
 *     @SWG\Parameter(
 *       name="premium",
 *       in="query",
 *       description="プレミアムフラグの出力要否。
 * 出力しない：false（デフォルト）
 * 出力する：true",
 *       type="boolean"
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/WorkForReleaseHimo"),
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
 *     path="/work/{workId}",
 *     description="作品基本情報を取得する",
 *     tags={"Work"},
 *     produces={"application/json"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Parameter(
 *       name="saleType",
 *       in="query",
 *       description="販売タイプ（sell, rental, theater(上映映画)） ※デフォルトはrental",
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
 * @SWG\Post(
 *     path="/work/bulk",
 *     description="作品詳細一括取得
 * 最大取得数30件まで",
 *     produces={"application/json"},
 *     tags={"Work"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="body",
 *       in="body",
 *       description="",
 *       type="array",
 *       @SWG\Schema(
 *         @SWG\Property(
 *             property="saleType",
 *             type="string",
 *             description="セルレンタル区分（sell, rental）",
 *             example= "rental",
 *         ),
 *         @SWG\Property(
 *             property="idType",
 *             type="string",
 *             description="jan又はrentalProductId",
 *             example= "rentalProductId",
 *         ),
 *         @SWG\Property(
 *             property="ageLimitCheck",
 *             type="boolean",
 *             description="既に年齢認証済みかどうか。認証済み=true、未認証=false",
 *         ),
 *         @SWG\Property(
 *             property="ids",
 *             type="array",
 *             description="「WorkId」もしくは「urlCd」",
 *             @SWG\Items(
 *                  type="string",
 *             ),
 *         )
 *       )
 *     ),
 *     @SWG\Parameter(
 *       name="premium",
 *       in="query",
 *       description="プレミアムセクション出力有無",
 *       type="boolean"
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/WorkNarrowBulk"),
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
 *     path="/work/tag/{thousandTag}",
 *     description="タグ作品取得",
 *     tags={"Work"},
 *     produces={"application/json"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *          name="thousandTag",
 *          description="1000タグ",
 *          in="path",
 *          required=true,
 *          type="string"
 *     ),
 *     @SWG\Parameter(ref="#/parameters/saleType"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Parameter(
 *       name="premium",
 *       in="query",
 *       description="プレミアムセクション出力有無",
 *       type="boolean"
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJsonThousandTag",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/WorkThousandTag"),
 *                  description="1000タグ作品取得",
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/convert/tags",
 *     description="タグ名変換",
 *     produces={"application/json"},
 *     tags={"Work"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *          in="query",
 *          name="tags[]",
 *          description="タグ名",
 *          required=true,
 *          type="array",
 *          collectionFormat="multi",
 *          @SWG\Items(
 *              type = "string",
 *          ),
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/WorkConvertTag"),
 *                  description="レスポンス情報",
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/work/{workId}/products",
 *     description="商品一覧情報取得",
 *     tags={"Work"},
 *     produces={"application/json"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Parameter(ref="#/parameters/saleType"),
 *     @SWG\Parameter(ref="#/parameters/sort"),
 *     @SWG\Parameter(
 *       name="isDummy",
 *       in="query",
 *       description="dummyデータを含める",
 *       type="boolean"
 *     ),
 *     @SWG\Parameter(
 *       name="taxIn",
 *       in="query",
 *       description="税込金額（true, false)",
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
 *     security={{"api_key":{}}},
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
 *     security={{"api_key":{}}},
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
 *     path="/work/{workId}/products/svod",
 *     description="動画配信商品検索",
 *     tags={"Work"},
 *     security={{"api_key":{}}},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Parameter(
 *       name="sort",
 *       in="query",
 *       description="並び順（話数の新しい順 = new、 話数の古い順 = old（デフォルト））",
 *       type="string",
 *       required=false
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="data",
 *                  type="object",
 *                  ref="$/definitions/SvodProductNarrow",
 *                  description="動画配信商品",
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
 *     security={{"api_key":{}}},
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
 *     security={{"api_key":{}}},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Parameter(ref="#/parameters/saleType"),
 *     @SWG\Parameter(ref="#/parameters/ageLimitCheck"),
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
 *     security={{"api_key":{}}},
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
 *     security={{"api_key":{}}},
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
 *     security={{"api_key":{}}},
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
 *     path="/work/{workId}/review/comicspace",
 *     description="Comicspaceレビュー取得",
 *     tags={"Work"},
 *     security={{"api_key":{}}},
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
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/work/{workId}/relation/works",
 *     description="関連作品取得",
 *     tags={"Work"},
 *     security={{"api_key":{}}},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Parameter(ref="#/parameters/ageLimitCheck"),
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
 *     security={{"api_key":{}}},
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
 *     security={{"api_key":{}}},
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
 *     path="/work/{workId}/relation/trailer",
 *     description="関連動画",
 *     tags={"Work"},
 *     security={{"api_key":{}}},
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
 *                       @SWG\Property(property="displayTitle",type="string"),
 *                       @SWG\Property(property="trailerUrl",type="string"),
 *                       @SWG\Property(property="youtubeId",type="string"),
 *                       @SWG\Property(property="thumbnail",type="string"),
 *                  ),
 *                  description="予告編",
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
 *     security={{"api_key":{}}},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/personId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Parameter(
 *       name="saleType",
 *       in="query",
 *       description="販売タイプ（sell, rental, theater)",
 *       type="string"
 *     ),
 *     @SWG\Parameter(ref="#/parameters/itemType"),
 *     @SWG\Parameter(ref="#/parameters/sort"),
 *     @SWG\Parameter(ref="#/parameters/ageLimitCheck"),
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
 *     security={{"api_key":{}}},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/genreId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Parameter(
 *       name="saleType",
 *       in="query",
 *       description="販売タイプ（sell, rental, theater) ※theater指定でもレンタルのみを返却",
 *       type="string"
 *     ),
 *     @SWG\Parameter(ref="#/parameters/ageLimitCheck"),
 *     @SWG\Parameter(
 *       name="sort",
 *       in="query",
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
 *     security={{"api_key":{}}},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Parameter(ref="#/parameters/saleType"),
 *     @SWG\Parameter(ref="#/parameters/ageLimitCheck"),
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
 *     security={{"api_key":{}}},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Parameter(ref="#/parameters/sort"),
 *     @SWG\Parameter(ref="#/parameters/ageLimitCheck"),
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
 *     security={{"api_key":{}}},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Parameter(ref="#/parameters/sort"),
 *     @SWG\Parameter(ref="#/parameters/ageLimitCheck"),
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
 *     path="/work/{workId}/recommend/theater",
 *     description="上映映画関連お薦め作品一覧取得",
 *     tags={"Work"},
 *     security={{"api_key":{}}},
 *     produces={"application/json"},
 *     @SWG\Parameter(ref="#/parameters/workId"),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Parameter(ref="#/parameters/ageLimitCheck"),
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
 *     security={{"api_key":{}}},
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
 *     security={{"api_key":{}}},
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
 *     security={{"api_key":{}}},
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
 *                  property="rentalPossibleDay",
 *                  type="string",
 *                  description="返却予定日（yyyy-mm-dd）",
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
 *     security={{"api_key":{}}},
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
 *     security={{"api_key":{}}},
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
 *     security={{"api_key":{}}},
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
 *       type="boolean"
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
 *     @SWG\Parameter(
 *       name="update",
 *       in="query",
 *       description="trueの場合、updateを付加及び、結果が0件でも200で返却する。（デフォルトはfalse）",
 *       type="boolean"
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
 *                  @SWG\Items(ref="#/definitions/WorkNarrowRanking"),
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
 *     path="/release/static/{month}/{genreId}",
 *     description="リリース情報（商品名を出す為、対象の商品を特定時に、ジャンルIDからセルレンタル区分を判別し特定する）",
 *     tags={"Release"},
 *     security={{"api_key":{}}},
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
 *                  @SWG\Items(ref="#/definitions/WorkNarrowRanking"),
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
 *     @SWG\Parameter(
 *       name="page",
 *       in="query",
 *       description="ページ番号",
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="limit",
 *       in="query",
 *       description="１ページ毎の取得件数",
 *       type="string"
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
 *                  @SWG\Items(ref="#/definitions/WorkNarrowRanking"),
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
 *     path="/release/has/recommend/",
 *     description="TSUTAYA一押しの有無一覧",
 *     produces={"application/json"},
 *     tags={"Release"},
 *     security={{"api_key":{}}},
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="data",
 *                  type="object",
 *              @SWG\Property(
 *                  property="last",
 *                  type="array",
 *                  @SWG\Items(
 *                      @SWG\Property(
 *                          property="genreId",
 *                          type="string",
 *                          description="独自のジャンルID"
 *                      ),
 *                      @SWG\Property(
 *                          property="exist",
 *                          type="string",
 *                          description="データの有無"
 *                      ),
 *                  ),
 *              ),
 *              @SWG\Property(
 *                  property="this",
 *                  type="array",
 *                  @SWG\Items(
 *                      @SWG\Property(
 *                          property="genreId",
 *                          type="string",
 *                          description="独自のジャンルID"
 *                      ),
 *                      @SWG\Property(
 *                          property="exist",
 *                          type="string",
 *                          description="データの有無"
 *                      ),
 *                  ),
 *              ),
 *              @SWG\Property(
 *                  property="next",
 *                  type="array",
 *                  @SWG\Items(
 *                      @SWG\Property(
 *                          property="genreId",
 *                          type="string",
 *                          description="独自のジャンルID"
 *                      ),
 *                      @SWG\Property(
 *                          property="exist",
 *                          type="string",
 *                          description="データの有無"
 *                      ),
 *                  ),
 *              ),
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
 * @SWG\Post(
 *     path="/favorite/list",
 *     description="お気に入り一覧取得",
 *     produces={"application/json"},
 *     tags={"Favorite"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="sort",
 *       in="query",
 *       description="並び順 new(新しい順) ※デフォルト / old(古い順）",
 *       type="string"
 *     ),
 *     @SWG\Parameter(
 *       name="body",
 *       in="body",
 *       description="",
 *       type="array",
 *       @SWG\Schema(
 *         @SWG\Property(
 *             property="tlsc",
 *             type="string",
 *             description="ユーザー識別番号(TLSC)",
 *         ),
 *         @SWG\Property(
 *             property="version",
 *             type="string",
 *             description="お気に入りデータのバージョン",
 *         ),
 *       )
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          ref="$/responses/ListJson",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="version",
 *                  type="string",
 *                  description="取得した際のバージョン",
 *              ),
 *              @SWG\Property(
 *                  property="isUpdate",
 *                  type="boolean",
 *                  description="更新フラグ",
 *              ),
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(ref="#/definitions/favorite"),
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
 * @SWG\Post(
 *     path="/favorite/add",
 *     description="お気に入り追加
 *  件数オーバー時はエラーを返却",
 *     produces={"application/json"},
 *     tags={"Favorite"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="body",
 *       in="body",
 *       description="",
 *       type="array",
 *       @SWG\Schema(
 *         @SWG\Property(
 *             property="tlsc",
 *             type="string",
 *             description="ユーザー識別番号(TLSC)",
 *         ),
 *         @SWG\Property(
 *             property="id",
 *             type="string",
 *             description="「WorkId」もしくは「urlCd」",
 *         )
 *       )
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          @SWG\Schema(
 *              ref="#/definitions/favorite_status",
 *          )
 *      ),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Post(
 *     path="/favorite/add/merge",
 *     description="お気に入り追加マージモード
 *  件数をオーバー時は古いものを削除してマージする",
 *     produces={"application/json"},
 *     tags={"Favorite"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="body",
 *       in="body",
 *       description="",
 *       type="array",
 *       @SWG\Schema(
 *         @SWG\Property(
 *             property="tlsc",
 *             type="string",
 *             description="ユーザー識別番号(TLSC)",
 *         ),
 *         @SWG\Property(
 *             property="ids",
 *             type="array",
 *             description="「WorkId」もしくは「urlCd」",
 *             @SWG\Items(
 *               @SWG\Property(property="id",type="string"),
 *               @SWG\Property(property="appCreatedAt",type="string")
 *             ),
 *         )
 *       )
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success（status=errorは返却なし。）",
 *          @SWG\Schema(
 *              ref="#/definitions/favorite_status",
 *          )
 *      ),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Post(
 *     path="/favorite/delete",
 *     description="お気に入り削除
 * 削除対象がなくても200で返却。エラーコード返却はしない。",
 *     produces={"application/json"},
 *     tags={"Favorite"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="body",
 *       in="body",
 *       description="",
 *       type="array",
 *       @SWG\Schema(
 *         @SWG\Property(
 *             property="tlsc",
 *             type="string",
 *             description="ユーザー識別番号(TLSC)",
 *         ),
 *         @SWG\Property(
 *             property="ids",
 *             type="array",
 *             description="IDリスト",
 *             @SWG\Items(
 *                type="string"
 *            ),
 *         )
 *       )
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          @SWG\Schema(
 *              ref="#/definitions/favorite_status",
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */

/**
 * @SWG\Post(
 *     path="/coupon/list",
 *     description="ワンタイムクーポン一覧取得",
 *     produces={"application/json"},
 *     tags={"Coupon"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="body",
 *       in="body",
 *       description="",
 *       type="array",
 *       @SWG\Schema(
 *         @SWG\Property(
 *             property="storeCds",
 *             type="array",
 *             description="店舗コード",
 *             @SWG\Items(
 *                  type="string",
 *             ),
 *         ),
 *       )
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="success",
 *         @SWG\Schema(
 *             @SWG\Property(
 *                 property="requestDate",
 *                 type="string",
 *                 description="リクエスト日時",
 *             ),
 *             @SWG\Property(
 *                 property="rows",
 *                 type="array",
 *                 description="店舗毎のクーポン情報",
 *                 @SWG\Items(
 *                     @SWG\Property(
 *                         property="storeCd",
 *                         type="string",
 *                         description="店舗コード",
 *                      ),
 *                     @SWG\Property(
 *                         property="coupons",
 *                         type="array",
 *                         description="クーポン情報",
 *                         @SWG\Items(ref="#/definitions/coupon"),
 *                     ),
 *                 )
 *             ),
 *         )
 *     ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=400, description="Bad Request"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Post(
 *     path="/member/tpoint",
 *     description="期間固定Tポイント情報取得",
 *     produces={"application/json"},
 *     tags={"Member"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="body",
 *       in="body",
 *       description="",
 *       type="array",
 *       @SWG\Schema(
 *         @SWG\Property(
 *             property="systemId",
 *             type="string",
 *             example="TAP",
 *             description="システムID（NTにて利用する為に、システムIDを指定）",
 *         ),
 *         @SWG\Property(
 *             property="tolId",
 *             type="integer",
 *             example="aNVWg%2BgjAUmvOb31UptcjPGEpF%2BOYv7wkTIdfk0qJlc%3D",
 *             description="アカウントID",
 *         ),
 *         @SWG\Property(
 *             property="refreshFlg",
 *             type="boolean",
 *             example=false,
 *             description="リフレッシュフラグ（true:リフレッシュ、falseまたは未指定：キャッシュ参照）",
 *         )
 *       )
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="success",
 *         @SWG\Schema(
 *             @SWG\Property(
 *                 property="responseCode",
 *                 type="string",
 *                 example="00",
 *                 description="TOL-APIが呼んでいるAPIのレスポンス"
 *              ),
 *             @SWG\Property(
 *                 property="membershipType",
 *                 type="string",
 *                 example="1",
 *                 description="会員種別（1:レンタル会員, 2:物販会員）"
 *              ),
 *             @SWG\Property(
 *                 property="point",
 *                 type="integer",
 *                 example="9999999999",
 *                 description="ポイント"
 *             ),
 *             @SWG\Property(
 *                 property="fixedPointTotal",
 *                 type="integer",
 *                 example="9999999999",
 *                 description="期間固定情報：期間固定ポイント数合計"
 *             ),
 *             @SWG\Property(
 *                 property="fixedPointMinLimitTime",
 *                 type="string",
 *                 example="2018-01-01 00:00:00",
 *                 description="期間固定情報：期間固定ポイント最短有効期限"
 *             )
 *         )
 *     ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
*/

/**
 * @SWG\Post(
 *     path="/member/status/rental",
 *     description="レンタル利用登録(モバT画面出し分け用)",
 *     produces={"application/json"},
 *     tags={"Member"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="body",
 *       in="body",
 *       description="",
 *       type="array",
 *       @SWG\Schema(
 *         @SWG\Property(
 *             property="tolId",
 *             type="integer",
 *             example="1234567890",
 *             description="アカウントID",
 *         )
 *       )
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="itemNumber",
 *                  type="integer",
 *                  description="処理項番 項番1~17の内の一つを返す",
 *              ),
 *              @SWG\Property(
 *                  property="rentalExpirationDate",
 *                  type="string",
 *                  description="有効期限 (yyyy-mm-dd 00:00:00) / ユーザーごとのレンタル利用登録の有効期限を返す",
 *              )
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=400, description="Bad Request"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */

/**
 * @SWG\Post(
 *     path="/member/status/premium",
 *     description="",
 *     produces={"application/json"},
 *     tags={"Member"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="body",
 *       in="body",
 *       description="",
 *       type="array",
 *       @SWG\Schema(
 *         @SWG\Property(
 *             property="tolId",
 *             type="integer",
 *             example="1234567890",
 *             description="アカウントID",
 *         )
 *       )
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="premium",
 *                  type="boolean",
 *                  description="プレミアム会員かどうか（true=プレミアム会員、false=非プレミアム会員）",
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=400, description="Bad Request"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */

/**
 * @SWG\Post(
 *     path="/member/status/ttv",
 *     description="",
 *     produces={"application/json"},
 *     tags={"Member"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="body",
 *       in="body",
 *       description="",
 *       type="array",
 *       @SWG\Schema(
 *         @SWG\Property(
 *             property="tlsc",
 *             type="string",
 *             description="ユーザー識別番号(TLSC)",
 *         )
 *       )
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="ttvId",
 *                  type="string",
 *                  description="TsutayaTV利用者ID",
 *              ),
 *              @SWG\Property(
 *                  property="tenpoCode",
 *                  type="string",
 *                  description="店舗コード",
 *              ),
 *              @SWG\Property(
 *                  property="tenpoName",
 *                  type="string",
 *                  description="店舗名",
 *              ),
 *              @SWG\Property(
 *                  property="tenpoPlanFee",
 *                  type="integer",
 *                  description="店プラン金額（税込）",
 *              ),
 *              @SWG\Property(
 *                  property="nextUpdateDate",
 *                  type="string",
 *                  description="次回契約更新日",
 *              )
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(
 *          response=400,
 *          description="",
 *          @SWG\Property(
 *              property="rows",
 *              type="object",
 *              @SWG\Property(property="httpcode",type="string",description="htttpステータスコード"),
 *              @SWG\Property(property="status",type="string",description="接続先APIのエラーステータスコード"),
 *              description="ttv apiのバリデーションエラー",
 *          ),
 *     ),
 *     @SWG\Response(
 *          response=401,
 *          description="",
 *          @SWG\Property(
 *              property="rows",
 *              type="object",
 *              @SWG\Property(property="httpcode",type="string",description="htttpステータスコード"),
 *              @SWG\Property(property="status",type="string",description="接続先APIのエラーステータスコード"),
 *              description="エラー情報",
 *          ),
 *     ),
 *     @SWG\Response(
 *          response=403,
 *          description="",
 *          @SWG\Property(
 *              property="rows",
 *              type="object",
 *              @SWG\Property(property="httpcode",type="string",description="htttpステータスコード"),
 *              @SWG\Property(property="status",type="string",description="接続先APIのエラーステータスコード"),
 *              description="認証失敗（TTV側と本システム）",
 *          ),
 *     ),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */

/**
 * @SWG\Post(
 *     path="/member/status/arrival/notification",
 *     description="",
 *     produces={"application/json"},
 *     tags={"Member"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="body",
 *       in="body",
 *       description="",
 *       type="array",
 *       @SWG\Schema(
 *         @SWG\Property(
 *             property="tolId",
 *             type="string",
 *             description="",
 *         ),
 *       ),
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="results",
 *                  type="object",
 *                  @SWG\Property(property="status",type="string",description="SUCCESS|ERROR"),
 *                  @SWG\Property(property="isRegistered",type="boolean",description="true|false"),
 *              )
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=400, description="Bad Request"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */

/**
 * @SWG\Post(
 *     path="/member/status/arrival/notification/update",
 *     description="",
 *     produces={"application/json"},
 *     tags={"Member"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="body",
 *       in="body",
 *       description="",
 *       type="array",
 *       @SWG\Schema(
 *         @SWG\Property(
 *             property="tolId",
 *             type="string",
 *             description="",
 *         ),
 *         @SWG\Property(
 *             property="isRegistered",
 *             type="boolean",
 *             description=""
 *         )
 *       )
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="results",
 *                  type="object",
 *                  @SWG\Property(property="status",type="string",description="SUCCESS"),
 *                  @SWG\Property(property="isRegistered",type="boolean",description="true|false"),
 *              )
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(
 *          response=400,
 *          description="",
 *          @SWG\Property(
 *              property="rows",
 *              type="object",
 *              @SWG\Property(property="httpcode",type="string",description="htttpステータスコード"),
 *              @SWG\Property(property="status",type="string",description="接続先APIのエラーステータスコード"),
 *              description="ttv apiのバリデーションエラー",
 *          ),
 *     ),
 *     @SWG\Response(
 *          response=401,
 *          description="",
 *          @SWG\Property(
 *              property="rows",
 *              type="object",
 *              @SWG\Property(property="httpcode",type="string",description="htttpステータスコード"),
 *              @SWG\Property(property="status",type="string",description="接続先APIのエラーステータスコード"),
 *              description="エラー情報",
 *          ),
 *     ),
 *     @SWG\Response(
 *          response=403,
 *          description="",
 *          @SWG\Property(
 *              property="rows",
 *              type="object",
 *              @SWG\Property(property="httpcode",type="string",description="htttpステータスコード"),
 *              @SWG\Property(property="status",type="string",description="接続先APIのエラーステータスコード"),
 *              description="認証失敗（TTV側と本システム）",
 *          ),
 *     ),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */

/**
 * @SWG\Post(
 *     path="/member/hasDisc/premium/rental",
 *     description="",
 *     produces={"application/json"},
 *     tags={"Member"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="body",
 *       in="body",
 *       description="",
 *       type="array",
 *       @SWG\Schema(
 *         @SWG\Property(
 *             property="tlsc",
 *             type="string",
 *             description="ユーザー識別番号(TLSC)",
 *         )
 *       )
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="rows",
 *                  type="array",
 *                  @SWG\Items(
 *                      @SWG\Property(
 *                          property="storeName",
 *                          type="string",
 *                          description="店舗名"
 *                      ),
 *                      @SWG\Property(
 *                          property="rentCnt",
 *                          type="integer",
 *                          description="レンタル枚数"
 *                      ),
 *                  ),
 *                  description="会員情報の拡充"
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(
 *          response=400,
 *          description="",
 *          @SWG\Property(
 *              property="rows",
 *              type="object",
 *              @SWG\Property(property="httpcode",type="string",description="htttpステータスコード"),
 *              @SWG\Property(property="status",type="string",description="接続先APIのエラーステータスコード"),
 *              description="ttv apiのバリデーションエラー",
 *          ),
 *     ),
 *     @SWG\Response(
 *          response=401,
 *          description="",
 *          @SWG\Property(
 *              property="rows",
 *              type="object",
 *              @SWG\Property(property="httpcode",type="string",description="htttpステータスコード"),
 *              @SWG\Property(property="status",type="string",description="接続先APIのエラーステータスコード"),
 *              description="エラー情報",
 *          ),
 *     ),
 *     @SWG\Response(
 *          response=403,
 *          description="",
 *          @SWG\Property(
 *              property="rows",
 *              type="object",
 *              @SWG\Property(property="httpcode",type="string",description="htttpステータスコード"),
 *              @SWG\Property(property="status",type="string",description="接続先APIのエラーステータスコード"),
 *              description="認証失敗（TTV側と本システム）",
 *          ),
 *     ),
 *     @SWG\Response(response=500, description="Server error"),
 *     @SWG\Response(response=503, description="Service temporarily unavailable")
 * )
 */
/**
 * @SWG\Post(
 *     path="/member/premium/authKey",
 *     description="認証キー",
 *     produces={"application/json"},
 *     tags={"Member"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="body",
 *       in="body",
 *       description="",
 *       type="array",
 *       @SWG\Schema(
 *         @SWG\Property(
 *             property="tolId",
 *             type="string",
 *             description="ユーザー識別番号(TLSC)",
 *         )
 *       )
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="authKey",
 *                  type="string",
 *                  description="WEB認証キー",
 *              )
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=400, description="Bad Request"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */

/**
 * @SWG\Get(
 *     path="/promotion/{promotion_id}",
 *     description="キャンペーン情報",
 *     tags={"Promotion"},
 *     produces={"application/json"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *          name="promotion_id",
 *          description="キャンペーンID",
 *          in="path",
 *          required=true,
 *          type="string"
 *     ),
 *     @SWG\Parameter(ref="#/parameters/limit"),
 *     @SWG\Parameter(ref="#/parameters/offset"),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="data",
 *                  type="object",
 *                  ref="#/definitions/Promotion",
 *                  description="キャンペーン情報",
 *              ),
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */

/**
 * @SWG\Post(
 *     path="/promotion/entry",
 *     description="キャンペーン応募",
 *     produces={"application/json"},
 *     tags={"Promotion"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="body",
 *       in="body",
 *       description="",
 *       type="array",
 *       @SWG\Schema(
 *         @SWG\Property(
 *             property="tolId",
 *             type="string",
 *             description="アカウントID",
 *         ),
 *         @SWG\Property(
 *             property="promotionId",
 *             type="string",
 *             description="キャンペーンID"
 *         ),
 *         @SWG\Property(
 *             property="prizeNo",
 *             type="string",
 *             description="賞品情報"
 *         ),
 *         @SWG\Property(
 *             property="ques",
 *             type="array",
 *             description="アンケート設問",
 *             @SWG\Items(
 *                 @SWG\Property(
 *                     property="no",
 *                     type="string",
 *                     example="1"
 *                 ),
 *                 @SWG\Property(
 *                     property="ans",
 *                     type="string",
 *                     example="1,2,6"
 *                 )
 *             )
 *         ),
 *       )
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="result",
 *                  type="boolean",
 *                  example="true",
 *              )
 *          )
 *      ),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */

/**
 * @SWG\Post(
 *     path="/promotion/entry/check",
 *     description="キャンペーン多重応募チェック",
 *     produces={"application/json"},
 *     tags={"Promotion"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="body",
 *       in="body",
 *       description="",
 *       type="array",
 *       @SWG\Schema(
 *         @SWG\Property(
 *             property="tolId",
 *             type="string",
 *             description="アカウントID",
 *         ),
 *         @SWG\Property(
 *             property="promotionId",
 *             type="string",
 *             description="キャンペーンID"
 *         )
 *       )
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="count",
 *                  type="string",
 *                  example="1",
 *              )
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */

/**
 * @SWG\Get(
 *     path="/system/maintenance",
 *     description="メンテナンス情報を返します",
 *     produces={"application/json"},
 *     tags={"System"},
 *     security={{"api_key":{}}},
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="title",
 *                  type="string",
 *                  example="",
 *              ),
 *              @SWG\Property(
 *                  property="text",
 *                  type="string",
 *                  example="",
 *              ),
 *              @SWG\Property(
 *                  property="endDate",
 *                  type="string",
 *                  example="",
 *              ),
 *              @SWG\Property(
 *                  property="caution",
 *                  type="array",
 *                  @SWG\Items(
 *                       @SWG\Property(property="title",type="string"),
 *                       @SWG\Property(property="text",type="string")
 *                  )
 *              ),
 *              @SWG\Property(
 *                  property="button",
 *                  type="array",
 *                  @SWG\Items(
 *                       @SWG\Property(property="text",type="string"),
 *                       @SWG\Property(property="link",type="string")
 *                  )
 *              )
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */

/**
 * @SWG\Post(
 *     path="/member/status/notification",
 *     description="プッシュ通知パーミッション取得",
 *     produces={"application/json"},
 *     tags={"Member"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="body",
 *       in="body",
 *       description="",
 *       type="array",
 *       @SWG\Schema(
 *         @SWG\Property(
 *             property="tolId",
 *             type="string",
 *             description="アカウントID",
 *         )
 *       )
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="results",
 *                  type="object",
 *             		@SWG\Property(
 *                  	property="status",
 *                  	type="string",
 *                  	description="ステータス",
 *              	),
 *              	@SWG\Property(
 *                  	property="data",
 *                  	type="array",
 *                  	@SWG\Items(ref="#/definitions/PushNotification"),
 *                  	description="データ",
 *              	),
 *              )
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */

/**
 * @SWG\Post(
 *     path="/member/status/notification/update",
 *     description="プッシュ通知パーミッション登録・取得",
 *     produces={"application/json"},
 *     tags={"Member"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="body",
 *       in="body",
 *       description="",
 *       type="array",
 *       @SWG\Schema(
 *         @SWG\Property(
 *             property="tolId",
 *             type="string",
 *             description="アカウントID",
 *         ),
 *         @SWG\Property(
 *             property="data",
 *             type="array",
 *             description="データ",
 *             @SWG\Items(
 *                 @SWG\Property(
 *                     property="applicationKind",
 *                     type="string",
 *                     example="600"
 *                 ),
 *                 @SWG\Property(
 *                     property="status",
 *                     type="boolean",
 *                     example="true"
 *                 )
 *             )
 *         ),
 *       )
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="success",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="results",
 *                  type="object",
 *              	@SWG\Property(
 *                  	property="status",
 *                  	type="string",
 *                  	description="ステータス",
 *              	),
 *              	@SWG\Property(
 *                  	property="errCd",
 *                  	type="string",
 *                  	description="エラーコード",
 *              	)
 *              )
 *          )
 *      ),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
