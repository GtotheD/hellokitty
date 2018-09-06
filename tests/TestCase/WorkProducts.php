<?php

use tests\TestData;

class WorkProducts extends TestCase
{
    private $apiPath;

    public function setUp()
    {
        parent::setUp();
        $this->baseUrl = env('APP_URL') . '/' . env('URL_PATH_PREFIX') . env('API_VERSION');
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        $bk2Seeder = new TestDataBk2RecommendsSeeder;
        $bk2Seeder->run();
        $keywordSeeder = new TestDataKeywordSuggestSeeder();
        $keywordSeeder->run();
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        $path = base_path('tests/himo/crossworks/');
        $audioList = glob($path . '/audio/*');
        $videoList = glob($path . '/video/*');
        $bookList = glob($path . '/book/*');
        $gameList = glob($path . '/game/*');
        $list = array_merge($audioList, $videoList, $bookList, $gameList);
        foreach ($list as $row) {
            $workIds[] = [
                basename($row),
                ''
            ];
        }
        return $workIds;
    }

    /**
     * All
     * @dataProvider dataProvider
     * @test
     */
    public function workProduct($workId, $expected)
    {
        $url = '/work/' . $workId;
        $this->getJsonWithAuth($url);
        $response = $this->getJsonWithAuth('/work/' . $workId . '/products');
        $response->assertResponseStatus(200, 204);
    }

    /**
     * Rental
     * @dataProvider dataProvider
     * @test
     */
    public function workProductRental($workId, $expected)
    {
        $url = '/work/' . $workId;
        $this->getJsonWithAuth($url);
        $response = $this->getJsonWithAuth('/work/' . $workId . '/products?saleType=rental');
        $response->assertResponseStatus(200, 204);
    }

//    /**
//     * Sell
//     * @dataProvider dataProvider
//     * @test
//     */
//    public function workProductSell($workId, $expected)
//    {
//        $url = '/work/' . $workId;
//        $this->getJsonWithAuth($url);
//        $response = $this->getJsonWithAuth('/work/' . $workId . '/products?saleType=sell');
//        $response->assertResponseStatus(200, 204);
//    }

    /**
     * @test
     */
    public function レンタル商品のみ()
    {
        // expected レンタル商品の出力
    }

    /**
     * @test
     */
    public function レンタル商品のみ_レンタル指定()
    {
        // expected レンタル商品の出力
    }

    /**
     * @test
     */
    public function レンタル商品のみ_販売指定()
    {
        // expected レンタル商品の出力
    }

    /**
     * @test
     */
    public function PPTを含むレンタル商品のみ()
    {
        // expected PPTを集約した商品の出力
    }

    /**
     * @test
     */
    public function セル商品のみ()
    {
        // expected セル商品の出力
    }

    /**
     * @test
     */
    public function 販売商品のみ_レンタル指定()
    {
        // expected 204の返却
    }

    /**
     * @test
     */
    public function 販売商品のみ_販売指定()
    {
        // expected 商品の出力
    }

    /**
     * @test
     */
    public function レンタルPPTのみ商品()
    {
        // expected PPT商品の出力
    }

    /**
     * @test
     */
    public function レンタルPPTのみ商品_販売指定()
    {
        // expected 出力なし
    }

    /**
     * @test
     */
    public function レンタルVHSのみ商品()
    {
        // expected VHSの出力
    }

    /**
     * @test
     */
    public function レンタルVHSでVHS以外の特殊媒体のみ商品()
    {
        // expected VHS以外の特殊媒体の出力
    }

    /**
     * @test
     */
    public function レンタルVHS複数商品がある場合()
    {
        // expected 最新のVHSの出力
    }

    /**
     * @test
     */
    public function セルVHSのみ商品()
    {
        // expected VHSの出力
    }

    /**
     * @test
     */
    public function セルVHSでVHS以外の特殊媒体のみ商品()
    {
        // expected VHS以外の特殊媒体の出力
    }

    /**
     * @test
     */
    public function セルVHS複数商品がある場合()
    {
        // expected 最新のVHSの出力
    }

    /**
     * @test
     * case　VHS及びPPTを含むレンタル商品
     * expected PPT商品のみの出力でVHSは出さない
     */
    public function VHS及びPPTを含むレンタル商品()
    {
        // expected PPT商品のみの出力でVHSは出さない
    }



    /**
     * @test
     */
    public function 巻数を付与されていること()
    {
        $url = '/work/PTA0000G66F0';
        $this->getJsonWithAuth( $url);
        $response = $this->getJsonWithAuth('/work/PTA0000G66F0/products');
        $response->seeJson([
            'totalCount' => 65,
            'productName' => '進撃の巨人<限定版> DVD付き（26）',
        ]);
    }


    /**
     * @test
     */
    public function 巻数が一巻のみの場合は巻数を付与しない()
    {
        $url = '/work/PTA0000R81I8';
        $this->getJsonWithAuth( $url);
        $response = $this->getJsonWithAuth('/work/PTA0000R81I8/products');
        $response->seeJson([
                'totalCount' => 1,
                'productName' => 'こちら葛飾区亀有公園前派出所 ∞巻<特装版>',
            ]);
    }
//
//    /**
//     * VHSかつダミーデータ(JANが9999始まり)のもの場合出力しない。
//     * @test
//     */
//    public function workProductIgnoreVHS()
//    {
//        $url = '/work/PTA00008M81I';
//        $this->getJsonWithAuth( $url);
//        $response = $this->getJsonWithAuth('/work/PTA00008M81I/products?saleType=rental');
//        $response->assertResponseStatus(204);
//    }
//
//    /**
//     *　セルのみの商品
//     * @test
//     */
//    public function workProductIgnoreVHSDisplayOtherData()
//    {
//        $url = '/work/PTA00008M81I';
//        $this->getJsonWithAuth( $url);
//        $response = $this->getJsonWithAuth('/work/PTA00008M81I/products?saleType=sell');
//        $response->seeJson([
//            'totalCount' => 6,
//            'productName' => '鉄人タイガーセブン 5',
//        ]);
//    }
}