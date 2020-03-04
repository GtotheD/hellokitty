<?php

class PromotionRegistTest extends TestCase
{
    public function workDataProvider()
    {
        return [
            [
                'tolId' => '38pcAMAGbN89rY6DBWS18fGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'result' => ["result" => true]
            ],
        ];
    }

    /**
     * @test
     * @dataProvider workDataProvider
     */
    public function registPromotionTest($tolId, $result)
    {
        $json = json_encode([
            'tolId' => $tolId,
            'promotionId' => 'dv_uchide100',
            "prizeNo" => "1",
            "ques" => [
                [
                    "no" => "1",
                    "ans" => "1,2"
                ],
                [
                    "no" => "10",
                    "ans" => "2,4"
                ]
            ]
        ]);

        $url = '/promotion/entry';
        $response = $this->postWithAuth($url, $json);
        $actual = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($actual, $result);
    }
}
