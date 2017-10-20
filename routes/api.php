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

$router->get('/', function () use ($router) {
    return $router->app->version();
});


// Api Group
$router->group(['namespace' => 'Api'], function() use ($router) {

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

//        $sectionData = $section->get($goodsName, $typeName, $sectionName);
        $sectionData = [
            'totalCount' => 10,
            'limit' => 10,
            'offset' => 0,
            'page' => 1,
            'hasNext' => true,
            'rows' => [
                [
                    'dispStartDate'=> '2017-01-01',
                    'dispEndDate'=> '2017-01-01',
                    'saleStartDate'=> '2017-01-01',
                    'rentalStartDate'=> '2017-01-01',
                    'imageUrl'=> 'https://tsutaya.jp/image/a.jpg',
                    'title' => 'ラ・ラ・ランド',
                    'supplement' => 'エマ・ストーン', // アーティスト名、著者、機種等
                    'code' => 'JAN_CODE',
                    'urlCode' => 'url code',
                    'rate' => 2
                ],
                [
                    'dispStartDate'=> '2017-01-01',
                    'dispEndDate'=> '2017-01-01',
                    'saleStartDate'=> '2017-01-01',
                    'rentalStartDate'=> '2017-01-01',
                    'imageUrl'=> 'https://tsutaya.jp/image/a.jpg',
                    'title' => 'ワイルド・スピード　ＩＣＥ　ＢＲＥＡＫ',
                    'supplement' => 'ヴィン・ディーゼル', // アーティスト名、著者、機種等
                    'code' => 'JAN_CODE',
                    'urlCode' => 'url code',
                    'rate' => 3
                ],
                [
                    'dispStartDate'=> '2017-01-01',
                    'dispEndDate'=> '2017-01-01',
                    'saleStartDate'=> '2017-01-01',
                    'rentalStartDate'=> '2017-01-01',
                    'imageUrl'=> 'https://tsutaya.jp/image/a.jpg',
                    'title' => '美女と野獣',
                    'supplement' => 'エマ・ワトソン', // アーティスト名、著者、機種等
                    'code' => 'JAN_CODE',
                    'urlCode' => 'url code',
                    'rate' => 4
                ],

            ]
        ];

        return response()->json($sectionData);

    });

    // バナーセクション取得API
    $router->get('section/banner/{sectionName:[A-Za-z]+}', function ($sectionName) {
        return 'section banner';

    });

    // レコメンドセクション取得API
    $router->get('section/recommend/{himoGenreId:[A-Za-z]+}', function (himoGenreId) {
        return 'section banner';
        // TWS APIと利用。himoGenreIdを変換する、classを作成する。
    });
});
