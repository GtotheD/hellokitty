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
use App\Repositories\SectionRepository;

// Api Group
$router->group(['prefix' => env('API_VERSION')], function() use ($router) {

    // 固定コンテンツ取得API
    $router->get('version', function () {
        $version = config('version');

        return response()->json($version);
    });

    // 固定コンテンツ取得API
    $router->get('fixed/banner', function () {
        return 'this is fixed banner';
    });

    // コンテンツ構成取得API
    $router->get('structure/{goodsName::dvd|book|cd|game}/{typeName:rental|sell}', function ($goodsName, $typeName) {
        $structureData = $structure->get($goodsName, $typeName);
        return response()->json($structureData);
    });


    // ランキングセクション取得API
    $router->get('section/{goodsName::dvd|book|cd|game}/{typeName:rental|sell}/ranking', function ($goodsName, $typeName) {
        $sectionRepository = new SectionRepository;
        $sectionData = $sectionRepository->normal($goodsName, $typeName, 'ranking');
        return response()->json($sectionData);
    });

    // 通常セクション取得API
    $router->get('section/{goodsName:dvd|book|cd|game}/{typeName:rental|sell}/{sectionName}', function ($goodsName, $typeName, $sectionName) {
        $sectionRepository = new SectionRepository;
        $sectionData = $sectionRepository->normal($goodsName, $typeName, $sectionName);
        return $sectionData;

    });

    // バナーセクション取得API
    $router->get('section/banner/{sectionName}', function ($sectionName) {
        $sectionRepository = new SectionRepository;
        $sectionData = $sectionRepository->banner($goodsName, $typeName, $sectionName);
        return $sectionData;

    });

    // レコメンドセクション取得API
    $router->get('section/recommend/ranking/{himoGenreId}', function ($himoGenreId) {
        $sectionRepository = new SectionRepository;
        $sectionData = $sectionRepository->ranking($himoGenreId);
        return $sectionData;
    });
});
