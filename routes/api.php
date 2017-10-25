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

$router->get('/', function () use ($router) {
    return $router->app->version();
});


// Api Group
$router->group(['namespace' => 'Api'], function() use ($router) {

    // 固定コンテンツ取得API
    $router->get('version', function () {
        $varsion =
            [
            'appinfo' => [
                'latestVersion' => '7.5.0',     // 最新バージョン
                'lowestVersion' => '7.4.3',   // アップデートなしでサポートする最低バージョン
                'alert' => '7.5.0がリリースされました。現在お使いのバージョンからアップデートをお願いします。', // 更新必須の場合のアラート
                'nugde' =>  '新バージョンがリリースされました。アップデートをお願いします。',  // アップデートを促すメッセージ
                'infomation' => ['7.5.0の新機能","https://store-tsutaya.tsite.jp/appinfo/whatsnewinthisversion.html'] // アプリ新機能紹介ページ
              ],
              'update' => '2017/10/04 10:00:00',  //更新日時。単なる文字列扱いでもいいかなと思います。
              'version' => '1507115185'            // API出力時にバージョン制御のシリアル値を出力した方がいいかもしれない。
            ];
        return response()->json($varsion);
    });

    // 固定コンテンツ取得API
    $router->get('fixed/banner', function () {
        return 'this is fixed banner';
    });

    // コンテンツ構成取得API
    $router->get('structure/{goodsName:[A-Za-z]+}/{typeName:[A-Za-z]+}', function ($goodsName, $typeName) {

//        $structureData = $structure->get($goodsName, $typeName);
        $structureData =
            [
                'totalCount' => 6,
                'limit' => 10,
                'offset' => 0,
                'page' => 1,
                'hasNext' => true,
                'rows' =>
                    [
                        [
                            'sectionId' => 1,
                            'sectionType' => 1,
                            'startDate' => '2017-01-01',
                            'endDate' => '2017-01-01',
                            'image' => [
                                'height' => 130,
                                'width' => 600
                            ],
                            'apiUrl' => '/section/banner/banner_section_1'
                        ],
                        [
                            'sectionId' => 2,
                            'sectionType' => 2,
                            'startDate' => '2017-01-01',
                            'endDate' => '2017-01-01',
                            'title' => '最新のものをチェック！',
                            'linkUrl' => 'https://tsutaya.jp/a.html',
                            'isTapOn' => false,
                            'isRanking' => false,
                            'apiUrl' => '/section/dvd/rental/section_name_1'
                        ],
                        [
                            'sectionId' => 3,
                            'sectionType' => 2,
                            'startDate' => '2017-01-01',
                            'endDate' => '2017-01-01',
                            'title' => '話題作をチェック！',
                            'linkUrl' => 'https://tsutaya.jp/b.html',
                            'isTapOn' => true,
                            'isRanking' => false,
                            'api_url' => '/section/dvd/rental/section_name_2'
                        ],
                        [
                            'sectionId' => 4,
                            'sectionType' => 2,
                            'startDate' => '2017-01-01',
                            'endDate' => '2017-01-01',
                            'title' => '今週の人気ランキング！',
                            'linkUrl' => 'tsutayaapp://ranking/aaaaaa',
                            'isTapOn' => false,
                            'isRanking' => true,
                            'apiUrl' => '/section/dvd/rental/ranking'
                        ],
                        [
                            'sectionId' => 5,
                            'sectionType' => 3
                        ],
                        [
                            'sectionId' => 6,
                            'sectionType' => 4
                        ],
                        [
                            'sectionId' => 7,
                            'sectionType' => 5 // PDMPレコメンドエンジン経由出力
                        ]
                    ]
            ];

        return response()->json($structureData);
    });


    // ランキングセクション取得API
    $router->get('section/{goodsName:[A-Za-z]+}/{typeName:[A-Za-z]+}/ranking/{rankingCategoryId:[A-Za-z]+}', function ($goodsName, $typeName, $sectionName) {

//        $sectionData = $section->get($goodsName, $typeName, $sectionName);
        $sectionData = [
            'totalCount' => 10,
            'limit' => 10,
            'offset' => 0,
            'page' => 1,
            'rows' => [
                [
                    'title' => 'Exsample Title ',
                    'startDate'=> '2017-01-01',
                    'endDate'=> '2017-01-01',
                    'imageUrl'=> 'https://tsutaya.jp/image/a.jpg',
                    'supplement' => ''
                ]
            ]
        ];

        return response()->json($sectionData);

    });

    // 通常セクション取得API
    $router->get('section/{goodsName:[A-Za-z]+}/{typeName:[A-Za-z]+}/{sectionName:[A-Za-z]+}', function ($goodsName, $typeName, $sectionName) {
        return 'section banner';

    });

    // バナーセクション取得API
    $router->get('section/banner/{sectionName:[A-Za-z]+}', function ($sectionName) {
        return 'section banner';

    });

    // レコメンドセクション取得API
    $router->get('section/recommend/ranking/{himoGenreId:[A-Za-z]+}', function ($himoGenreId) {

        $sectionRepository = new SectionRepository;
        $sectionData = $sectionRepository->ranking($goodsName, $typeName, $sectionName);
        return $sectionData;
        // TWS APIと利用。himoGenreIdを変換する、classを作成する。
    });
});
