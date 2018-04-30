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


    private function getWithAuth($uri, $param = [])
    {
        return $this->json('GET', $uri,
            $param, [], [], ['HTTP_Authorization' => 'k8AJR0NxM114Ogdl'], []
        );
    }

    /**
     * @test
     */
    public function work()
    {
        $url = '/work/PTA0000SF309';
        $response = $this->getWithAuth( $url);
        $response->assertResponseStatus(200);
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
        $response = $this->getWithAuth('/work/PTA0000SF309/recommend/other');
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
    public function convertWorkId()
    {
        $response = $this->getWithAuth('/convert/work/{idType}/{id}');
        $response->assertResponseStatus(200);
    }
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
        $response = $this->getWithAuth('/people/PTA0000THQMV');
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
        $response = $this->getWithAuth('/search/suggest/%E5%91%BD');
        $response->assertResponseStatus(200);
    }


}