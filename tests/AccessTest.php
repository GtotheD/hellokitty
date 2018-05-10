<?php

use tests\TestData;

class AccessTest extends TestCase
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

    private function getWithAuth($uri, $param = [])
    {
        return $this->json('GET', $uri,
            $param, [], [], ['HTTP_Authorization' => 'k8AJR0NxM114Ogdl'], []
        );
    }

    /**
     * @test
     * 作品情報取得テスト
     */
    public function work()
    {
        $url = '/work/PTA0000SF309';
        $response = $this->getWithAuth( $url);
        $response->assertResponseStatus(200);
    }

    /**
     * @test
     * 作品情報取得 年齢認証テスト　R15対象外
     */
    public function workAgeLimitNoAdult()
    {
        $url = '/work/PTA0000R6VWD';
        $response = $this->getWithAuth( $url);
        $response->assertResponseStatus(200);
    }

    /**
     * @test
     * 作品情報取得 年齢認証テスト　アダルト対象
     */
    public function workAgeLimitAdult()
    {
        $url = '/work/PTA0000V6J54';
        $response = $this->getWithAuth( $url);
        $response->assertResponseStatus(202);
    }

    /**
     * @test
     */
    public function workProduct()
    {
        $response = $this->getWithAuth('/work/PTA0000SF309/products');
        $response->assertResponseStatus(200);
    }

    /**
     * @test
     */
    public function workProductRental()
    {
        $response = $this->getWithAuth('/work/PTA0000SF309/products/rental');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
    public function workProductHas()
    {
        $response = $this->getWithAuth('/work/PTA0000SF309/products/has');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
    public function workPeople()
    {
        $response = $this->getWithAuth('/work/PTA0000SF309/people');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
    public function workSeries()
    {
        $response = $this->getWithAuth('/work/PTA0000SF309/series');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
//    public function workReviewFilmarks()
//    {
//        $response = $this->getWithAuth('/work/PTA0000SF309/review/filmarks');
//        $response->assertResponseStatus(200);
//    }
    /**
     * @test
     */
//    public function workReviewDiscas()
//    {
//        $response = $this->getWithAuth('/work/PTA0000SF309/review/Discas');
//        $response->assertResponseStatus(200);
//    }
    /**
     * @test
     */
//    public function workReviewTol()
//    {
//        $response = $this->getWithAuth('/work/PTA0000SF309/review/Tol');
//        $response->assertResponseStatus(200);
//    }
    /**
     * @test
     */
    public function workRelationWorks()
    {
        $response = $this->getWithAuth('/work/PTA0000SF309/relation/works');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
    public function workRelationPics()
    {
        $response = $this->getWithAuth('/work/PTA0000SF309/relation/pics');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
    public function workRelationArtist()
    {
        $response = $this->getWithAuth('/work/PTA0000U62N9/relation/artist');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
    public function workRecommendOther()
    {
        $response = $this->getWithAuth('/work/PTA0000G4CSA/recommend/other');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
    public function workRecommendAuthor()
    {
        $response = $this->getWithAuth('/work/PTA0000G4CSA/recommend/author');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
    public function workRecommendArtist()
    {
        $response = $this->getWithAuth('/work/PTA0000SF309/recommend/artist');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
//    public function convertWorkId()
//    {
//        $response = $this->getWithAuth('/convert/work/{idType}/{id}');
//        $response->assertResponseStatus(200);
//    }
    /**
     * @test
     */
    public function product()
    {
        $response = $this->getWithAuth('/product/PDT0000U2COC');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
//    public function productStock()
//    {
//        $response = $this->getWithAuth('/product/stock/{storeCd}/{productKey}');
//        $response->assertResponseStatus(200);
//    }
    /**
     * @test
     */
    public function people()
    {
        $response = $this->getWithAuth('/people/PPS00001LBUW');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
    public function genre()
    {
        $response = $this->getWithAuth('/genre/EXT0000000DY?saleType=sell');
        $response->assertResponseStatus(200);
    }


    /**
     * @test
     */
    public function search()
    {
        $response = $this->getWithAuth('/search/%E5%91%BD');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
    public function searchSuggest()
    {
        $response = $this->getWithAuth('/search/suggest/あ');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
    public function releaseCalender()
    {
        $response = $this->getWithAuth('/release/this/18?sort=old&cdFormatType=album&onlyReleased=false');
        $response->assertResponseStatus(200);
    }
}