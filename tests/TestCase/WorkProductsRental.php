<?php

use tests\TestData;

class WorkProducts extends TestCase
{
    private $apiPath;

    public function setUp()
    {
        parent::setUp();
        $this->baseUrl = env('APP_URL').'/'.env('URL_PATH_PREFIX').env('API_VERSION');
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
        $audioList = glob($path.'/audio/*');
        $videoList = glob($path.'/video/*');
        $bookList = glob($path.'/book/*');
        $gameList = glob($path.'/game/*');
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
        $url = '/work/'.$workId;
        $this->getJsonWithAuth( $url);
        $response = $this->getJsonWithAuth('/work/'.$workId.'/products');
        $response->assertResponseStatus(200);
    }

    /**
     * Rental
     * @dataProvider dataProvider
     * @test
     */
    public function workProductRental($workId, $expected)
    {
        $url = '/work/'.$workId;
        $this->getJsonWithAuth( $url);
        $response = $this->getJsonWithAuth('/work/'.$workId.'/products?saleType=rental');
        $response->assertResponseStatus(200);
    }

    /**
     * Sell
     * @dataProvider dataProvider
     * @test
     */
    public function workProductSell($workId, $expected)
    {
        $url = '/work/'.$workId;
        $this->getJsonWithAuth( $url);
        $response = $this->getJsonWithAuth('/work/'.$workId.'/products?saleType=sell');
        $response->assertResponseStatus(200);
    }
//
//    /**
//     * Video:巻数を付与されていること
//     * @test
//     */
//    public function workProductCheckNumberOfVolumeForBook()
//    {
//        $url = '/work/PTA0000G66F0';
//        $this->getJsonWithAuth( $url);
//        $response = $this->getJsonWithAuth('/work/PTA0000G66F0/products');
//        $response->seeJson([
//            'totalCount' => 65,
//            'productName' => '進撃の巨人<限定版> DVD付き（26）',
//        ]);
//    }
//
//    /**
//     * 巻数が一巻のみの場合は巻数を付与しない
//     * @test
//     */
//    public function workProductCheckNotNumberOfVolumeForBook()
//    {
//        $url = '/work/PTA0000R81I8';
//        $this->getJsonWithAuth( $url);
//        $response = $this->getJsonWithAuth('/work/PTA0000R81I8/products');
//        $response->seeJson([
//                'totalCount' => 1,
//                'productName' => 'こちら葛飾区亀有公園前派出所 ∞巻<特装版>',
//            ]);
//    }
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