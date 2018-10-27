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
            'ランキング 販売　TWSで返却される該当の商品の画像が出力されること' =>
                [
                    'agg',
                    'D045',
                    200
                ],
            'ランキング レンタル　TWSで返却される該当の商品の画像が出力されること' =>
                [
                    'agg',
                    'D045',
                    200
                ]

        ];
    }

    /**
     * @test
     * @dataProvider workDataProvider
     */
    public function 出力結果テスト($codeType, $code, $responseCode)
    {
        $this->getWithAuth('/work/PTA00007Z7HS'); // 千と千尋
        $this->getWithAuth('/work/PTA0000SF309'); // パイレーツ
        $this->getWithAuth('/work/PTA0000G8N5G'); // 黒子
        $url = sprintf('/section/ranking/%s/%s', $codeType, $code);
        $response = $this->getWithAuth($url);
        $this->actualDifference($code, $response);
    }
}
