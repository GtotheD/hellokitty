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
            $releaseDateTo = date('Ymd',strtotime('next sunday'));
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
        $result = $work->get($workId);
        $response = [
            'data' => $result
        ];
        return response()->json($response);
    });
    // 作品シリーズ情報
    $router->get('work/{workId}/series', function (Request $request, $workId) {
      $responseString = <<<EOT
      {
        "hasNext": true,
        "totalCount": 1,
        "rows": [
          {
            "workId": "PTA00007XDJP",
            "workTitle": "ピノキオ",
            "workTitleOrig": "PINOCCHIO",
            "saleType": "2",
            "workTypeId": "2",
            "jacketL": "https://cdn.store-tsutaya.tsite.jp/images/jacket/07483/4959241310644_1L.jpg",
            "saleStartDate": "1995-03-17",
            "bigGenreId": "EXT0000000WP",
            "bigGenreName": "キッズ",
            "mediumGenreId": "EXT000003SXI",
            "mediumGenreName": "ディズニー",
            "ratingName": "",
            "docText": "木のあやつり人形・ピノキオが命を与えられ、冒険を通して善悪を学び、本物の少年となっていくまでを描く。１９４０年初公開のディズニー名作アニメをデジタル・リマスターで蘇らせ、ボーナス・コンテンツを加えた３枚組にパッケージ。",
            "createdYear": "1940",
            "createdCountries": "アメリカ"
          }
        ]
      }
EOT;
      $json = json_decode($responseString);
      return response()->json($json);
    });
    // レビュー情報 filmarks
    $router->get('work/{workId}/review/filmarks', function (Request $request, $workId) {
      $responseString = <<<EOT
      {
        "totalCount": 1,
        "averageRating": 0,
        "rows": [
          {
            "rating": "4",
            "contributor": "ホゲホゲ",
            "contributeDate": "2018-03-01",
            "contents": "ふがふが　ほげほげ　ふがふが　ほげほげ"
          }
        ]
      }
EOT;
      $json = json_decode($responseString);
      return response()->json($json);
    });
    // レビュー情報 discas
    $router->get('work/{workId}/review/discas', function (Request $request, $workId) {
      $responseString = <<<EOT
      {
        "totalCount": 1,
        "averageRating": 4.0,
        "rows": [
          {
            "rating": 4.0,
            "contributor": "ホゲホゲ",
            "contributeDate": "2018-03-01",
            "contents": "ふがふが　ほげほげ　ふがふが　ほげほげ"
          }
        ]
      }
EOT;
      $json = json_decode($responseString);
      return response()->json($json);
    });
    // レビュー情報 tol
    $router->get('work/{workId}/review/tol', function (Request $request, $workId) {
      $responseString = <<<EOT
      {
        "totalCount": 1,
        "averageRating": 4.0,
        "rows": [
          {
            "rating": 4.0,
            "contributor": "ホゲホゲ",
            "contributeDate": "2018-03-01",
            "contents": "ふがふが　ほげほげ　ふがふが　ほげほげ"
          }
        ]
      }
EOT;
      $json = json_decode($responseString);
      return response()->json($json);
    });
    // 関連作品
    $router->get('work/{workId}/relation/works', function (Request $request, $workId) {
      $responseString = <<<EOT
      {
        "hasNext": true,
        "totalCount": 1,
        "rows": [
          {
            "workId": "PTA00007XDJP",
            "workTitle": "ピノキオ",
            "workTitleOrig": "PINOCCHIO",
            "saleType": "2",
            "workTypeId": "2",
            "jacketL": "https://cdn.store-tsutaya.tsite.jp/images/jacket/07483/4959241310644_1L.jpg",
            "saleStartDate": "1995-03-17",
            "bigGenreId": "EXT0000000WP",
            "bigGenreName": "キッズ",
            "mediumGenreId": "EXT000003SXI",
            "mediumGenreName": "ディズニー",
            "ratingName": "",
            "docText": "木のあやつり人形・ピノキオが命を与えられ、冒険を通して善悪を学び、本物の少年となっていくまでを描く。１９４０年初公開のディズニー名作アニメをデジタル・リマスターで蘇らせ、ボーナス・コンテンツを加えた３枚組にパッケージ。",
            "createdYear": "1940",
            "createdCountries": "アメリカ"
          }
        ]
      }
EOT;
      $json = json_decode($responseString);
      return response()->json($json);
    });
    // 関連動画
    $router->get('work/{workId}/relation/movie', function (Request $request, $workId) {
      $responseString = <<<EOT

      JSON HERE

EOT;
      $json = json_decode($responseString);
      return response()->json($json);
    });
    // 関連画像
    $router->get('work/{workId}/relation/pics', function (Request $request, $workId) {
      $responseString = <<<EOT
      {
        "hasNext": true,
        "totalCount": 1,
        "rows": [
          {
            "url": "//store-tsutaya.tsite.jp/images/bamen/00205/4959241958082_B001S.jpg"
          }
        ]
      }
EOT;
      $json = json_decode($responseString);
      return response()->json($json);
    });
    // 関連アーティスト
    $router->get('work/{workId}/relation/artist', function (Request $request, $workId) {
      $responseString = <<<EOT
      {
        "hasNext": true,
        "totalCount": 1,
        "rows": [
          {
            "workId": "PTA00007XDJP",
            "workTitle": "ピノキオ",
            "workTitleOrig": "PINOCCHIO",
            "saleType": "2",
            "workTypeId": "2",
            "jacketL": "https://cdn.store-tsutaya.tsite.jp/images/jacket/07483/4959241310644_1L.jpg",
            "saleStartDate": "1995-03-17",
            "bigGenreId": "EXT0000000WP",
            "bigGenreName": "キッズ",
            "mediumGenreId": "EXT000003SXI",
            "mediumGenreName": "ディズニー",
            "ratingName": "",
            "docText": "木のあやつり人形・ピノキオが命を与えられ、冒険を通して善悪を学び、本物の少年となっていくまでを描く。１９４０年初公開のディズニー名作アニメをデジタル・リマスターで蘇らせ、ボーナス・コンテンツを加えた３枚組にパッケージ。",
            "createdYear": "1940",
            "createdCountries": "アメリカ"
          }
        ]
      }
EOT;
      $json = json_decode($responseString);
      return response()->json($json);
    });


    // キャスト情報
    $router->get('cast/{castId}', function (Request $request, $workId) {
        return $json;
    });
    // 作品レコメンド（この作品を見た人はこんな作品もみています）
    $router->get('work/{workId}/recommend/other', function (Request $request, $workId) {
      $responseString = <<<EOT
      {
        "hasNext": true,
        "totalCount": 1,
        "rows": [
          {
            "workId": "PTA00007XDJP",
            "workTitle": "ピノキオ",
            "workTitleOrig": "PINOCCHIO",
            "saleType": "2",
            "workTypeId": "2",
            "jacketL": "https://cdn.store-tsutaya.tsite.jp/images/jacket/07483/4959241310644_1L.jpg",
            "saleStartDate": "1995-03-17",
            "bigGenreId": "EXT0000000WP",
            "bigGenreName": "キッズ",
            "mediumGenreId": "EXT000003SXI",
            "mediumGenreName": "ディズニー",
            "ratingName": "",
            "docText": "木のあやつり人形・ピノキオが命を与えられ、冒険を通して善悪を学び、本物の少年となっていくまでを描く。１９４０年初公開のディズニー名作アニメをデジタル・リマスターで蘇らせ、ボーナス・コンテンツを加えた３枚組にパッケージ。",
            "createdYear": "1940",
            "createdCountries": "アメリカ"
          }
        ]
      }
EOT;
      $json = json_decode($responseString);
      return response()->json($json);
    });
    // 作者レコメンド
    $router->get('work/{workId}/recommend/author', function (Request $request, $workId) {
      $responseString = <<<EOT
      {
        "hasNext": true,
        "totalCount": 1,
        "rows": [
          {
            "workId": "PTA00007XDJP",
            "workTitle": "ピノキオ",
            "workTitleOrig": "PINOCCHIO",
            "saleType": "2",
            "workTypeId": "2",
            "jacketL": "https://cdn.store-tsutaya.tsite.jp/images/jacket/07483/4959241310644_1L.jpg",
            "saleStartDate": "1995-03-17",
            "bigGenreId": "EXT0000000WP",
            "bigGenreName": "キッズ",
            "mediumGenreId": "EXT000003SXI",
            "mediumGenreName": "ディズニー",
            "ratingName": "",
            "docText": "木のあやつり人形・ピノキオが命を与えられ、冒険を通して善悪を学び、本物の少年となっていくまでを描く。１９４０年初公開のディズニー名作アニメをデジタル・リマスターで蘇らせ、ボーナス・コンテンツを加えた３枚組にパッケージ。",
            "createdYear": "1940",
            "createdCountries": "アメリカ"
          }
        ]
      }
EOT;
      $json = json_decode($responseString);
      return response()->json($json);
    });
    // 作品レコメンド
    $router->get('work/{workId}/recommend/artist', function (Request $request, $workId) {
      $responseString = <<<EOT
      {
        "hasNext": true,
        "totalCount": 1,
        "rows": [
          {
            "artist_id": 1,
            "artist_name": "ほげほげ"
          }
        ]
      }
EOT;
      $json = json_decode($responseString);
      return response()->json($json);
    });
    // 変換
    $router->get('convert/from/{id}/to/work_id', function (Request $request, $workId) {
        return $json;
    });
    // 変換
    $router->get('product/{cccFamilyCd}', function (Request $request, $workId) {
        return $json;
    });
    // 在庫
    $router->get('product/stock/{storeCd}/{cccFamilyCd}', function (Request $request, $workId) {
        return $json;
    });
    // キーワード検索
    $router->get('search/{keyword}', function (Request $request, $keyword) {
//        dd($keyword);
        $work = new WorkRepository();
        $result = $work->get('PTA0000RV0LG');
        return response()->json($result);
    });
    // キーワード検索サジェスト
    $router->get('product/stock/{storeCd}/{cccFamilyCd}', function (Request $request, $workId) {
        return $json;
    });

});
$router->group(['prefix' => env('URL_PATH_PREFIX') . env('API_VERSION')], function () use ($router) {
    // APIドキュメント
    $router->get('docs/swagger.json', function () {
        $swagger = \Swagger\scan(base_path('routes'));
        return response()->json($swagger);
    });
});
