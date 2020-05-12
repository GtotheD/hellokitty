<?php

class PushNotificationStatusTest extends TestCase
{
    public function workDataProvider()
    {
        return [
            [
                'tolId' => '38pcAMAGbN89rY6DBWS18fGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'result' => [
                    "results" => [
                        "status" => "0",
                        "data" => [
                            [
                                "applicationKind" => "600",
                                "registerStatus" => true
                            ],
                            [
                                "applicationKind" => "601",
                                "registerStatus" => true
                            ],
                            [
                                "applicationKind" => "602",
                                "registerStatus" => false
                            ],
                            [
                                "applicationKind" => "603",
                                "registerStatus" => false
                            ],
                            [
                                "applicationKind" => "604",
                                "registerStatus" => false
                            ],
                            [
                                "applicationKind" => "605",
                                "registerStatus" => true
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }

    /**
     * @test
     * @dataProvider workDataProvider
     */
    public function getPushNotificationStatus($tolId, $result)
    {
        $json = json_encode([
            'tolId' => $tolId
        ]);

        $url = '/member/status/notification';
        $response = $this->postWithAuth($url, $json);
        $actual = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($actual, $result);
    }
}
