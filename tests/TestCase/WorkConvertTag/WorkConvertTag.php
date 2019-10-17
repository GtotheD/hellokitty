<?php

use tests\TestData;

/*
 * Work（タグ名変換） APIテスト
 *
 */
class WorkConvertTag extends TestCase
{

    /**
     * @test
     */
    public function testConvertTag()
    {
        $url = '/convert/tags?tags[]=movie_00049&tags[]=movie_00180';
        $response = $this->getWithAuth($url);
        $actual = json_decode($response->getContent(), true);
        $expected = json_decode(file_get_contents(__DIR__ . '/expected/convertTag_1'), true);
        $this->assertEquals($expected, $actual);
    }
}
