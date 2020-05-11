<?php

class PushNotificationRegistTest extends TestCase
{
    public function workDataProvider()
    {
        return [
            [
                'tolId' => '38pcAMAGbN89rY6DBWS18fGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'result' => [
                    'results' => [
                        "status" => "0",
                        "errCd" => []
                    ]
                ]
            ],
        ];
    }

    /**
     * @test
     * @dataProvider workDataProvider
     */
    public function registPushNotification($tolId, $result)
    {
        $json = json_encode([
            'tolId' => $tolId,
            "data" => [
                [
                    "applicationKind" => "600",
                    "status" => true
                ],
                [
                    "applicationKind" => "602",
                    "status" => false
                ]
            ]
        ]);

        $url = '/member/status/notification/update';
        $response = $this->postWithAuth($url, $json);
        $actual = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($actual, $result);
    }
}
