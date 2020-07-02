<?php

class MemberStatusTtvTest extends \Laravel\Lumen\Testing\TestCase
{

    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }

    public function testMemberStatusTtv()
    {
        $url = env('URL_PATH_PREFIX') . env('API_VERSION') . '/member/status/ttv';
        $param = [
            'tlsc' => 1,
            'tolid' => 1
        ];
        $response = $this->json('POST', $url, $param, ['HTTP_Authorization' => 'k8AJR0NxM114Ogdl']);
        $response->assertResponseStatus(200);
    }

    /**
     * Test get store name (when environment is local)
     */
    public function testGetStoreName()
    {
        $storeNmae = 'SHIBUYA TSUTAYA';
        $repo = new \App\Repositories\DiscasRepository();
        $repo->setTolId(1);
        $result = $repo->storeDetail('')->get();

        $this->assertEquals($storeNmae, $result['entry']['storeName']);
    }

    /**
     * Test convert response if include tolId in body
     * @throws \App\Exceptions\NoContentsException
     */
    public function testProcessWhenHasTolId()
    {
        $data = [
            'httpcode' => '200',
            'tenpoCode' => '1234',
            'tenpoName' => 'SHIBUYA TSUTAYA',
            'tenpoPlanFee' => 1100,
            'nextUpdateDate' => '2020-08-01 12:00:00'
        ];
        $repo = new \App\Repositories\DiscasRepository();
        $repo->setNowDate('2020-07-02');
        $repo->setTolId(1);
        $result = $repo->processTtvWithTolid([]);
        $this->assertEquals($data, $result);
    }

    /**
     * Test logic to get next update date
     */
    public function testNextUpdateDate()
    {
        $repo = new \App\Repositories\DiscasRepository();

        $flatPlanRegistrationDate = '2020-08-13';
        $expected = '2020-07-13';
        $repo->setNowDate('2020-06-20');
        $result = $repo->getNextUpdateDate($flatPlanRegistrationDate);
        $this->assertEquals($expected, $result);

        $flatPlanRegistrationDate = '2020-08-31';
        $expected = '2020-06-30';
        $repo->setNowDate('2020-06-20');
        $result = $repo->getNextUpdateDate($flatPlanRegistrationDate);
        $this->assertEquals($expected, $result);

        $flatPlanRegistrationDate = '2020-09-30';
        $expected = '2020-07-30';
        $repo->setNowDate('2020-07-20');
        $result = $repo->getNextUpdateDate($flatPlanRegistrationDate);
        $this->assertEquals($expected, $result);

        $flatPlanRegistrationDate = '2020-09-15';
        $expected = '2020-08-15';
        $repo->setNowDate('2020-07-20');
        $result = $repo->getNextUpdateDate($flatPlanRegistrationDate);
        $this->assertEquals($expected, $result);
    }

}
