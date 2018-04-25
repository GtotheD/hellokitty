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
use App\Repositories\WorkRepository;
use App\Repositories\ProductRepository;
use App\Repositories\DiscasRepository;
use App\Repositories\TAPRepository;
use App\Repositories\PeopleRepository;
use App\Repositories\SeriesRepository;
use App\Repositories\TWSRepository;
use App\Repositories\HimoKeywordRepository;
use App\Repositories\PeopleRelatedWorksRepository;
use App\Repositories\RelateadWorkRepository;
use App\Repositories\RecommendOtherRepository;

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
        if ($goodsType === 'dvd') {
            $sectionRepository->setSupplementVisible(true);
        }

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
    $router->get('section/ranking/{codeType:himo|agg}/{code}[/{period}]', function (Request $request, $codeType, $code, $period = null) {
        $sectionRepository = new SectionRepository;
        $sectionRepository->setLimit(20);
        $sectionRepository->setSupplementVisible($request->input('supplementVisibleFlg', false));
        $sectionData = $sectionRepository->ranking($codeType, $code, $period);
        return $sectionData;
    });

    // レコメンドセクション取得API
    $router->get('section/release/manual/{tapCategoryId}[/{releaseDateTo}]', function (Request $request, $tapCategoryId, $releaseDateTo = null) {
        if (empty($releaseDateTo)) {
            $releaseDateTo = date('Ymd', strtotime('next sunday'));
        }
        $sectionRepository = new SectionRepository;
        $sectionRepository->setSupplementVisible($request->input('supplementVisibleFlg', false));
        $sectionData = $sectionRepository->releaseManual($tapCategoryId, $releaseDateTo);
        return $sectionData;
    });

    // レコメンドセクション取得API
    $router->get('section/release/auto/{genreId}/{storeProductItemCd}', function (Request $request, $genreId, $storeProductItemCd) {
        $sectionRepository = new SectionRepository;
        $sectionRepository->setSupplementVisible($request->input('supplementVisibleFlg', false));
        $sectionData = $sectionRepository->releaseAuto($genreId, $storeProductItemCd);
        return $sectionData;
    });

    // 作品基本情報
    $router->get('work/{workId}', function (Request $request, $workId) {
        $work = new WorkRepository();
        $work->setSaleType($request->input('saleType'));
        $ageLimitCheck = $request->input('ageLimitCheck', false);
        $result = $work->get($workId);
        $checkAgeLimit = $work->checkAgeLimit($result['ratingId'], $result['bigGenreId']);
        if ($ageLimitCheck === 'false' && ($checkAgeLimit === true || $result['adultFlg'] === '1')) {
            $response = [
                'status' => 'error',
                'message' => 'Age limit auth error'
            ];
        } else {
            $response = [
                'data' => $result
            ];
        }
        return response()->json($response);
    });
    // 商品一覧情報取得
    $router->get('work/{workId}/products', function (Request $request, $workId) {
        $product = new ProductRepository();
        $product->setLimit($request->input('limit', 10));
        $product->setOffset($request->input('offset', 0));
        $product->setSaleType($request->input('saleType'));
        $result = $product->getNarrow($workId);
        if (empty($result)) {
            throw new NoContentsException;
        }

        $response = [
            'hasNext' => $product->getHasNext(),
            'totalCount' => $product->getTotalCount(),
            'rows' => $result
        ];
        return response()->json($response);
    });
    // 商品一覧情報取得（DVDレンタル時のグルーピング（問い合わせ時のLimit数がおかしくなる為にグルーピングが必要））
    $router->get('work/{workId}/products/rental', function (Request $request, $workId) {
        $product = new ProductRepository();
        $product->setLimit($request->input('limit', 10));
        $product->setOffset($request->input('offset', 0));
        $result = $product->getRentalGroup($workId);
        if (empty($result)) {
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => $product->getHasNext(),
            'totalCount' => $product->getTotalCount(),
            'rows' => $result
        ];
        return response()->json($response);
    });
    // Himo作品ID作品検索
    $router->get('work/{workId}/products/has', function (Request $request, $workId) {
        $work = new WorkRepository();
        $work->setSaleType($request->input('saleType', 'rental'));
        $ageLimitCheck = $request->input('ageLimitCheck', false);
        $result = $work->getNarrowColumns($workId);
        $checkAgeLimit = $work->checkAgeLimit($result['ratingId'], $result['bigGenreId']);
        if ($ageLimitCheck === 'false' && ($checkAgeLimit === true || $result['adultFlg'] === '1')) {
            $response = [
                'status' => 'error',
                'message' => 'Age limit auth error'
            ];
        } else {
            $response = [
                'data' => $result
            ];
        }
        return response()->json($response);
    });
    // キャストスタッフ一覧取得
    $router->get('work/{workId}/people', function (Request $request, $workId) {
        $people = new PeopleRepository();
        $people->setLimit($request->input('limit', 10));
        $people->setOffset($request->input('offset', 0));
        $saleType = $request->input('saleType');
        $response = $people->getNarrow($workId, $saleType);
        return response()->json($response);
    });

    // 作品シリーズ情報
    $router->get('work/{workId}/series', function (Request $request, $workId) {
        $series = new SeriesRepository();
        $series->setLimit($request->input('limit', 10));
        $series->setOffset($request->input('offset', 0));
        $saleType = $request->input('saleType');
        $response = $series->getNarrow($workId, $saleType);
        return response()->json($response);
    });
    // レビュー情報 filmarks
    $router->get('work/{workId}/review/filmarks', function (Request $request, $workId) {

        $work = new WorkRepository();
        $tapRepository = new TAPRepository();
        $workData = $work->get($workId);

        $tapRepository->setLimit($request->input('limit', 10));
        $response = $tapRepository->getReview($workData['filmarksId']);

        if (empty($response)) {
            throw new NoContentsException;
        }
        
        return response()->json($response);
    });
    // レビュー情報 discas
    $router->get('work/{workId}/review/discas', function (Request $request, $workId) {
        $work = new WorkRepository();
        $discasRepository = new DiscasRepository();

        $workData = $work->get($workId);

        $discasRepository->setLimit($request->input('limit', 10));
        $discasRepository->setOffset($request->input('offset', 0));
        $response = $discasRepository->getReview($workData['cccWorkCd']);

        if (empty($response)) {
            throw new NoContentsException;
        }

        return response()->json($response);
    });
    // レビュー情報 tol
    $router->get('work/{workId}/review/tol', function (Request $request, $workId) {
        $work = new WorkRepository();
        $workData = $work->get($workId);

        $twsRepository = new TWSRepository();
        $twsRepository->setLimit($request->input('limit', 10));
        $twsRepository->setOffset($request->input('offset', 0));

        $response = $twsRepository->getReview($workData['urlCd']);
        if (empty($response)) {
            throw new NoContentsException;
        }

        return response()->json($response);
    });
    // 関連作品
    $router->get('work/{workId}/relation/works', function (Request $request, $workId) {
        $relateadWorkRepository = new RelateadWorkRepository;
        $results = $relateadWorkRepository->getNarrow($workId);
        if (empty($results)) {
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => $relateadWorkRepository->getHasNext(),
            'totalCount' => $relateadWorkRepository->getTotalCount(),
            'rows' => $results
        ];
        return response()->json($response);
    });

    // 関連画像
    $router->get('work/{workId}/relation/pics', function (Request $request, $workId) {
        $work = new WorkRepository();
        $workData = $work->get($workId);

        $relationPics = json_decode($workData['sceneL']);


        if(empty($relationPics)){
            throw new NoContentsException;
        }

        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);
        $total = count($relationPics);
        $rows = array_slice($relationPics, $offset, $limit);
        if (empty($rows)) {
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => ($offset + $limit < $total),
            'totalCount' => $total,
            'rows' => $rows
        ];
        return response()->json($response);
    });
    // 関連アーティスト
    $router->get('work/{workId}/relation/artist', function (Request $request, $workId) {
        $recommendArtistRepository = new \App\Repositories\RecommendArtistRepository();
        $recommendArtistRepository->setLimit($request->input('limit', 10));
        $recommendArtistRepository->setOffset($request->input('offset', 0));
        $response = $recommendArtistRepository->getArtist($workId);
        return response()->json($response);
    });


    // キャスト情報
    $router->get('cast/{castId}', function (Request $request, $workId) {
        return $json;
    });
    // 作品レコメンド（この作品を見た人はこんな作品もみています）
    $router->get('work/{workId}/recommend/other', function (Request $request, $workId) {
        $recommendOtherRepository = new RecommendOtherRepository;
        $recommendOtherRepository->setLimit($request->input('limit', 10));
        $recommendOtherRepository->setOffset($request->input('offset', 0));
        $rows = $recommendOtherRepository->getWorks($workId, $request->input('saleType'));
        $response = [
            'hasNext' => $recommendOtherRepository->getHasNext(),
            'totalCount' => $recommendOtherRepository->getTotalCount(),
            'rows' => $rows
        ];
        return response()->json($response);
    });
    // 作者レコメンド
    $router->get('work/{workId}/recommend/author', function (Request $request, $workId) {
        $peopleRelatedWorksRepository = new PeopleRelatedWorksRepository();
        $peopleRelatedWorksRepository->setLimit($request->input('limit', 10));
        $peopleRelatedWorksRepository->setOffset($request->input('offset', 0));
        $rows = $peopleRelatedWorksRepository->getWorks($workId);
        $response = [
            'hasNext' => $peopleRelatedWorksRepository->getHasNext(),
            'totalCount' => $peopleRelatedWorksRepository->getTotalCount(),
            'rows' => $rows
        ];
        return response()->json($response);
    });
    // 作品レコメンド
    $router->get('work/{workId}/recommend/artist', function (Request $request, $workId) {
        $peopleRelatedWorksRepository = new PeopleRelatedWorksRepository();
        $peopleRelatedWorksRepository->setOffset($request->input('offset', 0));
        $peopleRelatedWorksRepository->setLimit($request->input('limit', 10));
        $rows = $peopleRelatedWorksRepository->getWorksByArtist($workId);

        $response = [
            'hasNext' => $peopleRelatedWorksRepository->getHasNext(),
            'totalCount' => $peopleRelatedWorksRepository->getTotalCount(),
            'rows' => $rows
        ];
        return response()->json($response);
    });
    // 変換
    $router->get('convert/work/{idType}/{id}', function (Request $request, $idType, $id) {
        $workRepository = new WorkRepository();
        $json = $workRepository->convert($idType, $id);
        return response()->json($json);
    });

    // 変換
    $router->get('product/{productUniqueId}', function (Request $request, $productUniqueId) {
        $productRepository = new ProductRepository();
        $result = $productRepository->get($productUniqueId);
        $response = [
            'data' => $result
        ];
        return response()->json($response);
    });

    // 店舗在庫
    $router->get('product/stock/{storeCd}/{productKey}', function (Request $request, $storeCd, $productKey) {
        $productRepository = new ProductRepository();
        $response = $productRepository->stock($storeCd, $productKey);
        return response()->json($response);
    });

    //人物関連作品取得
    $router->get('people/{personId}', function (Request $request, $personId) {
        $work = new WorkRepository();
        $work->setLimit($request->input('limit', 10));
        $work->setOffset($request->input('offset', 0));
        $work->setSaleType($request->input('saleType', null));

        $sort = $request->input('sort', '');
        $itemType = $request->input('itemType', 'all');

        $response = $work->person($personId, $sort, $itemType);
        return response()->json($response);
    });

    // キーワード検索
    $router->get('search/{keyword}', function (Request $request, $keyword) {
        $work = new WorkRepository();
        $work->setLimit($request->input('limit', 10));
        $work->setOffset($request->input('offset', 0));

        $sort = $request->input('sort', '');
        $itemType = $request->input('itemType', 'all');
        $periodType = $request->input('periodType', 'all');
        $adultFlg = $request->input('adultFlg', 'false');
        $response = $work->searchKeyword($keyword, $sort, $itemType, $periodType, $adultFlg);
        if(empty($response)){
            throw new NoContentsException;
        }
        return response()->json($response);
    });

    // キーワードサジェスト
    $router->get('search/suggest/{keyword}', function (Request $request, $keyword) {
        $himoKeywordRepository = new HimoKeywordRepository();
        $himoKeywordRepository->setLimit($request->input('limit', 10));
        $himoKeywordRepository->setOffset($request->input('offset', 0));
        $keyword = urldecode($keyword);
        $keywords =  $himoKeywordRepository->get($keyword);
        if(empty($keywords)){
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => $himoKeywordRepository->getHasNext(),
            'totalCount' => $himoKeywordRepository->getTotalCount(),
            'rows' => $keywords
        ];
        return response()->json($response);
    });

    // ジャンルからの作品一覧取得
    $router->get('genre/{genreId}', function (Request $request, $genreId) {
        $work = new WorkRepository();
        $work->setLimit($request->input('limit', 10));
        $work->setOffset($request->input('offset', 0));

        $sort = $request->input('sort', '');
        $saleType = $request->input('saleType', '');
        $response = $work->genre($genreId, $sort, $saleType);
        if (empty($response)) {
            throw new NoContentsException;
        }
        return response()->json($response);
    });
});
$router->group(['prefix' => env('URL_PATH_PREFIX') . env('API_VERSION')], function () use ($router) {
    // APIドキュメント
    $router->get('docs/swagger.json', function () {
        $swagger = \Swagger\scan(base_path('routes'));
        return response()->json($swagger);
    });
});
