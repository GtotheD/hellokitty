<?php

class PromotionStatusTest extends TestCase
{
    public function workDataProvider()
    {
        return [
            [
                'tolId' => '38pcAMAGbN89rY6DBWS18fGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'result' => ['count' => '1']
            ],
        ];
    }

    /**
     * @test
     * @dataProvider workDataProvider
     */
    public function getPromotionStatus($tolId, $result)
    {
        $json = json_encode([
            'tolId' => $tolId,
            'promotionId' => 'dv_uchide100'
        ]);

        $url = '/promotion/entry/check';
        $response = $this->postWithAuth($url, $json);
        $actual = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($actual, $result);
    }
}
