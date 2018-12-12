<?php

use tests\TestData;

/*
 * Release（リリカレ） APIテスト
 *
 */
class ReleaseTest extends TestCase
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
            'リリカレ 1 NEWEST レンタルDVD' =>
                [
                    'newest',
                    '1',
                    200
                ],
            'リリカレ 9 NEWEST 販売DVD' =>
                [
                    'newest',
                    '9',
                    200
                ],
            'リリカレ 17 NEWEST レンタルCD' =>
                [
                    'newest',
                    '17',
                    200
                ],
            'リリカレ 22 NEWEST 販売CD' =>
                [
                    'newest',
                    '22',
                    200
                ],
            'リリカレ 28 NEWEST レンタル本' =>
                [
                    'newest',
                    '28',
                    200
                ],
            'リリカレ 39 NEWEST 販売本' =>
                [
                    'newest',
                    '39',
                    200
                ],
            'リリカレ 51 NEWEST 販売ゲーム' =>
                [
                    'newest',
                    '51',
                    200
                ],
        ];
    }

    /**
     * @test
     * @dataProvider workDataProvider
     */
    public function 出力結果テスト($releaseDisplayType, $tapGenreId, $responseCode)
    {
        $url = sprintf('/section/release/himo/%s/%s', $releaseDisplayType, $tapGenreId);
        $response = $this->getWithAuth($url);
//        var_dump(json_encode(json_decode($response->getContent()),JSON_UNESCAPED_UNICODE));
        $this->actualDifference($releaseDisplayType . '_' . $tapGenreId, $response);
    }
}
