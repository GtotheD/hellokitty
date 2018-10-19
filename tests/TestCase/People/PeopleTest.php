<?php

use tests\TestData;

/*
 * People（人物関連作品） APIテスト
 *
 */
class PeopleTest extends TestCase
{
    public function __construct(string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->testDir = __DIR__;
    }

    /*
     * 販売種別テスト用テストケース
     */
    public function workDataProvider()
    {
        return [
            '人物関連作品　レンタル' =>
                [
                    'PPS0000NPCK0',
                    'rental',
                    200
                ],
            '人物関連作品　セル' =>
                [
                    'PPS0000NPCK0',
                    'sell',
                    200
                ],
            '人物関連作品　上映映画' =>
                [
                    'PPS0000NPCK0',
                    'theater',
                    200
                ],
        ];
    }

    /**
     * @test
     * @dataProvider workDataProvider
     */
    public function 出力結果テスト($workId, $saleType, $responseCode)
    {
        $url = '/work/' . $workId . '?saleType=' . $saleType;
        $response = $this->getWithAuth($url);
        $this->saleTypeTestCase($workId, $saleType, $responseCode, $response);
    }
}