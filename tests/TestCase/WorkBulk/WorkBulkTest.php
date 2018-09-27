<?php

use tests\TestData;

/*
 * Work（一括作品情報取得） APIテスト
 *
 */
class WorkBulkTest extends TestCase
{

    /**
     * @test
     */
    public function お気に入り用一括取得()
    {
        $url = '/work/bulk';
        $json = json_encode([
            'saleType' => 'rental',
            'ageLimitCheck' => 'true',
            'ids' => [
                'PTA0000SF309', // 通常DVD
                'PTA0000V9KGR', // 配信のみ
                'PTA0000WEKO0', // 映画情報
                'PTA0000U62N9', // CD
                'PTA0000GD16P', // BOOK
            ],
        ]);
        $response = $this->postWithAuth($url, $json);
        $actual = json_decode($response->getContent(), true);
        $expected = json_decode(file_get_contents(__DIR__ . '/expected/bulk_1'), true);
        unset($expected['data']['createdAt']);
        unset($expected['data']['updatedAt']);
        unset($actual['data']['createdAt']);
        unset($actual['data']['updatedAt']);
        $this->assertEquals($expected, $actual);
    }

}