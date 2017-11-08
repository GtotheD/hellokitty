<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
use Illuminate\Http\Request;
use App\Repositories\StructureRepository;
use App\Repositories\SectionRepository;
use Swagger\Annotations\Swagger;

// Api Group
$router->group([
    'prefix' => env('URL_PATH_PREFIX') . env('API_VERSION'),
    'middleware' => ['auth']
    ], function() use ($router) {

    // バージョン取得API
    $router->get('version', function () {
        $version = config('version');
        $version['version'] = hash('sha256', serialize($version));
        return response()->json($version);
    });

    // コンテンツ構成取得API
    $router->get('structure/{goodsType:dvd|book|cd|game}/{saleType:rental|sell}', function ($goodsType, $saleType) {
        $structureRepository = new StructureRepository;
        $structures = $structureRepository->get($goodsType, $saleType);
        $response = [
                'hasNext' => $structures->getHasNext(),
                'totalCount' => $structures->getTotalCount(),
                'limit' => $structures->getLimit(),
                'offset' => $structures->getOffset(),
                'page' => $structures->getPage(),
                'rows' => $structures->getStructure(),
            ];
        return response()->json($response);
    });

    // 固定コンテンツ取得API
    $router->get('fixed/banner', function () {
        $sectionRepository = new SectionRepository;
        $sectionData = $sectionRepository->fixedBanner();
        return response()->json($sectionData);
    });

    // ランキングセクション取得API
    $router->get('section/{goodsType:dvd|book|cd|game}/{saleType:rental|sell}/ranking', function ($goodsType, $saleType) {
        $sectionRepository = new SectionRepository;
        $sectionData = $sectionRepository->normal($goodsType, $saleType, 'ranking');
        return response()->json($sectionData);
    });

    // 通常セクション取得API
    $router->get('section/{goodsType:dvd|book|cd|game}/{saleType:rental|sell}/{sectionName}', function ($goodsType, $saleType, $sectionName) {
        $sectionRepository = new SectionRepository;
        $sectionData = $sectionRepository->normal($goodsType, $saleType, $sectionName);
        return response()->json($sectionData);
    });

    // バナーセクション取得API
    $router->get('section/banner/{sectionName}', function ($sectionName) {
        $sectionRepository = new SectionRepository;
        $sectionData = $sectionRepository->banner($goodsName, $typeName, $sectionName);
        return $sectionData;

    });

    // レコメンドセクション取得API
    $router->get('section/ranking/{codeType:himo|agg}/{code}', function ($codeType, $code, $period = null) {
        $sectionRepository = new SectionRepository;
        $sectionData = $sectionRepository->ranking($codeType, $code, $period);
        return $sectionData;
    });

    // レコメンドセクション取得API（何故か任意パラメーターで追加できないので…）
    $router->get('section/ranking/{codeType:himo|agg}/{code}/{period}', function ($codeType, $code, $period = null) {
        $sectionRepository = new SectionRepository;
        $sectionData = $sectionRepository->ranking($codeType, $code, $period);
        return $sectionData;
    });

    // レコメンドセクション取得API
    $router->get('section/release/manual/{genreId}/{storeProductItemCd}/{itemCode}', function ($genreId, $storeProductItemCd, $itemCode) {
        $sectionRepository = new SectionRepository;
        $sectionData = $sectionRepository->releaseAuto($genreId, $storeProductItemCd, $itemCode);
        return $sectionData;
    });

    // レコメンドセクション取得API
    $router->get('section/release/auto/{genreId}/{storeProductItemCd}/{itemCode}', function ($genreId, $storeProductItemCd, $itemCode) {
        $sectionRepository = new SectionRepository;
        $sectionData = $sectionRepository->releaseAuto($genreId, $storeProductItemCd, $itemCode);
        return $sectionData;
    });

});
$router->group(['prefix' => env('URL_PATH_PREFIX') . env('API_VERSION') ], function() use ($router) {
    // APIドキュメント
    $router->get('docs/swagger.json', function () {
        $swagger = \Swagger\scan(base_path('routes'));
        return response()->json($swagger);
    });
});
