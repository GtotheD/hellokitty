<?php
// アプリバージョン情報及びメンテナンス
/**
 * @SWG\Get(
 *     path="/version",
 *     description="バージョンを取得する",
 *     produces={"application/json"},
 *     tags={"Api-Version"},
 *     @SWG\Response(response=200, description="Success"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */
/**
 * @SWG\Get(
 *     path="/structure/dvd/rental",
 *     description="DVD-レンタルのTOP構造を返却する",
 *     produces={"application/json"},
 *     tags={"structure"},
 *     security={{"api_key":{}}},
 *     @SWG\Response(response=200, description="Success"),
 *     @SWG\Response(response=204, description="Contents not found"),
 *     @SWG\Response(response=401, description="Auth error"),
 *     @SWG\Response(response=404, description="Parameter error"),
 *     @SWG\Response(response=500, description="Server error")
 * )
 */