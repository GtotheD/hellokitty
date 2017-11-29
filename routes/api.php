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
use App\Exceptions\NoContentsException;
use App\Repositories\BannerRepository;

// Api Group
$router->group([
    'prefix' => env('URL_PATH_PREFIX') . env('API_VERSION'),
    'middleware' => ['auth']
], function () use ($router) {

    // バージョン取得API
    $router->get('version', function () {
        $version = config('version');
        $version['version'] = hash('sha256', serialize($version));
        return response()->json($version);
    });

    // コンテンツ構成取得API
    $router->get('structure/{goodsType:dvd|book|cd|game}/{saleType:rental|sell}', function (Request $request, $goodsType, $saleType) {
        $structureRepository = new StructureRepository;
        $structureRepository->setLimit($request->input('limit', 10));
        $structureRepository->setOffset($request->input('offset', 0));
        $structures = $structureRepository->get($goodsType, $saleType);
        if ($structures->getTotalCount() == 0) {
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => $structures->getHasNext(),
            'totalCount' => $structures->getTotalCount(),
            'rows' => $structures->getRows(),
        ];
        return response()->json($response);
    });

    // 固定コンテンツ取得API
    $router->get('fixed/banner', function (Request $request) {
        $bannerRepository = new BannerRepository;
        $bannerRepository->setLoginType($request->input('isLoggedIn', false));
        $banner = $bannerRepository->banner('static', true);
        if ($banner->getTotalCount() == 0) {
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => $banner->getHasNext(),
            'totalCount' => $banner->getTotalCount(),
            'width' => $banner->getWidth(),
            'height' => $banner->getHeight(),
            'rows' => $banner->getRows()
        ];
        return response()->json($response);
    });

    // 通常セクション取得API
    $router->get('section/{goodsType:dvd|book|cd|game}/{saleType:rental|sell}/{sectionName}', function (Request $request, $goodsType, $saleType, $sectionName) {
        $sectionRepository = new SectionRepository;
        $sectionRepository->setLimit($request->input('limit', 10));
        $sectionRepository->setOffset($request->input('offset', 0));
        $section = $sectionRepository->normal($goodsType, $saleType, $sectionName);
        if ($section->getTotalCount() == 0) {
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => $section->getHasNext(),
            'totalCount' => $section->getTotalCount(),
            'rows' => $section->getRows()
        ];
        return response()->json($response);
    });

    // バナーセクション取得API
    $router->get('section/banner/{sectionName}', function (Request $request, $sectionName) {
        $bannerRepository = new BannerRepository;
        $bannerRepository->setLoginType($request->input('isLoggedIn', false));
        $banner = $bannerRepository->banner($sectionName);
        if ($banner->getTotalCount() == 0) {
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => $banner->getHasNext(),
            'totalCount' => $banner->getTotalCount(),
            'width' => $banner->getWidth(),
            'height' => $banner->getHeight(),
            'rows' => $banner->getRows()
        ];
        return response()->json($response);
    });

    // レコメンドセクション取得API
    $router->get('section/ranking/{codeType:himo|agg}/{code}[/{period}]', function ($codeType, $code, $period = null) {
        $sectionRepository = new SectionRepository;
        $sectionData = $sectionRepository->ranking($codeType, $code, $period);
        return $sectionData;
    });

    // レコメンドセクション取得API
    $router->get('section/release/manual/{tapCategoryId}[/{releaseDateTo}]', function ($tapCategoryId, $releaseDateTo = null) {
        if (empty($releaseDateTo)) {
            $releaseDateTo = date('Ymd',strtotime('next sunday'));
        }
        $sectionRepository = new SectionRepository;
        $sectionData = $sectionRepository->releaseManual($tapCategoryId, $releaseDateTo);
        return $sectionData;
    });

    // レコメンドセクション取得API
    $router->get('section/release/auto/{genreId}/{storeProductItemCd}', function ($genreId, $storeProductItemCd) {
        $sectionRepository = new SectionRepository;
        $sectionData = $sectionRepository->releaseAuto($genreId, $storeProductItemCd);
        return $sectionData;
    });

});
$router->group(['prefix' => env('URL_PATH_PREFIX') . env('API_VERSION')], function () use ($router) {
    // APIドキュメント
    $router->get('docs/swagger.json', function () {
        $swagger = \Swagger\scan(base_path('routes'));
        return response()->json($swagger);
    });
});
