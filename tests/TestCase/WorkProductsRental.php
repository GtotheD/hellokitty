<?php

use tests\TestData;

class WorkProductsRental extends TestCase
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
    public function workProductRental($workId, $expected)
    {
        $url = '/work/'.$workId;
        $this->getJsonWithAuth( $url);
        $response = $this->getJsonWithAuth('/work/'.$workId.'/products/rental');
        $response->assertResponseStatus(200);
    }
}