<?php

use tests\TestData;

/*
 * Work（作品情報取得） APIテスト
 *
 */
class WorkTest extends TestCase
{

    public function workDataProvider()
    {
        return [
            ['PTA0000SF309', 'rental'], // DVD
            ['PTA0000U62N9', 'rental'], // CD
            ['PTA0000GD16P', 'rental'], // BOOK
            ['PTA0000SF309', 'sell'], // DVD
            ['PTA0000U62N9', 'sell'], // CD
            ['PTA0000GD16P', 'sell'], // BOOK
            ['PTA0000U8W8U', 'sell'], // GAME
        ];
    }

    /**
     * @test
     * @dataProvider workDataProvider
     */
    public function セルレンタル区分別($workId, $saleType)
    {
        $url = '/work/' . $workId . '?saleType=' . $saleType;
        $response = $this->getWithAuth($url);
        $actual = json_decode($response->getContent(), true);
        $expected = json_decode(file_get_contents(__DIR__ . '/expected/' . $workId . '_' . $saleType), true);
        unset($expected['data']['createdAt']);
        unset($expected['data']['updatedAt']);
        unset($actual['data']['createdAt']);
        unset($actual['data']['updatedAt']);
        $this->assertEquals($expected, $actual);
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
            "message" => "Age limit auth error",
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