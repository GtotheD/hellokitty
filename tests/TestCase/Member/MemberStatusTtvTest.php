<?php

class MemberStatusTtvTest extends \Laravel\Lumen\Testing\TestCase
{

    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }

    /**
     * Test get store name (when environment is local)
     */
    public function testGetStoreName()
    {
        $storeNmae = 'SHIBUYA TSUTAYA';
        $repo = new \App\Repositories\TWSRepository();
        $result = $repo->storeDetail(1)->get();

        $this->assertEquals($storeNmae, $result['entry']['storeName']);
    }
}
