<?php

class PushNotificationStatusTest extends TestCase
{
    public function workDataProvider()
    {
        return [
            [
                'tolId' => '38pcAMAGbN89rY6DBWS18fGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'result' => [
                    "status" => "0",
                    "data" => [
                        [
                            "applicationKind" => "600",
                            "sort" => "1",
                            "name" => "レンタル商品返却予定日のお知らせ",
                            "registerStatus" => "1"
                        ],
                        [
                            "applicationKind" => "601",
                            "sort" => "2",
                            "name" => "返却日の3日前",
                            "registerStatus" => "1"
                        ],
                        [
                            "applicationKind" => "602",
                            "sort" => "3",
                            "name" => "返却日の前々日",
                            "registerStatus" => "0"
                        ],
                        [
                            "applicationKind" => "603",
                            "sort" => "4",
                            "name" => "返却日の前日",
                            "registerStatus" => "0"
                        ],
                        [
                            "applicationKind" => "604",
                            "sort" => "5",
                            "name" => "返却日当日",
                            "registerStatus" => "0"
                        ],
                        [
                            "applicationKind" => "605",
                            "sort" => "6",
                            "name" => "お知らせ",
                            "registerStatus" => "1"
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

        $url = '/notification/get';
        $response = $this->postWithAuth($url, $json);
        $actual = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($actual, $result);
    }
}
