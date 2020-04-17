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

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Repositories\StructureRepository;
use App\Repositories\SectionRepository;
use App\Repositories\BannerRepository;
use App\Repositories\WorkRepository;
use App\Repositories\ProductRepository;
use App\Repositories\DiscasRepository;
use App\Repositories\MintRepository;
use App\Repositories\TAPRepository;
use App\Repositories\TCSRepository;
use App\Repositories\PeopleRepository;
use App\Repositories\SeriesRepository;
use App\Repositories\TWSRepository;
use App\Repositories\HimoKeywordRepository;
use App\Repositories\PeopleRelatedWorksRepository;
use App\Repositories\RelateadWorkRepository;
use App\Repositories\RecommendOtherRepository;
use App\Repositories\HimoRepository;
use App\Repositories\RecommendTheaterRepository;
use App\Repositories\ReleaseCalenderRepository;
use App\Repositories\FavoriteRepository;
use App\Repositories\CouponRepository;
use App\Repositories\RentalUseRegistrationRepository;
use App\Repositories\PointRepository;
use App\Repositories\SectionPremiumRecommend;
use App\Repositories\StatusPremiumRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\PromotionRepository;
use App\Exceptions\AgeLimitException;
use App\Exceptions\ContentsException;
use App\Exceptions\NoContentsException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Carbon\Carbon;

// Api Group
$router->group([
    'prefix' => env('URL_PATH_PREFIX') . env('API_VERSION'),
    'middleware' => ['auth']
], function () use ($router) {

    // バージョン取得API
    $router->get('version', function () {
        $version = config('version');
        $version['version'] = hash('sha256', serialize($version));
        return response()->json($version)->header('X-Accel-Expires', '0');
    });

    // コンテンツ構成取得API
    $router->get('structure/{goodsType:dvd|book|cd|game}/{saleType:rental|sell}',
        function (Request $request, $goodsType, $saleType) {
            $structureRepository = new StructureRepository;
            $structureRepository->setLimit($request->input('limit', 10));
            $structureRepository->setOffset($request->input('offset', 0));
            // プレミアム対応にてAPIバージョンをv4にあげない為、旧アプリへsectionType=6を出さないようにする対応
            $isPremium = $request->input('premium', false);
            $isPremium = ($isPremium !== 'true') ? false : true;

            // プレミアム対応にてAPIバージョンをv4にあげない為、旧アプリへsectionType=6を出さないようにする対応
            $isRecommend = $request->input('recommend', false);
            $isRecommend = ($isRecommend !== 'true') ? false : true;

            // ThousandTags
            $isThousandTag = $request->input('thousandTag', false);
            $isThousandTag = ($isThousandTag !== 'true') ? false : true;

            // version
            $app_version = $request->input('version', '0.0.0');

            $structures = $structureRepository->get($goodsType, $saleType, $isPremium, $isRecommend, $isThousandTag, $app_version);
            if ($structures->getTotalCount() == 0) {
                throw new NoContentsException;
            }
            $response = [
                'hasNext' => $structures->getHasNext(),
                'totalCount' => $structures->getTotalCount(),
                'rows' => $structures->getRows(),
            ];
            return response()->json($response)->header('X-Accel-Expires', '600');
        });

    // コンテンツ構成取得API
    $router->get('structure/premium/dvd/rental', function (Request $request) {
        $structureRepository = new StructureRepository;
        $structureRepository->setLimit($request->input('limit', 10));
        $structureRepository->setOffset($request->input('offset', 0));
        // version
        $appVersion = $request->input('version', '0.0.0');
        $structures = $structureRepository->get('premiumDvd', 'rental', true, false, false, $appVersion);

        if ($structures->getTotalCount() == 0) {
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => $structures->getHasNext(),
            'totalCount' => $structures->getTotalCount(),
            'rows' => $structures->getRows(),
        ];
        return response()->json($response)->header('X-Accel-Expires', '600');
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
        return response()->json($response)->header('X-Accel-Expires', '600');
    });

    // 通常セクション取得API
    $router->get('section/{goodsType:dvd|book|cd|game}/{saleType:rental|sell}/{sectionName}',
        function (Request $request, $goodsType, $saleType, $sectionName) {
            $sectionRepository = new SectionRepository;
            $sectionRepository->setLimit($request->input('limit', 10));
            $sectionRepository->setOffset($request->input('offset', 0));
            $premiumFlag = $request->input('premium', false);
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
            if ($premiumFlag !== 'true') {
                foreach ($response['rows'] as $rowKey => $row) {
                    unset($response['rows'][$rowKey]['isPremium']);
                    unset($response['rows'][$rowKey]['isPremiumNet']);
                    unset($response['rows'][$rowKey]['allPremiumNet']);
                    unset($response['rows'][$rowKey]['premiumNetStatus']);
                }
            }
            return response()->json($response)->header('X-Accel-Expires', '600');
        });

    // プレミアム通常セクション取得API
    $router->get('section/premium/dvd/rental/{sectionName}', function (Request $request, $sectionName) {
        $sectionRepository = new SectionRepository;
        $sectionRepository->setLimit($request->input('limit', 10));
        $sectionRepository->setOffset($request->input('offset', 0));
        $sectionRepository->setSupplementVisible(true);

        $section = $sectionRepository->normal('premiumDvd', 'rental', $sectionName);

        if ($section->getTotalCount() == 0) {
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => $section->getHasNext(),
            'totalCount' => $section->getTotalCount(),
            'rows' => $section->getRows()
        ];

        return response()->json($response)->header('X-Accel-Expires', '600');
    });


    // 映画漬けセクション取得API
    $router->get('section/premium/dvd/rental/movie/{sectionName}', function (Request $request, $sectionName) {
        $sectionRepository = new SectionRepository;
        $sectionRepository->setLimit($request->input('limit', 10));
        $sectionRepository->setOffset($request->input('offset', 0));
        $sectionRepository->setSupplementVisible(true);

        // プレミアムフラグを渡して取得
        $section = $sectionRepository->normal('premiumDvd', 'rental', $sectionName, true);
        if ($section->getTotalCount() == 0) {
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => $section->getHasNext(),
            'totalCount' => $section->getTotalCount(),
            'rows' => $section->getRows()
        ];
        return response()->json($response)->header('X-Accel-Expires', '600');
    });


    // TOP用ジャンル特定版プレミアムリコメンドAPI
    $router->get('section/premium/dvd/rental/{genre}/recommend', function (Request $request, $genre) {
        $urlCd = [];

        $sectionPremiumRecommend = new SectionPremiumRecommend;
        $sectionPremiumRecommend->setLimit($request->input('limit', 10));
        $sectionPremiumRecommend->setOffset($request->input('offset', 0));
        $sectionPremiumRecommend->getWorks($urlCd, $genre);
        // プレミアムフラグを渡して取得
        if ($sectionPremiumRecommend->getTotalCount() == 0) {
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => $sectionPremiumRecommend->getHasNext(),
            'totalCount' => $sectionPremiumRecommend->getTotalCount(),
            'rows' => $sectionPremiumRecommend->getRows()
        ];
        return response()->json($response)->header('X-Accel-Expires', '600');
    });

    // TOP用プレミアムリコメンドAPI
    $router->post('section/premium/dvd/rental/recommend', function (Request $request) {
        $body = json_decode($request->getContent(), true);
        $urlCd = isset($body['urlCd']) ? $body['urlCd'] : '';

        $sectionPremiumRecommend = new SectionPremiumRecommend;
        $sectionPremiumRecommend->setLimit($request->input('limit', 10));
        $sectionPremiumRecommend->setOffset($request->input('offset', 0));
        $sectionPremiumRecommend->getWorks($urlCd);
        // プレミアムフラグを渡して取得
        if ($sectionPremiumRecommend->getTotalCount() == 0) {
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => $sectionPremiumRecommend->getHasNext(),
            'totalCount' => $sectionPremiumRecommend->getTotalCount(),
            'rows' => $sectionPremiumRecommend->getRows()
        ];
        return response()->json($response)->header('X-Accel-Expires', '600');
    });

    // TOP用プレミアムレコメンド net見放題API
    $router->get('section/premium/dvd/rental/recommend/net', function (Request $request) {
        $sectionPremiumRecommend = new SectionPremiumRecommend;
        $sectionPremiumRecommend->setLimit($request->input('limit', 10));
        $sectionPremiumRecommend->setOffset($request->input('offset', 0));

        $sectionPremiumRecommend->getNetWorks();

        if ($sectionPremiumRecommend->getTotalCount() == 0) {
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => $sectionPremiumRecommend->getHasNext(),
            'totalCount' => $sectionPremiumRecommend->getTotalCount(),
            'rows' => $sectionPremiumRecommend->getRows()
        ];
        return response()->json($response)->header('X-Accel-Expires', '600');
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
        return response()->json($response)->header('X-Accel-Expires', '600');
    });

    // 【API】レコメンドバナー用API
    $router->get('section/banner/recommend/{image}', function (Request $request, $image) {
        if ($image == '') {
            throw new NoContentsException;
        }

        $recommendImageHost = env('RECOMMEND_IMAGE_HOST');
        $imageNew = str_replace('.jpg', '_notice.jpg', $image);

        return response()->json([
            'imageUrl' => $recommendImageHost . $image,
            'imageWithBadgeUrl' => $recommendImageHost . $imageNew
        ])->header('X-Accel-Expires', '86400');
    });

    // レコメンドセクション取得API
    $router->get('section/ranking/{codeType:himo|agg}/{code}[/{period}]',
        function (Request $request, $codeType, $code, $period = null) {
            $sectionRepository = new SectionRepository;
            $sectionRepository->setLimit(20);
            $sectionRepository->setSupplementVisible($request->input('supplementVisibleFlg', false));
            $premiumFlag = $request->input('premium', false);
            $rows = $sectionRepository->ranking($codeType, $code, $period);
            if (empty($rows)) {
                throw new NoContentsException;
            }
            // プレミアムフラグを返却しないようにする。
            // 現状はDVDレンタルの為、臨時対応として取得元で制御は行わない
            if ($premiumFlag !== 'true') {
                foreach ($rows as $rowKey => $row) {
                    unset($rows[$rowKey]['isPremium']);
                    unset($rows[$rowKey]['isPremiumNet']);
                    unset($rows[$rowKey]['allPremiumNet']);
                    unset($rows[$rowKey]['premiumNetStatus']);
                }
            }
            $response = [
                'hasNext' => $sectionRepository->getHasNext(),
                'totalCount' => $sectionRepository->getTotalCount(),
                'aggregationPeriod' => $sectionRepository->getAggregationPeriod(),
                'rows' => $rows
            ];
            if (!empty($sectionRepository->getRankingTitle())) {
                $response['title'] = $sectionRepository->getRankingTitle();
            }
            return response()->json($response)->header('X-Accel-Expires', '86400');
        });

    // レコメンドセクション取得API
    $router->get('section/release/manual/{tapCategoryId}[/{releaseDateTo}]',
        function (Request $request, $tapCategoryId, $releaseDateTo = null) {
            if (empty($releaseDateTo)) {
                $releaseDateTo = date('Ymd', strtotime('next sunday'));
            }
            $premiumFlag = $request->input('premium', false);
            $sectionRepository = new SectionRepository;
            $sectionRepository->setSupplementVisible($request->input('supplementVisibleFlg', false));
            $sectionData = $sectionRepository->releaseManual($tapCategoryId, $releaseDateTo);
            // プレミアムフラグを返却しないようにする。
            // 現状はDVDレンタルの為、臨時対応として取得元で制御は行わない
            if ($premiumFlag !== 'true') {
                foreach ($sectionData['rows'] as $rowKey => $row) {
                    unset($sectionData['rows'][$rowKey]['isPremium']);
                    unset($sectionData['rows'][$rowKey]['isPremiumNet']);
                    unset($sectionData['rows'][$rowKey]['allPremiumNet']);
                    unset($sectionData['rows'][$rowKey]['premiumNetStatus']);
                }
            }
            return response()->json($sectionData)->header('X-Accel-Expires', '600');
        });

    // レコメンドセクション取得API
    $router->get('section/release/auto/{genreId}/{storeProductItemCd}',
        function (Request $request, $genreId, $storeProductItemCd) {
            $sectionRepository = new SectionRepository;
            $sectionRepository->setSupplementVisible($request->input('supplementVisibleFlg', false));
            $sectionData = $sectionRepository->releaseAuto($genreId, $storeProductItemCd);
            return response()->json($sectionData)->header('X-Accel-Expires', '86400');
        });

    // レコメンドセクション取得API
    $router->get('section/release/himo/{periodType}/{tapGenreId}', function (Request $request, $periodType, $genreId) {
        $premiumFlag = $request->input('premium', false);
        $sectionRepository = new SectionRepository;
        $sectionRepository->setSupplementVisible($request->input('supplementVisibleFlg', false));
        $sectionData = $sectionRepository->releaseHimo($periodType, $genreId);

        if (empty($sectionData)) {
            throw new NoContentsException;
        }
        // プレミアムフラグを返却しないようにする。
        // 現状はDVDレンタルの為、臨時対応として取得元で制御は行わない
        if ($premiumFlag !== 'true') {
            foreach ($sectionData['rows'] as $rowKey => $row) {
                unset($sectionData['rows'][$rowKey]['isPremium']);
                unset($sectionData['rows'][$rowKey]['isPremiumNet']);
                unset($sectionData['rows'][$rowKey]['allPremiumNet']);
                unset($sectionData['rows'][$rowKey]['premiumNetStatus']);
            }
        }
        return response()->json($sectionData)->header('X-Accel-Expires', '86400');
    });

    // 作品基本情報
    $router->get('work/{workId}', function (Request $request, $workId) {
        $work = new WorkRepository();
        $saleType = $request->input('saleType', $work::SALE_TYPE_RENTAL);
        $work->setSaleType($saleType);
        $ageLimitCheck = $request->input('ageLimitCheck', false);
        $work->setAgeLimitCheck($ageLimitCheck);
        $result = $work->get($workId);
        if (empty($result)) {
            throw new NoContentsException;
        }

        if ($result['premiumNetStatus'] === 1) {
            // Add allPremiumNet in response
            $productModel = new \App\Model\Product();
            if($productModel->processAllPremiumNet($workId) == true) {
                $result['premiumNetStatus'] = 2;
            }
        }

        //$result['allPremiumNet'] = $productModel->processAllPremiumNet($workId);

        if ($result['premiumNetStatus'] === 1) {
            // Add allPremiumNet in response
            $productModel = new \App\Model\Product();
            if($productModel->processAllPremiumNet($workId) == true) {
                $result['premiumNetStatus'] = 2;
            }
        }

        //$result['allPremiumNet'] = $productModel->processAllPremiumNet($workId);

        // 映画リクエストでレスポンスがなかった場合
        if (
            $result['msdbItem'] === $work::MSDB_ITEM_VIDEO &&
            $result['workTypeId'] !== $work::WORK_TYPE_THEATER &&
            $saleType === $work::SALE_TYPE_THEATER
        ) {
            throw new ContentsException('202-002');
        }

        if (empty(array_key_exists('makerCd', $result)) || empty($result)) {
            throw new NoContentsException;
        }

        $checkAgeLimit = checkAgeLimit(
            $ageLimitCheck,
            $result['ratingId'],
            $result['adultFlg'],
            $result['bigGenreId'],
            $result['mediumGenreId'],
            $result['smallGenreId'],
            $result['makerCd']
        );
        if (!$checkAgeLimit) {
            throw new ContentsException('202-001');
        }
        $response = [
            'data' => $result
        ];
        return response()->json($response)->header('X-Accel-Expires', '86400');
    });
    // 商品一覧情報取得
    $router->get('work/{workId}/products', function (Request $request, $workId) {
        $product = new ProductRepository();
        $product->setLimit($request->input('limit', 10));
        $product->setOffset($request->input('offset', 0));
        $product->setSaleType($request->input('saleType'));
        $product->setSort($request->input('sort', 'new'));

        // #104199 edit start　ここ追加
        $isDummy = $request->input('isDummy', false);
        $isDummy = ($isDummy !== 'true') ? false : true;
        $product->setIsDummy($isDummy);
        // #104199 edit end

        $result = $product->getNarrow($workId);
        if (empty($result)) {
            throw new NoContentsException;
        }

        // START 109341
        foreach ($result as &$item) {
            $taxOut_key = array_search('priceTaxOut', array_keys($item));
            $priceTaxIn = floor((int)$item['priceTaxOut'] * ProductRepository::TAX_RATE);
            $item = array_slice($item, 0, $taxOut_key + 1, true) +
                ['priceTaxIn' => (string)($priceTaxIn == 0 ? '' : $priceTaxIn)] +
                array_slice($item, $taxOut_key + 1, count($item) - 1, true);
        }
        // END 109341

        $response = [
            'hasNext' => $product->getHasNext(),
            'totalCount' => $product->getTotalCount(),
            'rows' => $result
        ];
        return response()->json($response)->header('X-Accel-Expires', '86400');
    });
    // 商品一覧情報取得（DVDレンタル時のグルーピング（問い合わせ時のLimit数がおかしくなる為にグルーピングが必要））
    $router->get('work/{workId}/products/rental', function (Request $request, $workId) {
        $product = new ProductRepository();
        $product->setLimit($request->input('limit', 10));
        $product->setOffset($request->input('offset', 0));
        $sort = $request->input('sort', 'new');
        $result = $product->getRentalGroup($workId, $sort);
        if (empty($result)) {
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => $product->getHasNext(),
            'totalCount' => $product->getTotalCount(),
            'rows' => $result
        ];
        return response()->json($response)->header('X-Accel-Expires', '86400');
    });
    // Himo作品ID作品検索
    $router->get('work/{workId}/products/has', function (Request $request, $workId) {
        $work = new WorkRepository();
        $work->setSaleType($request->input('saleType', 'rental'));
        $work->setAgeLimitCheck($request->input('ageLimitCheck', false));
        $result = $work->getNarrowColumns($workId);
        if (empty($result)) {
            throw new NoContentsException;
        }
        $response = [
            'data' => $result
        ];
        return response()->json($response)->header('X-Accel-Expires', '86400');
    });
    // キャストスタッフ一覧取得
    $router->get('work/{workId}/people', function (Request $request, $workId) {
        $peopleRepository = new PeopleRepository();
        $peopleRepository->setLimit($request->input('limit', 10));
        $peopleRepository->setOffset($request->input('offset', 0));
        $saleType = $request->input('saleType');
        $people = $peopleRepository->getNarrow($workId, $saleType);
        $response = [
            'hasNext' => $people->getHasNext(),
            'totalCount' => $people->getTotalCount(),
            'rows' => $people->getRows()
        ];

        if (empty($response)) {
            throw new NoContentsException;
        }
        return response()->json($response)->header('X-Accel-Expires', '86400');
    });

    // 作品シリーズ情報
    $router->get('work/{workId}/series', function (Request $request, $workId) {
        $series = new SeriesRepository();
        $series->setLimit($request->input('limit', 10));
        $series->setOffset($request->input('offset', 0));
        $series->setAgeLimitCheck($request->input('ageLimitCheck', false));
        $saleType = $request->input('saleType');
        $response = $series->getNarrow($workId, $saleType);
        if (empty($response)) {
            throw new NoContentsException;
        }
        return response()->json($response)->header('X-Accel-Expires', '86400');
    });
    // レビュー情報 filmarks
    $router->get('work/{workId}/review/filmarks', function (Request $request, $workId) {
        $work = new WorkRepository();
        $tapRepository = new TAPRepository();
        $workData = $work->get($workId);
        if (empty($workData['filmarksId'])) {
            throw new NoContentsException;
        }
        $tapRepository->setLimit($request->input('limit', 10));
        $response = $tapRepository->getReview($workData['filmarksId']);
        if (empty($response)) {
            throw new NoContentsException;
        }
        return response()->json($response)->header('X-Accel-Expires', '86400');
    });
    // レビュー情報 discas
    $router->get('work/{workId}/review/discas', function (Request $request, $workId) {
        $discasProduct = new \App\Model\DiscasProduct();
        $discasRepository = new DiscasRepository();

        $productData = (array)$discasProduct->setConditionByWorkId($workId)
            ->selectCamel(['ccc_product_id'])
            ->getOne();
        if (empty($productData)) {
            throw new NoContentsException;
        }
        $discasRepository->setLimit($request->input('limit', 10));
        $discasRepository->setOffset($request->input('offset', 0));
        $response = $discasRepository->getReview($productData['cccProductId']);
        if (empty($response)) {
            throw new NoContentsException;
        }

        return response()->json($response)->header('X-Accel-Expires', '86400');
    });
    // レビュー情報 tol
    $router->get('work/{workId}/review/tol', function (Request $request, $workId) {
        $work = new WorkRepository();
        $workData = $work->get($workId);
        if (empty($workData['urlCd'])) {
            throw new NoContentsException;
        }

        $twsRepository = new TWSRepository();
        $twsRepository->setLimit($request->input('limit', 10));
        $twsRepository->setOffset($request->input('offset', 0));

        $response = $twsRepository->getReview($workData['urlCd']);
        if (empty($response)) {
            throw new NoContentsException;
        }

        return response()->json($response)->header('X-Accel-Expires', '86400');
    });
    // レビュー情報 comicspace
    $router->get('work/{workId}/review/comicspace', function (Request $request, $workId) {
        $work = new WorkRepository();
        $workData = $work->get($workId);
        
        if (empty($workData)) {
            throw new NoContentsException;
        }

        $tcsRepository = new TCSRepository();
        $tcsRepository->setLimit($request->input('limit', 10));
        $response = $tcsRepository->getReview($workId);
        if (empty($response)) {
            throw new NoContentsException;
        }
        return response()->json($response)->header('X-Accel-Expires', '86400');
    });
    // 関連作品
    $router->get('work/{workId}/relation/works', function (Request $request, $workId) {
        $relateadWorkRepository = new RelateadWorkRepository;
        $relateadWorkRepository->setLimit($request->input('limit', 10));
        $relateadWorkRepository->setOffset($request->input('offset', 0));
        $relateadWorkRepository->setAgeLimitCheck($request->input('ageLimitCheck', false));
        $results = $relateadWorkRepository->getNarrow($workId);
        if (empty($results)) {
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => $relateadWorkRepository->getHasNext(),
            'totalCount' => $relateadWorkRepository->getTotalCount(),
            'rows' => $results
        ];
        return response()->json($response)->header('X-Accel-Expires', '86400');
    });

    // 関連画像
    $router->get('work/{workId}/relation/pics', function (Request $request, $workId) {
        $work = new WorkRepository();
        $workData = $work->get($workId);

        $relationPics = json_decode($workData['sceneL']);


        if (empty($relationPics)) {
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
        return response()->json($response)->header('X-Accel-Expires', '86400');
    });
    // 関連アーティスト
    $router->get('work/{workId}/relation/artist', function (Request $request, $workId) {
        $recommendArtistRepository = new \App\Repositories\RecommendArtistRepository();
        $recommendArtistRepository->setLimit($request->input('limit', 10));
        $recommendArtistRepository->setOffset($request->input('offset', 0));
        $response = $recommendArtistRepository->getArtist($workId);
        return response()->json($response)->header('X-Accel-Expires', '86400');
    });
    // 関連動画
    $router->get('work/{workId}/relation/trailer', function (Request $request, $workId) {
        $work = new WorkRepository();
        $relationTrailers = $work->getTrailerByWorkId($workId);
        if (empty($relationTrailers)) {
            throw new NoContentsException;
        }

        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);
        $total = count($relationTrailers);
        $rows = array_slice($relationTrailers, $offset, $limit);
        if (empty($rows)) {
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => ($offset + $limit < $total),
            'totalCount' => $total,
            'rows' => $rows
        ];

        return response()->json($response)->header('X-Accel-Expires', '86400');
    });
    // キャスト情報
    $router->get('cast/{castId}', function (Request $request, $workId) {
        return response()->json($json)->header('X-Accel-Expires', '86400');
    });
    // 作品レコメンド（この作品を見た人はこんな作品もみています）
    $router->get('work/{workId}/recommend/other', function (Request $request, $workId) {
        $recommendOtherRepository = new RecommendOtherRepository;
        $recommendOtherRepository->setLimit($request->input('limit', 10));
        $recommendOtherRepository->setOffset($request->input('offset', 0));
        $recommendOtherRepository->setAgeLimitCheck($request->input('ageLimitCheck', false));
        $rows = $recommendOtherRepository->getWorks($workId, $request->input('saleType'));
        if (empty($rows)) {
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => $recommendOtherRepository->getHasNext(),
            'totalCount' => $recommendOtherRepository->getTotalCount(),
            'rows' => $rows
        ];
        return response()->json($response)->header('X-Accel-Expires', '86400');
    });
    // 作者レコメンド
    $router->get('work/{workId}/recommend/author', function (Request $request, $workId) {
        $peopleRelatedWorksRepository = new PeopleRelatedWorksRepository();
        $peopleRelatedWorksRepository->setLimit($request->input('limit', 10));
        $peopleRelatedWorksRepository->setOffset($request->input('offset', 0));
        $peopleRelatedWorksRepository->setSort($request->input('sort', 'new'));
        $peopleRelatedWorksRepository->setAgeLimitCheck($request->input('ageLimitCheck', false));
        $rows = $peopleRelatedWorksRepository->getWorks($workId);
        if (empty($rows)) {
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => $peopleRelatedWorksRepository->getHasNext(),
            'totalCount' => $peopleRelatedWorksRepository->getTotalCount(),
            'rows' => $rows
        ];
        return response()->json($response)->header('X-Accel-Expires', '86400');
    });
    // 作品レコメンド
    $router->get('work/{workId}/recommend/artist', function (Request $request, $workId) {
        $peopleRelatedWorksRepository = new PeopleRelatedWorksRepository();
        $peopleRelatedWorksRepository->setOffset($request->input('offset', 0));
        $peopleRelatedWorksRepository->setLimit($request->input('limit', 10));
        $peopleRelatedWorksRepository->setSort($request->input('sort', 'new'));
        $peopleRelatedWorksRepository->setAgeLimitCheck($request->input('ageLimitCheck', false));
        $rows = $peopleRelatedWorksRepository->getWorksByArtist($workId);
        if (empty($rows)) {
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => $peopleRelatedWorksRepository->getHasNext(),
            'totalCount' => $peopleRelatedWorksRepository->getTotalCount(),
            'rows' => $rows
        ];
        return response()->json($response)->header('X-Accel-Expires', '86400');
    });
    // 上映映画用レコメンド
    $router->get('work/{workId}/recommend/theater', function (Request $request, $workId) {
        $recommendTheaterRepository = new RecommendTheaterRepository();
        $recommendTheaterRepository->setOffset($request->input('offset', 0));
        $recommendTheaterRepository->setLimit($request->input('limit', 10));
        $recommendTheaterRepository->setSaleType($request->input('saleType', 'new'));
        $recommendTheaterRepository->setAgeLimitCheck($request->input('ageLimitCheck', false));
        $rows = $recommendTheaterRepository->get($workId);
        if (empty($rows)) {
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => $recommendTheaterRepository->getHasNext(),
            'totalCount' => $recommendTheaterRepository->getTotalCount(),
            'rows' => $rows
        ];
        return response()->json($response)->header('X-Accel-Expires', '86400');
    });
    // 変換
    $router->get('convert/work/{idType}/{id}', function (Request $request, $idType, $id) {
        $workRepository = new WorkRepository();
        $response = $workRepository->convert($idType, $id);
        if (empty($response)) {
            throw new NoContentsException;
        }
        return response()->json($response)->header('X-Accel-Expires', '86400');
    });

    // 変換
    $router->get('product/{productUniqueId}', function (Request $request, $productUniqueId) {
        $productRepository = new ProductRepository();
        $result = $productRepository->get($productUniqueId);
        if (empty($result)) {
            throw new NoContentsException;
        }

        // START 109341
        $taxOut_key = array_search('priceTaxOut', array_keys($result));
        $priceTaxIn = floor((int)$result['priceTaxOut'] * ProductRepository::TAX_RATE);
        $result = array_slice($result, 0, $taxOut_key + 1, true) +
            ['priceTaxIn' => (string)($priceTaxIn == 0 ? '' : $priceTaxIn)] +
            array_slice($result, $taxOut_key + 1, count($result) - 1, true);
        // END 109341

        $response = [
            'data' => $result
        ];
        return response()->json($response)->header('X-Accel-Expires', '86400');
    });

    // 店舗在庫
    $router->get('product/stock/{storeCd}/{productKey}', function (Request $request, $storeCd, $productKey) {
        $productRepository = new ProductRepository();
        $response = $productRepository->stock($storeCd, $productKey);
        if (empty($response)) {
            throw new NoContentsException;
        }
        return response()->json($response)->header('X-Accel-Expires', '0');
    });

    //人物関連作品取得
    $router->get('people/{personId}', function (Request $request, $personId) {
        $work = new WorkRepository();
        $work->setLimit($request->input('limit', 10));
        $work->setOffset($request->input('offset', 0));
        $work->setSaleType($request->input('saleType', null));
        $work->setAgeLimitCheck($request->input('ageLimitCheck', false));

        $sort = $request->input('sort', '');
        $itemType = $request->input('itemType', 'all');

        $response = $work->person($personId, $sort, $itemType);
        if (empty($response)) {
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => $work->getHasNext(),
            'totalCount' => $work->getTotalCount(),
            'rows' => $response
        ];


        return response()->json($response)->header('X-Accel-Expires', '86400');
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
        $work->setAgeLimitCheck($request->input('ageLimitCheck', false));
        $keyword = urldecode($keyword);
        $response = $work->searchKeyword(
            $keyword,
            $sort,
            $itemType,
            $periodType,
            $adultFlg);
        if (empty($response)) {
            throw new NoContentsException;
        }
        return response()->json($response)->header('X-Accel-Expires', '86400');
    });

    // キーワードサジェスト
    $router->get('search/suggest/{keyword}', function (Request $request, $keyword) {
        $himoKeywordRepository = new HimoKeywordRepository();
        $himoKeywordRepository->setLimit($request->input('limit', 10));
        $himoKeywordRepository->setOffset($request->input('offset', 0));
        $keyword = urldecode($keyword);
        $keywords = $himoKeywordRepository->get($keyword);
        if (empty($keywords)) {
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => $himoKeywordRepository->getHasNext(),
            'totalCount' => $himoKeywordRepository->getTotalCount(),
            'rows' => $keywords
        ];
        return response()->json($response)->header('X-Accel-Expires', '86400');
    });

    // ジャンルからの作品一覧取得
    $router->get('genre/{genreId}', function (Request $request, $genreId) {
        $workRepository = new WorkRepository();
        $workRepository->setLimit($request->input('limit', 10));
        $workRepository->setOffset($request->input('offset', 0));
        $saleType = $request->input('saleType', null);
        if (empty($saleType)) {
            throw new BadRequestHttpException;
        }
        $workRepository->setSaleType($saleType);
        $workRepository->setSort($request->input('sort'));
        $workRepository->setAgeLimitCheck($request->input('ageLimitCheck', false));
        $genreId = urldecode($genreId);
        $rows = $workRepository->genre($genreId);
        if (empty($rows)) {
            throw new NoContentsException;
        }
        $response = [
            'hasNext' => $workRepository->getHasNext(),
            'totalCount' => $workRepository->getTotalCount(),
            'rows' => $rows
        ];
        return response()->json($response)->header('X-Accel-Expires', '86400');
    });

    $router->get('release/has/recommend', function () {
        $releaseCalenderRepository = new ReleaseCalenderRepository();
        $response = $releaseCalenderRepository->hasRecommend();
        $response = [
            'data' => $response
        ];
        return response()->json($response)->header('X-Accel-Expires', '3600');
    });

    $router->get('release/{month}/{genreId}', function (Request $request, $month, $genreId) {
        $releaseCalenderRepository = new ReleaseCalenderRepository();
        $releaseCalenderRepository->setLimit($request->input('limit', 10));
        $releaseCalenderRepository->setOffset($request->input('offset', 0));
        $releaseCalenderRepository->setMonth($month);
        $releaseCalenderRepository->setGenreId($genreId);
        $releaseCalenderRepository->setSort($request->input('sort'));
        $releaseCalenderRepository->setMediaFormat($request->input('cdFormatType'));
        $releaseCalenderRepository->setOnlyReleased($request->input('onlyReleased', 'false'));
        $releaseCalenderRepository->setAgeLimitCheck($request->input('ageLimitCheck', false));
        // rowが無くても、200で返す
        $withUpdate = $request->input('update', false);

        $rows = $releaseCalenderRepository->get();
        // コミックレンタルだった場合は、204を返却しない
        if ($withUpdate === 'true') {
            $carbon = new carbon;
            $nextWednesday = date('Y-m-d', strtotime('next WEDNESDAY'));
            $nextWednesdayMonth = date('Y-m', strtotime('next WEDNESDAY'));

            //月を比較
            $nowMonth = \Carbon\Carbon::now()->format('Y-m');

            if ($nextWednesdayMonth != $nowMonth) {
                $updateDate = '';
            } else {
                if (date('Y-m-d', strtotime('WEDNESDAY')) === date('Y-m-d')) {
                    $updateDate = date('Y-m-d');
                } else {
                    $updateDate = $nextWednesday;
                }
            }

            if (empty($rows)) {
                $response = [
                    'hasNext' => false,
                    'totalCount' => 0,
                    'updateDate' => $updateDate,
                    'baseMonth' => \Carbon\Carbon::now()->format('Y-m'),
                    'rows' => []
                ];
            } else {
                $response = [
                    'hasNext' => $releaseCalenderRepository->getHasNext(),
                    'totalCount' => $releaseCalenderRepository->getTotalCount(),
                    'updateDate' => $updateDate,
                    'baseMonth' => \Carbon\Carbon::now()->format('Y-m'),
                    'rows' => $rows
                ];
            }
        } else {
            if (empty($rows)) {
                throw new NoContentsException;
            }
            $response = [
                'hasNext' => $releaseCalenderRepository->getHasNext(),
                'totalCount' => $releaseCalenderRepository->getTotalCount(),
                // 常に当月を出力するように変更
                'baseMonth' => \Carbon\Carbon::now()->format('Y-m'),
                'rows' => $rows
            ];
        }
        return response()->json($response)->header('X-Accel-Expires', '3600');
    });
    $router->get('release/static/{month}/{genreId}', function (Request $request, $month, $genreId) {
        $releaseCalenderRepository = new ReleaseCalenderRepository();
        $releaseCalenderRepository->setMonth($month);
        $releaseCalenderRepository->setGenreId($genreId);
        $rows = $releaseCalenderRepository->getStatic();
        if (empty($rows)) {
            throw new NoContentsException;
        }
        $response = [
            // 常に当月を出力するように変更
            'baseMonth' => \Carbon\Carbon::now()->format('Y-m'),
            'rows' => $rows
        ];
        return response()->json($response)->header('X-Accel-Expires', '3600');
    });

    $router->get('ranking/{codeType:himo|agg}/{code}[/{period}]',
        function (Request $request, $codeType, $code, $period = null) {
            $sectionRepository = new SectionRepository;
//        $sectionRepository->setLimit($request->input('limit', 20));
            $sectionRepository->setLimit(30);
            $sectionRepository->setPage($request->input('page', 1));
            $sectionRepository->setSupplementVisible($request->input('supplementVisibleFlg', false));
            $rows = $sectionRepository->ranking($codeType, $code, $period);
            if (empty($rows)) {
                throw new NoContentsException;
            }
            $response = [
                'hasNext' => $sectionRepository->getHasNext(),
                'totalCount' => $sectionRepository->getTotalCount(),
                'aggregationPeriod' => $sectionRepository->getAggregationPeriod(),
                'rows' => $rows
            ];
            if (!empty($sectionRepository->getRankingTitle())) {
                $response['title'] = $sectionRepository->getRankingTitle();
            }
            return response()->json($response)->header('X-Accel-Expires', '86400');
        });

    // Favorite list
    $router->post('favorite/list', function (Request $request) {
        $bodyObj = json_decode($request->getContent(), true);
        $tlsc = isset($bodyObj['tlsc']) ? $bodyObj['tlsc'] : '';
        // Check tlsc
        if (empty($tlsc)) {
            throw new BadRequestHttpException;
        }
        $favoriteRepository = new FavoriteRepository();
        $favoriteRepository->setTlsc($bodyObj['tlsc']);
        // Check version
        $version = isset($bodyObj['version']) ? $bodyObj['version'] : null;
        $limit = isset($bodyObj['limit']) ? $bodyObj['limit'] : 2000;
        $offset = isset($bodyObj['offset']) ? $bodyObj['offset'] : 0;
        $sort = isset($bodyObj['sort']) ? $bodyObj['sort'] : 'new';
        $favoriteRepository->setLimit($limit);
        $favoriteRepository->setOffset($offset);
        $favoriteRepository->setSort($sort);
        $versionResponse = $favoriteRepository->getFavoriteVersion($bodyObj['tlsc']);
        // Check version
        if ($versionResponse == $version) {
            $versionUpdateString = '{
                "isUpdate": false,
                "rows":null
            }';
            $response = json_decode($versionUpdateString);
            return response()->json($response);
        }
        $response = $favoriteRepository->list($bodyObj);
        // Check number record return
        if (!isset($response) || !array_key_exists('totalCount', $response) || $response['totalCount'] <= 0) {
            $response = [
                'hasNext' => false,
                'isUpdate' => true,
                'totalCount' => 0,
                'version' => $versionResponse,
                'rows' => []
            ];
            return response()->json($response);
        }
        $response = $favoriteRepository->formatData($response);
        $response['version'] = $versionResponse;
        return response()->json($response);
    });

    // Favorite works
    $router->post('/work/bulk', function (Request $request) {
        $premiumFlag = $request->input('premium', false);
        $body_obj = json_decode($request->getContent(), true);
        $saleType = isset($body_obj['saleType']) ? $body_obj['saleType'] : '';
        $idType = isset($body_obj['idType']) ? $body_obj['idType'] : '';
        // Check if have no data for input saleType
        if (empty($saleType)) {
            throw new BadRequestHttpException;
        }
        // Check ids must have value
        $idsArray = isset($body_obj['ids']) ? $body_obj['ids'] : '';
        if (empty($idsArray) || count($idsArray) <= 0) {
            throw new BadRequestHttpException;
        }
        $workRepository = new WorkRepository();
        if (empty($idType)) {
            // Covert urlCd to id if have
            $workIdsArray = $workRepository->convertUrlCdToWorkId($idsArray);
        } else {
            $workIdsArray = $workRepository->convertWorkId($idsArray, $idType);
        }

        $ageLimitCheck = isset($body_obj['ageLimitCheck']) ? $body_obj['ageLimitCheck'] : false;
        $workRepository->setAgeLimitCheck($ageLimitCheck);
        $workRepository->setSaleType($saleType);
        // Get work data
        $workData = $workRepository->getWorkList($workIdsArray, null, null, false, $saleType);
        if (empty($workData)) {
            throw new NoContentsException;
        }
        // Format output work data
        $workDataFormat = $workRepository->formatOutputBulk($workIdsArray, $workData);
        // プレミアムフラグを返却しないようにする。
        // 現状はDVDレンタルの為、臨時対応として取得元で制御は行わない
        if ($premiumFlag !== 'true') {
            foreach ($workDataFormat as $rowKey => $row) {
                unset($workDataFormat[$rowKey]['isPremium']);
                unset($workDataFormat[$rowKey]['isPremiumNet']);
                unset($workDataFormat[$rowKey]['allPremiumNet']);
                unset($workDataFormat[$rowKey]['premiumNetStatus']);
            }
        }

        // START 109341
        foreach ($workDataFormat as &$item) {
            $taxOut_key = array_search('priceTaxOut', array_keys($item));
            $priceTaxIn = floor((int)$item['priceTaxOut'] * ProductRepository::TAX_RATE);
            $item = array_slice($item, 0, $taxOut_key + 1, true) +
                ['priceTaxIn' => (string)($priceTaxIn == 0 ? '' : $priceTaxIn)] +
                array_slice($item, $taxOut_key + 1, count($item) - 1, true);
        }
        // END 109341

        $response = [
            'hasNext' => false,
            'totalCount' => count($workDataFormat),
            'rows' => $workDataFormat
        ];
        return response()->json($response);
    });

    // タグ作品取得
    $router->get('/work/tag/{thousandTag}', function (Request $request, $thousandTag) {
        $workRepository = new WorkRepository();
        $workRepository->setLimit($request->input('limit', 10));
        $workRepository->setOffset($request->input('offset', 0));
        $workRepository->setSaleType($request->input('saleType', $workRepository::SALE_TYPE_RENTAL));

        $premiumFlag = $request->input('premium', false);

        // Get work data
        $workData = $workRepository->getWorkListByThousandTag($thousandTag);

        if (empty($workData)) {
            throw new NoContentsException;
        }
        // Format output work data
        $workDataFormat = $workRepository->formatOutputThousandTag($workData);

        // プレミアムフラグを返却しないようにする。
        // 現状はDVDレンタルの為、臨時対応として取得元で制御は行わない
        if ($premiumFlag !== 'true') {
            foreach ($workDataFormat as $rowKey => $row) {
                unset($workDataFormat[$rowKey]['isPremium']);
                unset($workDataFormat[$rowKey]['isPremiumNet']);
                unset($workDataFormat[$rowKey]['allPremiumNet']);
                unset($workDataFormat[$rowKey]['premiumNetStatus']);
            }
        }


        $tagInfo = $workRepository->convertTagToName((array) $thousandTag);

        $response = [
            'hasNext' => $workRepository->getHasNext(),
            'totalCount' => $workRepository->getTotalCount(),
            'tag' => $tagInfo[0]->tag,
            'tagTitle' => $tagInfo[0]->tagTitle,
            'tagMessage' => $tagInfo[0]->tagMessage,
            'rows' => $workDataFormat
        ];

        return response()->json($response);
    });

    // タグ名変換
    $router->get('/convert/tags', function (Request $request) {
        $arrTags = $request->input('tags', null);
        if (empty($arrTags)) {
            throw new BadRequestHttpException;
        }
        $workRepository = new WorkRepository();
        $results = $workRepository->convertTagToName($arrTags);
        if (empty($results)) {
            throw new NoContentsException;
        }

        $response = [
            'rows' => $results
        ];
        return response()->json($response);
    });

    // Favorite add
    $router->post('favorite/add', function (Request $request) {
        $bodyObj = json_decode($request->getContent(), true);
        $tlsc = isset($bodyObj['tlsc']) ? $bodyObj['tlsc'] : '';
        $id = isset($bodyObj['id']) ? $bodyObj['id'] : '';
        // Check tlsc and $workId
        if (empty($tlsc) || empty($id)) {
            throw new BadRequestHttpException;
        }
        $favoriteRepository = new FavoriteRepository();
        $favoriteRepository->setTlsc($bodyObj['tlsc']);
        $response = $favoriteRepository->add($id);
        if ($response === false) {
            throw new BadRequestHttpException('該当の作品が存在しない為、処理できませんでした。');
        }
        // Other error
        if ($response['status'] == 'error') {
            $addFvrString = '{
                "status": "error",
                "message": "登録上限に達しています。"
            }';
            $response = json_decode($addFvrString);
            return response()->json($response);
        }
        return response()->json($response);
    });

    // Favorite merge
    $router->post('favorite/add/merge', function (Request $request) {
        $bodyObj = json_decode($request->getContent(), true);
        $tlsc = isset($bodyObj['tlsc']) ? $bodyObj['tlsc'] : '';
        $ids = isset($bodyObj['ids']) ? $bodyObj['ids'] : '';
        // Check tlsc and $workId
        if (empty($tlsc) || empty(count($ids))) {
            throw new BadRequestHttpException;
        }
        $favoriteRepository = new FavoriteRepository();
        $favoriteRepository->setTlsc($bodyObj['tlsc']);
        $response = $favoriteRepository->merge($ids);
        if ($response === false) {
            throw new BadRequestHttpException('該当の作品が存在しない為、処理できませんでした。');
        }
        // Limit error
        if ($response['status'] == 'error') {
            throw new Exception;
        }

        // アプリで利用しない為versionの返却はしないように変更
        // 8.3でAndroidが利用しているため、APIで制御をかける
        $response['version'] = '';

        return response()->json($response);
    });

    // Favorite delete
    $router->post('favorite/delete', function (Request $request) {
        $bodyObj = json_decode($request->getContent(), true);
        $tlsc = isset($bodyObj['tlsc']) ? $bodyObj['tlsc'] : '';
        $ids = isset($bodyObj['ids']) ? $bodyObj['ids'] : '';
        // Check tlsc and $workId
        if (empty($tlsc) || empty(count($ids))) {
            throw new BadRequestHttpException;
        }
        $favoriteRepository = new FavoriteRepository();
        $favoriteRepository->setTlsc($bodyObj['tlsc']);
        $response = $favoriteRepository->delete($ids);
        if ($response === false) {
            throw new BadRequestHttpException('該当の作品が存在しない為、処理できませんでした。');
        }
        if ($response['status'] == 'error') {
            throw new Exception;
        }
        return response()->json($response);
    });

    // Coupon list
    $router->post('coupon/list', function (Request $request) {
        $bodyObj = json_decode($request->getContent(), true);
        $storeCds = isset($bodyObj['storeCds']) ? $bodyObj['storeCds'] : '';
        if (empty($storeCds)) {
            throw new BadRequestHttpException;
        }
        $couponRepository = new CouponRepository();
        $couponRepository->setStoreCds($storeCds);

        $rows = $couponRepository->get();
        if (empty($rows)) {
            throw new NoContentsException;
        }
        $response = [
            'requestDate' => date('YmdHis'),
            'rows' => $rows
        ];
        return response()->json($response)->header('X-Accel-Expires', '0');
    });

    // メンバー利用登録　
    $router->post('member/status/rental', function (Request $request) {
        $bodyObj = json_decode($request->getContent(), true);
        $tolId = isset($bodyObj['tolId']) ? $bodyObj['tolId'] : '';
        if (empty($tolId)) {
            throw new BadRequestHttpException;
        }
        $rentalUseRegistrationRepository = new RentalUseRegistrationRepository($tolId);
        $result = $rentalUseRegistrationRepository->get();
        if (empty($result)) {
            throw new NoContentsException;
        }
        Log::info("Response itemNumber[" . $result['itemNumber'] . "] rentalExpirationDate[" . $result['rentalExpirationDate'] . "]");
        $response = [
            'itemNumber' => $result['itemNumber'],
            'rentalExpirationDate' => $result['rentalExpirationDate']
        ];

        return response()->json($response)->header('X-Accel-Expires', '0');
    });


    // 期間固定Tポイント
    $router->post('member/tpoint', function (Request $request) {
        $bodyObj = json_decode($request->getContent(), true);
        $tolId = isset($bodyObj['tolId']) ? $bodyObj['tolId'] : '';
        $systemId = isset($bodyObj['systemId']) ? $bodyObj['systemId'] : '';
        $refreshFlg = isset($bodyObj['refreshFlg']) ? $bodyObj['refreshFlg'] : false;
        if (empty($tolId) || empty($systemId)) {
            throw new BadRequestHttpException;
        }
        $pointRepository = new PointRepository($systemId, $tolId, $refreshFlg);
        $point = $pointRepository->get();
        if ($point === false) {
            throw new NoContentsException;
        }
        $response = [
            'responseCode' => $pointRepository->getResponseCode(),
            'membershipType' => $pointRepository->getMembershipType(),
            'point' => $pointRepository->getPoint(),
            'fixedPointTotal' => $pointRepository->getFixedPointTotal(),
            'fixedPointMinLimitTime' => $pointRepository->getFixedPointMinLimitTime(),
        ];
        return response()->json($response)->header('X-Accel-Expires', '0');
    });

    // 　プレミアム会員状態取得API
    $router->post('member/status/premium', function (Request $request) {
        $bodyObj = json_decode($request->getContent(), true);
        $tolId = isset($bodyObj['tolId']) ? $bodyObj['tolId'] : '';
        $statusPremium = new StatusPremiumRepository($tolId);

        $response = [
            'premium' => $statusPremium->get()
        ];
        return response()->json($response)->header('X-Accel-Expires', '0');
    });

    // 　プレミアム会員状態取得API
    $router->post('member/status/ttv', function (Request $request) {
        $bodyObj = json_decode($request->getContent(), true);
        $tlsc = isset($bodyObj['tlsc']) ? $bodyObj['tlsc'] : '';
        // Check tlsc and $workId
        if (empty($tlsc)) {
            throw new BadRequestHttpException;
        }

        $discasRepository = new DiscasRepository();
        try {
            $response = $discasRepository->customer($tlsc)->get();
            $response = [
                'httpcode' => '200',
                'ttvId' => $response['ttvId'],
                'tenpoCode' => $response['tenpoCode'],
                'tenpoName' => $response['tenpoName'],
                'tenpoPlanFee' => (int)$response['tenpoPlanFee'],
                'nextUpdateDate' => date("Y-m-d h:i:s", strtotime($response['nextUpdateDate']))
            ];
            //response中に特定店舗があった場合、店舗コードを空にする
            if ($response['tenpoCode'] === '8811' || $response['tenpoCode'] === '8813') {
                $response['tenpoCode'] = '';
            }
        } catch (\Exception $e) {
            $exceptionResponse = $e->getResponse();
            $statusCode = $exceptionResponse->getStatusCode();
            $errorCode = json_decode($exceptionResponse->getBody()->getContents(), true);
            $response = [
                'httpcode' => (string)$statusCode,
                'status' => $errorCode['error']
            ];
        }
        return response()->json($response)->header('X-Accel-Expires', '0');
    });

    // 「予約・注文・定期購読 商品入荷連絡 登録状況」取得　
    $router->post('/member/status/arrival/notification', function (Request $request) {
        $bodyObj = json_decode($request->getContent(), true);
        $tolId = isset($bodyObj['tolId']) ? $bodyObj['tolId'] : '';
        if (empty($tolId)) {
            throw new BadRequestHttpException;
        }

        $getNotificationRepository = new NotificationRepository($tolId);
        $result = $getNotificationRepository->getNotificationStatus();
        if (empty($result)) {
            throw new NoContentsException;
        }

        $response = [
            'results' => (array)$result
        ];

        $status = isset($response['results']['status']) ? $response['results']['status'] : 'ERROR';
        if ($status === 'ERROR') {
            throw new NoContentsException;
        }

        // 返却項目名の変更、値をbooleanへ変更
        $response['results']['isRegistered'] = $response['results']['registerStatus'] === '1';
        unset($response['results']['registerStatus']);

        return response()->json($response)->header('X-Accel-Expires', '0');
    });

    // 「予約・注文・定期購読 商品入荷連絡 登録状況」更新
    $router->post('member/status/arrival/notification/update', function (Request $request) {
        $bodyObj = json_decode($request->getContent(), true);
        $tolId = isset($bodyObj['tolId']) ? $bodyObj['tolId'] : '';

        if (isset($bodyObj['isRegistered']) && $bodyObj['isRegistered']) {
            $isRegistered = 1;
        } else {
            $isRegistered = 0;
        }

        // Check tolId
        if (empty($tolId)) {
            throw new BadRequestHttpException;
        }

        $getNotificationRepository = new NotificationRepository($tolId);
        $result = $getNotificationRepository->updateNotification($isRegistered);

        if (empty($result)) {
            throw new NoContentsException;
        }

        $response = [
            'results' => (array)$result
        ];

        $status = isset($response['results']['status']) ? $response['results']['status'] : 'ERROR';
        if ($status === 'ERROR') {
            throw new NoContentsException;
        }

        // 返却項目名の変更、値をbooleanへ変更
        $response['results']['isRegistered'] = $response['results']['registerStatus'] === '1';
        unset($response['results']['registerStatus']);

        return response()->json($response)->header('X-Accel-Expires', '0');
    });

    $router->post('member/hasDisc/premium/rental', function (Request $request) {
        $bodyObj = json_decode($request->getContent(), true);
        $tlsc = isset($bodyObj['tlsc']) ? $bodyObj['tlsc'] : '';
        // Check tlsc and $workId
        if (empty($tlsc)) {
            throw new BadRequestHttpException;
        }
        $discasRepository = new DiscasRepository();
        try {
            $response = [
                'httpcode' => '200',
                'rows' => []
            ];
            $apiData = $discasRepository->customerRental($tlsc)->get();
            // Process data response from API
            if (isset($apiData['hasDiscList'])) {
                foreach ($apiData['hasDiscList'] as $item) {
                    $response['rows'][] = [
                        'storeName' => $item['tenpoName'],
                        'rentCnt' => $item['discNum']
                    ];
                }
            }
        } catch (\Exception $e) {
            $exceptionResponse = $e->getResponse();
            $statusCode = $exceptionResponse->getStatusCode();
            $errorCode = json_decode($exceptionResponse->getBody()->getContents(), true);
            $response = [
                'httpcode' => (string)$statusCode,
                'status' => $errorCode['error']
            ];
        }
        return response()->json($response)->header('X-Accel-Expires', '0');
    });

    $router->post('member/premium/authKey', function (Request $request) {
        $bodyObj = json_decode($request->getContent(), true);
        $tolId = isset($bodyObj['tolId']) ? $bodyObj['tolId'] : '';

        $statusPremium = new StatusPremiumRepository($tolId);
        $tInternalNumber = $statusPremium->pre_get();

        $mintRepository = new MintRepository();
        try {
            $response = $mintRepository->authKey($tInternalNumber);
            $response = [
                'authKey' => $response['ResultList'][0][0]
            ];
        } catch (\Exception $e) {
            throw new NoContentsException;
        }

        return response()->json($response)->header('X-Accel-Expires', '86400');
    });

    // キャンペーン情報
    $router->get('/promotion/{promotion_id}', function (Request $request, $promotion_id) {
        $promotionRepository = new PromotionRepository();
        $result = $promotionRepository->getPromotionData($promotion_id);
        
        if (empty($result)) {
            throw new NoContentsException;
        }
        $response = $result;
        return response()->json($response)->header('X-Accel-Expires', '86400');
    });

    // キャンペーン応募
    $router->post('promotion/entry', function (Request $request) {
        $bodyObj = json_decode($request->getContent(), true);
        $tolId = isset($bodyObj['tolid']) ? $bodyObj['tolid'] : '';
        $promotionId = isset($bodyObj['promotionId']) ? $bodyObj['promotionId'] : '';
        $prizeNo = isset($bodyObj['prizeNo']) ? $bodyObj['prizeNo'] : '';
        $ques = isset($bodyObj['ques']) ? $bodyObj['ques'] : '';

        $promotionRepository = new PromotionRepository();
        $result = $promotionRepository->registPromotion($tolId, $promotionId, $prizeNo, $ques);
        $result = isset($result->returnCode) && current($result->returnCode) === '00';
        $response = [
            'result' => true
        ];

        return response()->json($response)->header('X-Accel-Expires', '600');
    });

    // キャンペーン応募回数
    $router->post('promotion/entry/check', function (Request $request) {
        $bodyObj = json_decode($request->getContent(), true);
        $tolId = isset($bodyObj['tolid']) ? $bodyObj['tolid'] : '';
        $promotionId = isset($bodyObj['promotionId']) ? $bodyObj['promotionId'] : '';

        $promotionRepository = new PromotionRepository();
        $result = (array)$promotionRepository->getPromotionStatus($tolId, $promotionId);

        if ($result['entryCount'] === '0') {
            throw new NoContentsException;
        }

        $response = [
            'count' => $result['entryCount']
        ];

        return response()->json($response)->header('X-Accel-Expires', '600');
    });

    // 検証環境まで有効にするテスト用
    if (env('APP_ENV') === 'local' || env('APP_ENV') === 'develop' || env('APP_ENV') === 'staging') {
        $router->get('himo/{workId}', function (Request $request, $workId) {
            $himo = new HimoRepository();
            $response = $himo->crosswork([$workId])->get();
            return response()->json($response)->header('X-Accel-Expires', '0');
        });

    }

    // 119598 maintenance
    $router->get("system/maintenance", function (Request $request) {
        $maintenance = new \App\Repositories\MaintenanceRepository();
        $content = $maintenance->loadMaintenanceData();
        if (empty($content)) {
            throw new NoContentsException;
        }

        return response()->json($content)->header('X-Accel-Expires', '0');
    });
});

$router->group(['prefix' => env('URL_PATH_PREFIX') . env('API_VERSION')], function () use ($router) {
    // APIドキュメント
    $router->get('docs/swagger.json', function () {
        $swagger = \Swagger\scan(base_path('routes'));
        return response()->json($swagger)->header('X-Accel-Expires', '0');
    });
});
