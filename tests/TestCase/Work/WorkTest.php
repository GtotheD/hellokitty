<?php

use tests\TestData;
use Illuminate\Support\Carbon;

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
        // NewFlagが変更されるため、現在時刻を変更
        Carbon::setTestNow(new Carbon('2018-10-01 00:00:00'));

    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        $path = base_path('tests/Data/himo/crossworks/');
        $audioList = glob($path . '/audio/*');
        $videoList = glob($path . '/video/*');
        $bookList = glob($path . '/book/*');
        $gameList = glob($path . '/game/*');
        $list = array_merge($audioList, $videoList, $bookList, $gameList);
        foreach ($list as $row) {
            $workIds[] = [
                basename($row)
            ];
        }
        return $workIds;
    }

    /**
     * himoスタブ用データ全件アクセステスト
     * @dataProvider dataProvider
     * @test
     */
    public function 全件インポート($workId)
    {
        $url = '/work/' . $workId;
        $response = $this->getWithAuth($url);
        $this->assertNotEquals(500, $response->getStatusCode());
    }

    /*
     * 販売種別テスト用テストケース
     */
    public function workDataProvider()
    {
        return [
            'レンタル_通常DVD ' => ['PTA0000SF309', 'rental', 200], // 通常DVD
            'レンタル_上映映画作品' => ['PTA0000WEKO0', 'rental', 200], // 上映映画
            'レンタル_CD' => ['PTA0000U62N9', 'rental', 200], // CD
            'レンタル_DVD' => ['PTA0000GD16P', 'rental', 200], // BOOK
            'レンタル_GAME' => ['PTA0000U8W8U', 'rental', 204], // GAME
            'セル_通常DVD' => ['PTA0000SF309', 'sell', 200], // 通常DVD
            'セル_上映映画' => ['PTA0000WEKO0', 'sell', 200], // 上映映画
            'セル_CD' => ['PTA0000U62N9', 'sell', 200], // CD
            'セル_BOOK' => ['PTA0000GD16P', 'sell', 200], // BOOK
            '上映映画_通常DVD' => ['PTA0000SF309', 'theater', 202], // 通常DVD
            '上映映画_CD' => ['PTA0000U62N9', 'theater', 204], // CD
            '上映映画_BOOK' => ['PTA0000GD16P', 'theater', 204], // BOOK
            '上映映画_GAME' => ['PTA0000U8W8U', 'theater', 204], // GAME
            '上映映画_上映映画' => ['PTA0000WEKO0', 'theater', 200], // 上映映画
            '上映映画_配信オンリー' => ['PTA0000V9KGR', 'theater', 202], // 配信オンリー
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