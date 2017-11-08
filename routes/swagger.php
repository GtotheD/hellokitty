<?php
/**
 * @SWG\Get(
 *     path="/version",
 *     description="バージョンを管理する為の情報を取得する",
 *     produces={"application/json"},
 *     tags={"Version"},
 *     security={{"api_key":{}}},
 *     @SWG\Response(response=200, description="Success"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/structure/dvd/rental",
 *     description="DVD-レンタルのTOP構造を返却する",
 *     produces={"application/json"},
 *     tags={"Structure"},
 *     security={{"api_key":{}}},
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
 *     tags={"Structure"},
 *     security={{"api_key":{}}},
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
 *     tags={"Structure"},
 *     security={{"api_key":{}}},
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
 *     tags={"Structure"},
 *     security={{"api_key":{}}},
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
 *     tags={"Structure"},
 *     security={{"api_key":{}}},
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
 *     tags={"Structure"},
 *     security={{"api_key":{}}},
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
 *     tags={"Structure"},
 *     security={{"api_key":{}}},
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
 *     tags={"Fixed"},
 *     security={{"api_key":{}}},
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
 *     tags={"Section"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="sectionName",
 *       in="path",
 *       description="セクション名",
 *       required=true,
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
 *     path="/section/dvd/rental/ranking",
 *     description="DVD-レンタルのランキング情報を返却する",
 *     produces={"application/json"},
 *     tags={"Section"},
 *     security={{"api_key":{}}},
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
 *     tags={"Section"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="sectionName",
 *       in="path",
 *       description="セクション名",
 *       required=true,
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
 *     path="/section/dvd/sell/ranking",
 *     description="DVD-セルのランキングを返却する",
 *     produces={"application/json"},
 *     tags={"Section"},
 *     security={{"api_key":{}}},
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
 *     tags={"Section"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="sectionName",
 *       in="path",
 *       description="セクション名",
 *       required=true,
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
 *     path="/section/book/rental/ranking",
 *     description="DVD-セルのランキング情報を返却する",
 *     produces={"application/json"},
 *     tags={"Section"},
 *     security={{"api_key":{}}},
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
 *     tags={"Section"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="sectionName",
 *       in="path",
 *       description="セクション名",
 *       required=true,
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
 *     path="/section/book/sell/ranking",
 *     description="DVD-セルのランキング情報を返却する",
 *     produces={"application/json"},
 *     tags={"Section"},
 *     security={{"api_key":{}}},
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
 *     tags={"Section"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="sectionName",
 *       in="path",
 *       description="セクション名",
 *       required=true,
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
 *     path="/section/cd/rental/ranking",
 *     description="DVD-セルのランキング情報を返却する",
 *     produces={"application/json"},
 *     tags={"Section"},
 *     security={{"api_key":{}}},
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
 *     tags={"Section"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="sectionName",
 *       in="path",
 *       description="セクション名",
 *       required=true,
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
 *     path="/section/cd/sell/ranking",
 *     description="DVD-セルのランキング情報を返却する",
 *     produces={"application/json"},
 *     tags={"Section"},
 *     security={{"api_key":{}}},
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
 *     tags={"Section"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="sectionName",
 *       in="path",
 *       description="セクション名",
 *       required=true,
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
 *     path="/section/game/sell/ranking",
 *     description="DVD-セルのTOP構造を返却する",
 *     produces={"application/json"},
 *     tags={"Section"},
 *     security={{"api_key":{}}},
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
 *     tags={"Section"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="sectionName",
 *       in="path",
 *       description="セクション名",
 *       required=true,
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
 *     path="/section/recommend/ranking/{himoGenreId}",
 *     description="DVD-セルのTOP構造を返却する",
 *     produces={"application/json"},
 *     tags={"Section"},
 *     security={{"api_key":{}}},
 *     @SWG\Parameter(
 *       name="himoGenreId",
 *       in="path",
 *       description="HimoジャンルID（例：EXT0000005F2）",
 *       required=true,
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
 *     path="/section/release/manual/{tapCategoryId}/{releaseDateTo}",
 *     description="手動運用のリリースカレンダー情報を返却する（TAP API経由）",
 *     produces={"application/json"},
 *     tags={"Section"},
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
 *       description="リリース開始起算日",
 *       required=true,
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
 *     path="/section/release/auto/{largeGenreCd}/{storeProductItemCd}/{itemCode}",
 *     description="自動取得のリリースカレンダー情報を返却する（TWSのサーチを利用）",
 *     produces={"application/json"},
 *     tags={"Section"},
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
 *       name="itemCode",
 *       in="path",
 *       description="アイテムコード（002）",
 *       required=true,
 *       type="string"
 *     ),
 *     @SWG\Response(response=200, description="Success"),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Page not found"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
