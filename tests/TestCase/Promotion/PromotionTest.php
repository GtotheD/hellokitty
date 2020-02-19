<?php

use tests\TestData;

/*
 * キャンペーン情報
 */
class PromotionTest extends TestCase
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            ['dev_ouchide'],
        ];
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function promotionData($promotion_id)
    {
        $url = '/promotion/' . $promotion_id;
        $response = $this->getJsonWithAuth($url);
        $response->assertResponseStatus(200);
    }
}
