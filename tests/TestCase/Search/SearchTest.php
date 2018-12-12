<?php

class SearchTest extends TestCase
{
    /*
     * 販売種別テスト用テストケース
     * タイトルは作品
     * 画像とその他は取得する商品情報
     */
    public function workJacketDataProvider()
    {
        // ジャンルのデータをそのままつかっているので、キーワードにジャンルデータのファイル名を渡す
        return [
            'DVDレンタル　単巻 DVD優先' => [
                'EXTTEST00001',
                'ビデオ単品（Productには複数あり）作品タイトル',
                'https://cdn.video_test_single_product_02_size_l.jpg',
                '2017-11-06 00:00:00',
                '2017-11-06 00:00:00'
            ],
            'DVDレンタル　複数巻 DVD優先' => [
                'EXTTEST00002',
                'キングダム',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/08599/4988064579570_1L.jpg',
                '2014-03-28 00:00:00',
                '2013-05-03 00:00:00'
            ],
            'CDレンタル　単巻' => [
                'EXTTEST00003',
                'Flavor Of Life',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/00400/4988006210073_1L.jpg',
                '',
                '2007-02-28 00:00:00'
            ],
            'CDレンタル　複数巻' => [
                'EXTTEST00004',
                '11月のアンクレット',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/12468/4988003514310_1L.jpg',
                '',
                '2017-11-22 00:00:00'
            ],
            'BOOKレンタル　単巻' => [
                'EXTTEST00005',
                'レンタルBOOK単品（Productに複数刊あり）作品タイトル',
                'https://cdn.book_test_single_product_02_size_l.jpg',
                '',
                '2018-09-20 00:00:00'
            ],
            'BOOKレンタル　複数巻' => [
                'EXTTEST00006',
                '進撃の巨人',
                '',
                '',
                '2018-04-09 00:00:00'
            ],
            'DVDセル　単巻' => [
                'EXTTEST00007',
                'ビデオ単品（Productには複数あり）作品タイトル',
                'https://cdn.video_test_single_product_04_size_l.jpg',
                '2017-11-06 00:00:00',
                ''
            ],
            'DVDセル　複数巻' => [
                'EXTTEST00008',
                'キングダム',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/08764/4988064624461_1L.jpg',
                '2014-03-28 00:00:00',
                ''
            ],
            'CDセル　単巻' => [
                'EXTTEST00009',
                'Flavor Of Life',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/00400/4988006210073_1L.jpg',
                '2007-02-28 00:00:00',
                ''
            ],
            'CDセル　複数巻' => [
                'EXTTEST00010',
                '11月のアンクレット',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/12468/4988003514310_1L.jpg',
                '2017-11-22 00:00:00',
                '2017-11-22 00:00:00'
            ],
            'BOOKセル 単巻' => [
                'EXTTEST00011',
                'レンタルBOOK単品（Productに複数刊あり）作品タイトル',
                'https://cdn.book_test_single_product_04_size_l.jpg',
                '2018-09-20 00:00:00',
                ''
            ],
            'BOOKセル 複数巻 セル優先最新刊' => [
                'EXTTEST00012',
                '進撃の巨人',
                'shingeki_26',
                '2018-08-09 00:00:00',
                '2018-04-09 00:00:00'
            ],
            'GAMEセル 単巻' => [
                'EXTTEST00013',
                '妖怪ウォッチバスターズ 白犬隊',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/10388/4571237660672_1L.jpg',
                '2015-07-11 00:00:00',
                ''
            ],
            'GAMEセル 複数巻' => [
                'EXTTEST00014',
                'モンスターハンター:ワールド',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/12737/4976219089999_1L.jpg',
                '2018-01-26 00:00:00',
                ''
            ],
            '上映映画' => [
                'EXTTEST00015',
                'ザ・プレデター',
                'https://cdn.store-tsutaya.tsite.jp/images/extdata/04/00/00/36/45/01/364528_02_01.jpg',
                '',
                ''
            ]
        ];
    }

    /**
     * @test
     * @dataProvider workJacketDataProvider
     */
    public function ジャケ写チェック(
        $keyword, $expectedTitle, $expectedImage, $expectedSaleStartDateSell, $expectedSaleStartDateRental
    )
    {
        $url = '/search/' . $keyword;
        $response = $this->getWithAuth($url);
        $actual = json_decode($response->getContent(), true);
        $this->assertEquals($expectedTitle,     $actual['rows'][0]['workTitle']);
        $this->assertEquals($expectedImage,     $actual['rows'][0]['jacketL']);
        $this->assertEquals($expectedSaleStartDateSell, $actual['rows'][0]['saleStartDateSell']);
        $this->assertEquals($expectedSaleStartDateRental, $actual['rows'][0]['saleStartDateRental']);
    }
}