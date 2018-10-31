<?php

use tests\TestData;

/*
 * Work（作品情報取得） APIテスト
 *
 */
class WorkTest extends TestCase
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
            'レンタル 通常DVD ' => ['PTA0000SF309', 'rental', 200], // 通常DVD
            'レンタル 上映映画作品' => ['PTA0000WEKO0', 'rental', 200], // 上映映画
            'レンタル CD' => ['PTA0000U62N9', 'rental', 200], // CD
            'レンタル DVD' => ['PTA0000GD16P', 'rental', 200], // BOOK
            'レンタル GAME' => ['PTA0000U8W8U', 'rental', 204], // GAME
            '' => ['PTA0000SF309', 'sell', 200], // 通常DVD
            '' => ['PTA0000WEKO0', 'sell', 200], // 上映映画
            '' => ['PTA0000U62N9', 'sell', 200], // CD
            '' => ['PTA0000GD16P', 'sell', 200], // BOOK
            '' => ['PTA0000SF309', 'theater', 202], // 通常DVD
            '' => ['PTA0000U62N9', 'theater', 204], // CD
            '' => ['PTA0000GD16P', 'theater', 204], // BOOK
            '' => ['PTA0000U8W8U', 'theater', 204], // GAME
            '' => ['PTA0000WEKO0', 'theater', 200], // 上映映画
            '' => ['PTA0000V9KGR', 'theater', 202], // 配信オンリー
            'IDが存在しない場合' => ['PTA00000000', 'theater', 204], //
        ];
    }

    /**
     * @test
     * @dataProvider workDataProvider
     */
    public function セルレンタル区分別($workId, $saleType, $responseCode)
    {
        $url = '/work/' . $workId . '?saleType=' . $saleType;
        $response = $this->getWithAuth($url);
        $this->saleTypeTestCase($workId, $saleType, $responseCode, $response);
    }


    /**
     * @test
     * 作品情報取得テスト　ミュージコデータ
     */
    public function workMusico()
    {
        $url = '/work/PTA000092WMF';
        $response = $this->getJsonWithAuth( $url);
        $response->assertResponseStatus(200);
    }


    /**
     * @test
     * 作品情報取得 年齢認証テスト　R15対象外
     */
    public function workAgeLimitNoAdult()
    {
        $url = '/work/PTA0000RV0LG?saleType=sell';
        $response = $this->getJsonWithAuth( $url);
        $response->assertResponseStatus(200);
    }

    /**
     * @test
     * 作品情報取得 年齢認証テスト　アダルト対象
     */
    public function workAgeLimitAdult()
    {
        $url = '/work/PTA0000V6J54';
        $response = $this->getJsonWithAuth( $url);
        $response->assertResponseStatus(202);
        $response->seeJson([
            "message" => "Age limit error.",
            "status" => "202-001"
        ]);
    }

    /**
     * @test
     * DVDの場合はsupplementがブランクになるテスト
     */
    public function workDVDSupplementBlank()
    {
        $url = '/work/PTA0000SF309';
        $response = $this->getJsonWithAuth( $url);
        $response->seeJson([
            'supplement' => '',
        ]);
    }

}