<?php

class WorkProductsHasTest extends TestCase
{
    public function workDataProvider()
    {
        return [
            ['PTA0000SF309'], // 通常DVD
            ['PTA0000WEKO0'], // 上映映画
            ['PTA0000U62N9'], // CD
            ['PTA0000GD16P'], // BOOK
            ['PTA0000U8W8U'], // GAME
        ];
    }

    /**
     * @test
     * @dataProvider workDataProvider
     */
    public function セルレンタル区分別($workId)
    {
        $url = '/work/' . $workId . '/products/has';
        $response = $this->getWithAuth($url);
        $actual = json_decode($response->getContent(), true);
        $expected = json_decode(file_get_contents(__DIR__ . '/expected/' . $workId), true);
        unset($expected['data']['createdAt']);
        unset($expected['data']['updatedAt']);
        unset($actual['data']['createdAt']);
        unset($actual['data']['updatedAt']);
        $this->assertEquals($expected, $actual);
    }

/*
 * MOVIE
 */
//    /**
//     * @test
//     */
//    public function レンタル商品のみ()
//    {
//        // expected レンタル商品の出力
//    }
//
//    /**
//     * @test
//     */
//    public function レンタル商品のみ_レンタル指定()
//    {
//        // expected レンタル商品の出力
//    }
//
//    /**
//     * @test
//     */
//    public function レンタル商品のみ_販売指定()
//    {
//        // expected レンタル商品の出力
//    }
//
//    /**
//     * @test
//     */
//    public function PPTを含むレンタル商品のみ()
//    {
//        // expected PPTを集約した商品の出力
//    }
//
//    /**
//     * @test
//     */
//    public function セル商品のみ()
//    {
//        // expected セル商品の出力
//    }
//
//    /**
//     * @test
//     */
//    public function 販売商品のみ_レンタル指定()
//    {
//        // expected 204の返却
//    }
//
//    /**
//     * @test
//     */
//    public function 販売商品のみ_販売指定()
//    {
//        // expected 商品の出力
//    }
//
//    /**
//     * @test
//     */
//    public function レンタルPPTのみ商品()
//    {
//        // expected PPT商品の出力
//    }
//
//    /**
//     * @test
//     */
//    public function レンタルPPTのみ商品_販売指定()
//    {
//        // expected 出力なし
//    }
//
//    /**
//     * @test
//     */
//    public function レンタルVHSのみ商品()
//    {
//        // expected VHSの出力
//    }
//
//    /**
//     * @test
//     */
//    public function レンタルVHSでVHS以外の特殊媒体のみ商品()
//    {
//        // expected VHS以外の特殊媒体の出力
//    }
//
//    /**
//     * @test
//     */
//    public function レンタルVHS複数商品がある場合()
//    {
//        // expected 最新のVHSの出力
//    }
//
//    /**
//     * @test
//     */
//    public function セルVHSのみ商品()
//    {
//        // expected VHSの出力
//    }
//
//    /**
//     * @test
//     */
//    public function セルVHSでVHS以外の特殊媒体のみ商品()
//    {
//        // expected VHS以外の特殊媒体の出力
//    }
//
//    /**
//     * @test
//     */
//    public function セルVHS複数商品がある場合()
//    {
//        // expected 最新のVHSの出力
//    }
//
//    /**
//     * @test
//     * case　VHS及びPPTを含むレンタル商品
//     * expected PPT商品のみの出力でVHSは出さない
//     */
//    public function VHS及びPPTを含むレンタル商品()
//    {
//        // expected PPT商品のみの出力でVHSは出さない
//    }
//
//
//
//    /**
//     * @test
//     */
//    public function 巻数を付与されていること()
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
//
//    /**
//     * @test
//     */
//    public function 巻数が一巻のみの場合は巻数を付与しない()
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