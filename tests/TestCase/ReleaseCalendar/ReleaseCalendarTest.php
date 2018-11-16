<?php

use tests\TestData;

/*
 * Release（リリカレ） APIテスト
 *
 */
class ReleaseCalendarTest extends TestCase
{
    public function __construct(string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->testDir = __DIR__;
    }

    /*
     * テスト用テストケース
     */
    public function workDataProvider()
    {
        return [
            'lastの情報が出力されること' =>
                [
                    'last',
                    '1',
                    200
                ],
            'thisの情報が出力されること' =>
                [
                    'this',
                    '1',
                    200
                ],
            'nextの情報が出力されること' =>
                [
                    'next',
                    '1',
                    200
                ],
            'last_16' =>
                [
                    'last',
                    '16',
                    200
                ],
            'last_18' =>
                [
                    'last',
                    '18',
                    200
                ],
            'this_15' =>
                [
                    'this',
                    '15',
                    200
                ]
        ];
    }

    /**
     * @test
     * @dataProvider workDataProvider
     */
    public function 出力結果テスト($month, $tapGenreId, $responseCode)
    {
        $url = sprintf('/release/%s/%s', $month, $tapGenreId);
        $response = $this->getWithAuth($url);
//        var_dump(json_encode(json_decode($response->getContent()),JSON_UNESCAPED_UNICODE));
//        $this->actualDifference($releaseDisplayType . '_' . $tapGenreId, $response);
    }
}
