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

    /**
     * @test
     * 作品情報取得テスト
     */
    public function work()
    {
        $url = '/work/PTA0000SF309';
        $response = $this->getJsonWithAuth( $url);
        $response->assertResponseStatus(200);
    }

    /**
     * @test
     * 作品情報取得 年齢認証テスト　R15対象外
     */
    public function workAgeLimitNoAdult()
    {
        $url = '/work/PTA0000R6VWD?saleType=sell';
        $response = $this->getJsonWithAuth( $url);
        $response->assertResponseStatus(200);
    }

    /**
     * @test
     * 作品情報取得 年齢認証テスト　アダルト対象
     */
    public function workAgeLimitAdult()
    {
        $url = '/work/PTA0000V6J54';
        $response = $this->getJsonWithAuth( $url);
        $response->assertResponseStatus(202);
    }

    /**
     * @test
     * DVDの場合はsupplementがブランクになるテスト
     */
    public function workDVDSupplementBlank()
    {
        $url = '/work/PTA0000SF309';
        $response = $this->getJsonWithAuth( $url);
        $response->seeJson([
            'supplement' => '',
        ]);
    }

    /**
     * @test
     */
    public function workProduct()
    {
        $response = $this->getJsonWithAuth('/work/PTA0000SF309/products');
        $response->assertResponseStatus(200);
    }

    /**
     * 巻数を付与されていることの確認
     * @test
     */
    public function workProductCheckNumberOfVolumeForBook()
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
     * 巻数が一巻のみの場合は巻数を付与しない場合のテスト
     * @test
     */
    public function workProductCheckNotNumberOfVolumeForBook()
    {
        $url = '/work/PTA0000R81I8';
        $this->getJsonWithAuth( $url);
        $response = $this->getJsonWithAuth('/work/PTA0000R81I8/products');
        $response->seeJson([
                'totalCount' => 1,
                'productName' => 'こちら葛飾区亀有公園前派出所 ∞巻<特装版>',
            ]);
    }

    /**
     * 巻数が一巻のみの場合は巻数を付与しない場合のテスト
     * @test
     */
    public function workProductIgnoreVHS()
    {
        $url = '/work/PTA00008M81I';
        $this->getJsonWithAuth( $url);
        $response = $this->getJsonWithAuth('/work/PTA00008M81I/products?saleType=rental');
        $response->assertResponseStatus(204);
    }

    /**
     * 巻数が一巻のみの場合は巻数を付与しない場合のテスト
     * @test
     */
    public function workProductIgnoreVHSDisplayOtherData()
    {
        $url = '/work/PTA00008M81I';
        $this->getJsonWithAuth( $url);
        $response = $this->getJsonWithAuth('/work/PTA00008M81I/products?saleType=sell');
        $response->seeJson([
            'totalCount' => 6,
            'productName' => '鉄人タイガーセブン 5',
        ]);
    }

    /**
     * @test
     */
    public function workProductRental()
    {
        $response = $this->getJsonWithAuth('/work/PTA0000SF309/products/rental');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
    public function workProductHas()
    {
        $response = $this->getJsonWithAuth('/work/PTA0000SF309/products/has');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
    public function workPeople()
    {
        $response = $this->getJsonWithAuth('/work/PTA0000SF309/people');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
    public function workSeries()
    {
        $response = $this->getJsonWithAuth('/work/PTA0000G8UGB/series');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
    public function workSeriesRental()
    {
        $response = $this->getJsonWithAuth('/work/PTA0000G8UGB/series?saleType=rental');
        $response->assertResponseStatus(200);
        $response->seeJson([
            'totalCount' => 1,
            'workId' => 'PTA0000H4C7V',
        ]);
    }
    /**
     * @test
     */
    public function workSeriesSell()
    {
        $response = $this->getJsonWithAuth('/work/PTA0000G8UGB/series?saleType=sell');
        $response->assertResponseStatus(200);
        $response->seeJson([
            'totalCount' => 2,
            'workId' => 'PTA0000H72ME',
            'workId' => 'PTA0000H4C7V',
        ]);
    }
    /**
     * @test
     */
//    public function workReviewFilmarks()
//    {
//        $response = $this->getJsonWithAuth('/work/PTA0000SF309/review/filmarks');
//        $response->assertResponseStatus(200);
//    }
    /**
     * @test
     */
//    public function workReviewDiscas()
//    {
//        $response = $this->getJsonWithAuth('/work/PTA0000SF309/review/Discas');
//        $response->assertResponseStatus(200);
//    }
    /**
     * @test
     */
//    public function workReviewTol()
//    {
//        $response = $this->getJsonWithAuth('/work/PTA0000SF309/review/Tol');
//        $response->assertResponseStatus(200);
//    }
    /**
     * @test
     */
    public function workRelationWorks()
    {
        $response = $this->getJsonWithAuth('/work/PTA0000SF309/relation/works');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
    public function workRelationPics()
    {
        $response = $this->getJsonWithAuth('/work/PTA0000SF309/relation/pics');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
    public function workRelationArtist()
    {
        $response = $this->getJsonWithAuth('/work/PTA0000U62N9/relation/artist');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
    public function workRecommendOther()
    {
        $response = $this->getJsonWithAuth('/work/PTA0000G4CSA/recommend/other');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
    public function workRecommendAuthor()
    {
        $response = $this->getJsonWithAuth('/work/PTA0000G4CSA/recommend/author');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
    public function workRecommendArtist()
    {
        $response = $this->getJsonWithAuth('/work/PTA0000SF309/recommend/artist');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
//    public function convertWorkId()
//    {
//        $response = $this->getJsonWithAuth('/convert/work/{idType}/{id}');
//        $response->assertResponseStatus(200);
//    }
    /**
     * @test
     */
    public function product()
    {
        $response = $this->getJsonWithAuth('/product/PDT0000U2COC');
        $response->assertResponseStatus(200);
    }

    /**
     * CDの場合、product_detailの情報を取得する
     *
     * @test
     */
    public function productCd()
    {
        $url = '/work/PTA0000V402M';
        $response = $this->getJsonWithAuth( $url);

        $response = $this->getJsonWithAuth('/product/PDT0000VH302');
        $response->assertResponseStatus(200);
    }


    /**
     * @test
     */
//    public function productStock()
//    {
//        $response = $this->getJsonWithAuth('/product/stock/{storeCd}/{productKey}');
//        $response->assertResponseStatus(200);
//    }
    /**
     * @test
     */
    public function people()
    {
        $response = $this->getJsonWithAuth('/people/PPS00001LBUW');
        $response->assertResponseStatus(200);
    }
    /**
     * @test
     */
    public function genre()
    {
        $response = $this->getJsonWithAuth('/genre/EXT0000000DY?saleType=sell');
        $response->assertResponseStatus(200);
    }

    /**
     * @test
     * NotFoundテスト
     */
    public function genreNotFound()
    {
        $response = $this->getJsonWithAuth('/genre/00NotFoundTest00?saleType=sell');
        $response->assertResponseStatus(204);
    }

    /**
     * @test
     * URL間違いテスト
     */
    public function genreNotFound2()
    {
        $response = $this->getJsonWithAuth('/NotFoundTest/00NotFoundTest00Y?saleType=sell');
        $response->assertResponseStatus(404);
    }

    /**
     * @test
     * パラメータ間違いテスト
     */
    public function genreNotFound3()
    {
        $response = $this->getJsonWithAuth('/genre/EXT0000000DY?PARAM_ERR=rental');
    //    print_r($response);
        $response->assertResponseStatus(400);
    }


    /**
     * @test
     */
    public function search()
    {
        $response = $this->getJsonWithAuth('/search/aaa');
        $response->assertResponseStatus(200);
    }

    /**
     * @test
     */
    public function searchCheckBody()
    {
        $response = $this->getJsonWithAuth('/search/bbb');
        $response->assertEquals(empty($response->response->original['rows']),false);
    }


    /**
     * @test
     */
    public function searchNotFoundCheckBody()
    {
        $response = $this->getJsonWithAuth('/search/%E5%91%BD%FF%FF');
        $response->assertEquals(empty($response->response->original['rows']),true);
    }


    /**
     * @test
     */
    public function searchSuggest()
    {
        $response = $this->getJsonWithAuth('/search/suggest/あ');
        $response->assertResponseStatus(200);
    }

    /**
     * @test
     */
    public function searchSuggestCheckBody()
    {
        $response = $this->getJsonWithAuth('/search/suggest/あ');
        $response->assertEquals(empty($response->response->original['rows']), false);
    }

    /**
     * @test
     */
    public function searchSuggestNotFoundCheckBody()
    {
        $response = $this->getJsonWithAuth('/search/suggest/ccc');
        print_r($response->response->original);
        $response->assertEquals(empty($response->response->original['rows']), true);
    }


    /**
     * @test
     */
    public function releaseCalender()
    {
        $response = $this->getJsonWithAuth('/release/this/18?sort=old&cdFormatType=album&onlyReleased=false');
        $response->assertResponseStatus(200);
    }
}