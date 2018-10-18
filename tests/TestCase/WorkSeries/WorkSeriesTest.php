<?php

use tests\TestData;

/*
 * Work（作品情報取得） APIテスト
 *
 */

class WorkSeriesTest extends TestCase
{
    public function __construct(string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->testDir = __DIR__;
    }

    /*
     * 販売種別テスト用テストケース
     * タイトルは作品
     * 画像とその他は取得する商品情報
     *
     * use tap_v2;
     * SELECT * FROM ts_products where work_id = 'PTATESTBK01'
     *   AND product_type_id = 1
     * order by sale_start_date desc, ccc_family_cd desc, item_cd asc, ccc_product_id asc;
     *
     */
    public function workJacketDataProvider()
    {
        return [
            'DVDレンタル　単巻 DVD優先' => ['PTATESTVI011', 'rental',
                'ビデオ単品（Productには複数あり）作品タイトル',
                'https://cdn.video_test_single_product_02_size_l.jpg',
                '2017-11-06 00:00:00'
            ],
            'DVDレンタル　複数巻 DVD優先' => ['PTATESTVI012', 'rental',
                'キングダム',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/08599/4988064579570_1L.jpg',
                '2013-05-03 00:00:00'
            ],
            'CDレンタル　単巻' => ['PTATESTAU011', 'rental',
                'Flavor Of Life',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/00400/4988006210073_1L.jpg',
                '2007-02-28 00:00:00'
            ],
            'CDレンタル　複数巻' => ['PTATESTAU012', 'rental',
                '11月のアンクレット',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/12468/4988003514310_1L.jpg',
                '2017-11-22 00:00:00'
            ],
            'BOOKレンタル　単巻' => ['PTATESTBK011', 'rental',
                'レンタルBOOK単品（Productに複数刊あり）作品タイトル',
                'https://cdn.book_test_single_product_02_size_l.jpg',
                '2018-09-20 00:00:00'
            ],
            'BOOKレンタル　複数巻' => ['PTATESTBK012', 'rental',
                '進撃の巨人',
                '',
                '2018-04-09 00:00:00'
            ],
            'DVDセル　単巻' => ['PTATESTVI021', 'sell',
                'ビデオ単品（Productには複数あり）作品タイトル',
                'https://cdn.video_test_single_product_04_size_l.jpg',
                '2017-11-06 00:00:00'
            ],
            'DVDセル　複数巻' => ['PTATESTVI022', 'sell',
                'キングダム',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/08764/4988064624461_1L.jpg',
                '2013-06-28 00:00:00'
            ],
            'CDセル　単巻' => ['PTATESTAU021', 'sell',
                'Flavor Of Life',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/00400/4988006210073_1L.jpg',
                '2007-02-28 00:00:00'
            ],
            'CDセル　複数巻' => ['PTATESTAU022', 'sell',
                '11月のアンクレット',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/12468/4988003514365_1L.jpg',
                '2017-11-22 00:00:00'
            ],
            'BOOKセル 単巻' => ['PTATESTBK021', 'sell',
                'レンタルBOOK単品（Productに複数刊あり）作品タイトル',
                'https://cdn.book_test_single_product_04_size_l.jpg',
                '2018-09-20 00:00:00'
            ],
            'BOOKセル 複数巻' => ['PTATESTBK022', 'sell',
                '進撃の巨人',
                '',
                '2018-08-09 00:00:00'
            ],
            'GAMEセル 単巻' => ['PTATESTGM021', 'sell',
                '妖怪ウォッチバスターズ 白犬隊',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/10388/4571237660672_1L.jpg',
                '2015-07-11 00:00:00'
            ],
            'GAMEセル 複数巻' => ['PTATESTGM022', 'sell',
                'モンスターハンター:ワールド',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/13235/4976219095624_1L.jpg',
                '2018-08-02 00:00:00'
            ],
        ];
    }

    /**
     * @test
     * @dataProvider workJacketDataProvider
     */
    public function ジャケ写チェック($workId, $saleType, $expectedTitle, $expectedImage, $expectedStartDate)
    {
        $url = '/work/' . $workId . '/series?saleType=' . $saleType;
        $response = $this->getJsonWithAuth($url);
        $response = $this->getWithAuth($url);
        $actual = json_decode($response->getContent(), true);
        $this->assertEquals($expectedTitle,     $actual['rows'][0]['workTitle']);
        $this->assertEquals($expectedImage,     $actual['rows'][0]['jacketL']);
    }


}