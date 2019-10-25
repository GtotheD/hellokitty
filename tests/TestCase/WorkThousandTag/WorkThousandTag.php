<?php

use tests\TestData;

/*
 * Work（1000タグ） APIテスト
 *
 */
class WorkThousandTag extends TestCase
{
    public function workDataProvider()
    {
        return [
            ['movie_00049'],
        ];
    }

    /**
     * @test
     * @dataProvider workDataProvider
     */
    public function testGetThousandTag($thousandTag)
    {
        $url = '/work/tag/' . $thousandTag;
        $response = $this->getWithAuth($url);
        $actual = json_decode($response->getContent(), true);
        $expected = json_decode(file_get_contents(__DIR__ . '/expected/' . $thousandTag), true);
        $this->assertEquals($expected, $actual);
    }
}
