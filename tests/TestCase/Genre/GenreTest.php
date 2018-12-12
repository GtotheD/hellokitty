<?php

class GenreTest extends TestCase
{
    /*
     * 販売種別テスト用テストケース
     * タイトルは作品
     * 画像とその他は取得する商品情報
     */
    public function workJacketDataProvider()
    {
        return [
            'DVDレンタル　単巻 DVD優先' => [
                'EXTTEST00001', // スタブで作品レンタルレスポンス限定
                'rental', //　スタブなので渡しても意味がないが、バリデーションの引っかかる為に渡す
                'ビデオ単品（Productには複数あり）作品タイトル',
                'https://cdn.video_test_single_product_02_size_l.jpg',
                '2017-11-06 00:00:00'
            ],
            'DVDレンタル　複数巻 DVD優先' => ['EXTTEST00002', 'rental',
                'キングダム',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/08599/4988064579570_1L.jpg',
                '2013-05-03 00:00:00'
            ],
            'CDレンタル　単巻' => ['EXTTEST00003', 'rental',
                'Flavor Of Life',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/00400/4988006210073_1L.jpg',
                '2007-02-28 00:00:00'
            ],
            'CDレンタル　複数巻' => ['EXTTEST00004', 'rental',
                '11月のアンクレット',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/12468/4988003514310_1L.jpg',
                '2017-11-22 00:00:00'
            ],
            'BOOKレンタル　単巻' => ['EXTTEST00005', 'rental',
                'レンタルBOOK単品（Productに複数刊あり）作品タイトル',
                'https://cdn.book_test_single_product_02_size_l.jpg',
                '2018-09-20 00:00:00'
            ],
            'BOOKレンタル　複数巻' => ['EXTTEST00006', 'rental',
                '進撃の巨人',
                '',
                '2018-04-09 00:00:00'
            ],
            'DVDセル　単巻' => ['EXTTEST00007', 'sell',
                'ビデオ単品（Productには複数あり）作品タイトル',
                'https://cdn.video_test_single_product_04_size_l.jpg',
                '2017-11-06 00:00:00'
            ],
            'DVDセル　複数巻' => ['EXTTEST00008', 'sell',
                'キングダム',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/08764/4988064624461_1L.jpg',
                '2013-06-28 00:00:00'
            ],
            'CDセル　単巻' => ['EXTTEST00009', 'sell',
                'Flavor Of Life',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/00400/4988006210073_1L.jpg',
                '2007-02-28 00:00:00'
            ],
            'CDセル　複数巻' => ['EXTTEST00010', 'sell',
                '11月のアンクレット',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/12468/4988003514365_1L.jpg',
                '2017-11-22 00:00:00'
            ],
            'BOOKセル 単巻' => ['EXTTEST00011', 'sell',
                'レンタルBOOK単品（Productに複数刊あり）作品タイトル',
                'https://cdn.book_test_single_product_04_size_l.jpg',
                '2018-09-20 00:00:00'
            ],
            'BOOKセル 複数巻' => ['EXTTEST00012', 'sell',
                '進撃の巨人',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/13153/9784063970494_1L.jpg',
                '2018-08-09 00:00:00'
            ],
            'GAMEセル 単巻' => ['EXTTEST00013', 'sell',
                '妖怪ウォッチバスターズ 白犬隊',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/10388/4571237660672_1L.jpg',
                '2015-07-11 00:00:00'
            ],
            'GAMEセル 複数巻' => ['EXTTEST00014', 'sell',
                'モンスターハンター:ワールド',
                'https://cdn.store-tsutaya.tsite.jp/images/jacket/12737/4976219089999_1L.jpg',
                '2018-01-26 00:00:00'
            ],
        ];
    }

    /**
     * @test
     * @dataProvider workJacketDataProvider
     */
    public function ジャケ写チェック($genreId, $saleType, $expectedTitle, $expectedImage, $expectedStartDate)
    {
        $url = '/genre/' . $genreId . '?saleType=' . $saleType;
        $response = $this->getWithAuth($url);
        $actual = json_decode($response->getContent(), true);
        $this->assertEquals($expectedTitle,     $actual['rows'][0]['workTitle']);
        $this->assertEquals($expectedImage,     $actual['rows'][0]['jacketL']);
        $this->assertEquals($expectedStartDate, $actual['rows'][0]['saleStartDate']);
    }
}