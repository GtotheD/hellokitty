<?php

use tests\TestData;

class ImportTest extends TestCase
{


    public function setUp()
    {
        parent::setUp();
        $testData = new TestData;
        $testData->jsonInitialize();
        // 　プレミアム用データ作成
        $testData->jsonInitializePremium();
    }

    /**
     * @test
     */
    public function checkApiAuth()
    {
        $response = $this->call('GET',
            env('URL_PATH_PREFIX') . env('API_VERSION') . '/structure/dvd/rental');
        $this->assertEquals(401, $response->getStatusCode());

    }
//
//    /**
//     * @return array
//     */
//    public function fixedBannerProvider()
//    {
//        return [
//            [
//                'isLoggedIn' => 'true',
//                'expected' => ['fixed_2', 'fixed_3']
//            ],
//            [
//                'isLoggedIn' => 'false',
//                'expected' => ['fixed_1', 'fixed_3']
//            ]
//        ];
//    }
//
//    /**
//     * @test
//     * @group update
//     */
//    public function import()
//    {
//        $testData = new TestData;
//        $this->assertTrue($testData->import());
//    }
//
//    /**
//     * @test
//     * @dataProvider fixedBannerProvider
//     * @group firstInsert
//     */
//    public function checkApiFixedBanner($isLoggedIn, $expected)
//    {
//        $response = $this->getWithAuth('/fixed/banner', ['isLoggedIn' => $isLoggedIn]);
//        $this->assertEquals(200, $response->getStatusCode());
//        $responseData = $response->getData(true);
//        $this->assertEquals(2, count($responseData['rows']));
//        $this->assertEquals($expected[0], $responseData['rows'][0]['imageUrl']);
//        $this->assertEquals($expected[1], $responseData['rows'][1]['imageUrl']);
//    }
//
//    /**
//     * @return array
//     */
//    public function structureProvider()
//    {
//        return [
//            ['/structure/dvd/rental', ['1_1_1', '1_1_2', '1_1_3', '1_1_4', '1_1_5']],
//            ['/structure/dvd/sell', ['1_2_1', '1_2_2', '1_2_3', '1_2_4', '1_2_5']],
//            ['/structure/cd/rental', ['2_1_1', '2_1_2', '2_1_3', '2_1_4', '2_1_5']],
//            ['/structure/cd/sell', ['2_2_1', '2_2_2', '2_2_3', '2_2_4', '2_2_5']],
//            ['/structure/book/rental', ['3_1_1', '3_1_2', '3_1_3', '3_1_4', '3_1_5']],
//            ['/structure/book/sell', ['3_2_1', '3_2_2', '3_2_3', '3_2_4', '3_2_5']],
//            ['/structure/game/sell', ['4_2_1', '4_2_2', '4_2_3', '4_2_4', '4_2_5']],
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider structureProvider
//     * @group firstInsert
//     */
//    public function checkApiStructure($url, $expected)
//    {
//        $response = $this->getWithAuth($url);
//        $this->assertEquals(200, $response->getStatusCode());
//        $responseData = $response->getData(true);
//        $this->assertEquals(count($expected), count($responseData['rows']));
//        $this->assertEquals($expected[0], $responseData['rows'][0]['title']);
//        $this->assertEquals($expected[1], $responseData['rows'][1]['title']);
//        $this->assertEquals($expected[2], $responseData['rows'][2]['title']);
//        $this->assertEquals($expected[3], $responseData['rows'][3]['title']);
//        $this->assertEquals($expected[4], $responseData['rows'][4]['title']);
//    }
//
//    public function sectionProvider()
//    {
//        return [
//            'DVDレンタル' => [
//                'url' => '/section/dvd/rental/1_1_2',
//                'expected' => ['089937132', '082394367', '089939640']
//            ],
//            [
//                'url' => '/section/dvd/sell/1_2_2',
//                'expected' => ['4988142453822', '4988111144690', '4959241980366']
//            ],
//            [
//                'url' => '/section/cd/rental/2_1_2',
//                'expected' => ['005634334', '005773147', '005841435'],
//            ],
//            [
//                'url' => '/section/cd/sell/2_2_2',
//                'expected' => ['4988003508821', '4547366354164', '4547366377972']
//            ],
//            [
//                'url' => '/section/book/rental/3_1_2',
//                'expected' => ['102421256', '103390522', '103388956'],
//            ],
//            [
//                'url' => '/section/book/sell/3_2_2',
//                'expected' => ['9784063970494', '9784088814964', '9784592144403'],
//            ],
//            [
//                'url' => '/section/game/sell/4_2_2',
//                'expected' => ['4976219095631', '4938833022950', '4571237660672'],
//            ],
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider sectionProvider
//     * @group firstInsert
//     */
//    public function checkApiSection($url, $expected)
//    {
//        $response = $this->getWithAuth($url);
//        $this->assertEquals(200, $response->getStatusCode());
//        $responseData = $response->getData(true);
//        $this->assertEquals(count($expected), count($responseData['rows']));
//        $this->assertEquals($expected[0], $responseData['rows'][0]['code']);
//        $this->assertEquals($expected[1], $responseData['rows'][1]['code']);
//        $this->assertEquals($expected[2], $responseData['rows'][2]['code']);
//    }
//
//    public function bannerProvider()
//    {
//        return [
//            ['/section/banner/1_1_1', ['banner_1_1_1', 'banner_2_1_1']],
//            ['/section/banner/1_2_1', ['banner_1_1_2', 'banner_2_1_2']],
//            ['/section/banner/2_1_1', ['banner_1_2_1', 'banner_2_2_1']],
//            ['/section/banner/2_2_1', ['banner_1_2_2', 'banner_2_2_2']],
//            ['/section/banner/3_1_1', ['banner_1_3_1', 'banner_2_3_1']],
//            ['/section/banner/3_2_1', ['banner_1_3_2', 'banner_2_3_2']],
//            ['/section/banner/4_2_1', ['banner_1_4_2', 'banner_2_4_2']],
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider bannerProvider
//     * @group firstInsert
//     */
//    public function checkApiBanner($url, $expected)
//    {
//        $response = $this->getWithAuth($url);
//        $this->assertEquals(200, $response->getStatusCode());
//        $responseData = $response->getData(true);
//        dd($responseData);
//        $this->assertEquals(count($expected), count($responseData['rows']));
//        $this->assertEquals($expected[0], $responseData['rows'][0]['imageUrl']);
//        $this->assertEquals($expected[1], $responseData['rows'][1]['imageUrl']);
//    }
//
//    /**
//     * @test
//     * @group update
//     */
//    public function updateStructureJson()
//    {
//        $testData = new TestData;
//        $testData->updateBaseJson();
//        $this->assertTrue($testData->import());
//    }
//
//    public function structureProviderUpdated()
//    {
//        return [
//            ['/structure/dvd/rental', ['1_1_1_update', '1_1_2_update', '1_1_3_update', '1_1_4_update', '1_1_5_update']],
//            ['/structure/dvd/sell', ['1_2_1', '1_2_2', '1_2_3', '1_2_4', '1_2_5']],
//            ['/structure/cd/rental', ['2_1_1_update', '2_1_2_update', '2_1_3_update', '2_1_4_update', '2_1_5_update']],
//            ['/structure/cd/sell', ['2_2_1', '2_2_2', '2_2_3', '2_2_4', '2_2_5']],
//            ['/structure/book/rental', ['3_1_1_update', '3_1_2_update', '3_1_3_update', '3_1_4_update', '3_1_5_update']],
//            ['/structure/book/sell', ['3_2_1', '3_2_2', '3_2_3', '3_2_4', '3_2_5']],
//            ['/structure/game/sell', ['4_2_1_update', '4_2_2_update', '4_2_3_update', '4_2_4_update', '4_2_5_update']],
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider structureProviderUpdated
//     * @group update
//     */
//    public function checkApiStructureUpdated($url, $expected)
//    {
//        $this->checkApiStructure($url, $expected);
//    }
//
//    /**
//     * @test
//     * @group update
//     */
//    public function updateSectionAndBannerJson()
//    {
//        $testData = new TestData;
//        $testData->updateSectionAndBannerJson();
//        $this->assertTrue($testData->import());
//    }
//
//    public function sectionProviderUpdated()
//    {
//        return [
//            ['/section/dvd/rental/1_1_2', ['110000001_update', '110000002', '110000003_update']],
//            ['/section/dvd/sell/1_2_2', ['120000001', '120000002', '120000003']],
//            ['/section/cd/rental/2_1_2', ['210000001_update', '210000002', '210000003_update']],
//            ['/section/cd/sell/2_2_2', ['220000001', '220000002', '220000003']],
//            ['/section/book/rental/3_1_2', ['310000001_update', '310000002', '310000003_update']],
//            ['/section/book/sell/3_2_2', ['320000001', '320000002', '320000003']],
//            ['/section/game/sell/4_2_2', ['420000001_update', '420000002', '420000003_update']],
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider sectionProviderUpdated
//     * @group update
//     */
//    public function checkApiSectionUpdated($url, $expected)
//    {
//        $this->checkApiSection($url, $expected);
//    }
//
//    public function bannerProviderUpdated()
//    {
//        return [
//            ['/section/banner/1_1_1', ['banner_1_1_1', 'banner_2_1_1_update']],
//            ['/section/banner/1_2_1', ['banner_1_1_2', 'banner_2_1_2']],
//            ['/section/banner/2_1_1', ['banner_1_2_1', 'banner_2_2_1_update']],
//            ['/section/banner/2_2_1', ['banner_1_2_2', 'banner_2_2_2']],
//            ['/section/banner/3_1_1', ['banner_1_3_1', 'banner_2_3_1_update']],
//            ['/section/banner/3_2_1', ['banner_1_3_2', 'banner_2_3_2']],
//            ['/section/banner/4_2_1', ['banner_1_4_2', 'banner_2_4_2_update']],
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider bannerProviderUpdated
//     * @group update
//     */
//    public function checkApiBannerUpdated($url, $expected)
//    {
//        $this->checkApiBanner($url, $expected);
//    }
//
//    /**
//     * @test
//     * @dataProvider structureProviderUpdated
//     * @group update
//     */
//    public function checkApiStructureUpdatedNoInfluence($url, $expected)
//    {
//        $this->checkApiStructure($url, $expected);
//    }
//
//    public function fixedBannerProviderUpdated()
//    {
//        return [
//            [
//                'isLoggedIn' => 'true',
//                'expected' => ['fixed_2_update', 'fixed_3']
//            ],
//            [
//                'isLoggedIn' => 'false',
//                'expected' => ['fixed_1_update', 'fixed_3']
//            ]
//        ];
//    }
//
//    /**
//     * @test
//     * @group update
//     */
//    public function updateFixedBannerJson()
//    {
//        $testData = new TestData;
//        $testData->updateFixedBannerJson();
//        $this->assertTrue($testData->import());
//    }
//
//    /**
//     * @test
//     * @dataProvider fixedBannerProviderUpdated
//     * @group update
//     */
//    public function checkApiFixedBannerUpdate($isLoggedIn, $expected)
//    {
//        $this->checkApiFixedBanner($isLoggedIn, $expected);
//    }
//
//    /**
//     * @test
//     * @dataProvider structureProviderUpdated
//     * @group update
//     */
//    public function checkApiStructureUpdatedNoInfluenceSecond($url, $expected)
//    {
//        $this->checkApiStructure($url, $expected);
//    }
//
//    /**
//     * @test
//     * @dataProvider sectionProviderUpdated
//     * @group update
//     */
//    public function checkApiSectionUpdatedNoInfluence($url, $expected)
//    {
//        $this->checkApiSection($url, $expected);
//    }
//
//
//    /**
//     * @test
//     * @dataProvider bannerProviderUpdated
//     * @group update
//     */
//    public function checkApiBannerUpdatedNoInfluence($url, $expected)
//    {
//        $this->checkApiBanner($url, $expected);
//    }
//
//    /**
//     * @test
//     * @group update
//     */
//    public function addRowSectionAndBannerJson()
//    {
//        $testData = new TestData;
//        $testData->addRowSectionAndBannerJson();
//        $this->assertTrue($testData->import());
//    }
//
//    public function addRowSectionProviderUpdated()
//    {
//        return [
//            ['/section/dvd/rental/1_1_2', ['110000001_update', '110000002', '110000003_update', '110000004']],
//            ['/section/dvd/sell/1_2_2', ['120000001', '120000002', '120000003']],
//            ['/section/cd/rental/2_1_2', ['210000001_update', '210000002', '210000003_update', '210000004']],
//            ['/section/cd/sell/2_2_2', ['220000001', '220000002', '220000003']],
//            ['/section/book/rental/3_1_2', ['310000001_update', '310000002', '310000003_update', '310000004']],
//            ['/section/book/sell/3_2_2', ['320000001', '320000002', '320000003']],
//            ['/section/game/sell/4_2_2', ['420000001_update', '420000002', '420000003_update', '420000004']],
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider addRowSectionProviderUpdated
//     * @group update
//     */
//    public function checkApiAddRowSectionUpdated($url, $expected)
//    {
//        $this->checkApiSection($url, $expected);
//    }
//
//    public function addRowNBannerProviderUpdated()
//    {
//        return [
//            ['/section/banner/1_1_1', ['banner_1_1_1', 'banner_2_1_1_update', 'banner_3_1_1']],
//            ['/section/banner/1_2_1', ['banner_1_1_2', 'banner_2_1_2']],
//            ['/section/banner/2_1_1', ['banner_1_2_1', 'banner_2_2_1_update', 'banner_3_2_1']],
//            ['/section/banner/2_2_1', ['banner_1_2_2', 'banner_2_2_2']],
//            ['/section/banner/3_1_1', ['banner_1_3_1', 'banner_2_3_1_update', 'banner_3_3_1']],
//            ['/section/banner/3_2_1', ['banner_1_3_2', 'banner_2_3_2']],
//            ['/section/banner/4_2_1', ['banner_1_4_2', 'banner_2_4_2_update', 'banner_3_4_2']],
//        ];
//    }
//
//    /**
//     * @test
//     * @dataProvider addRowNBannerProviderUpdated
//     * @group update
//     */
//    public function checkApiAddRowBannerUpdated($url, $expected)
//    {
//        $this->checkApiBanner($url, $expected);
//    }
//
//    /**
//     * @test
//     * @dataProvider structureProviderUpdated
//     * @group update
//     */
//    public function checkApiStructureUpdatedNoInfluenceThird($url, $expected)
//    {
//        $this->checkApiStructure($url, $expected);
//    }

}