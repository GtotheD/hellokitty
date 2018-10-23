<?php


class WorkProductsRentalTest extends TestCase
{
    public function workDataProvider()
    {
        return [
            '商品が一個の場合' => ['PTA0000SF309'],
            '商品が二個の場合' => ['PTA00007YKJ7'],
        ];
    }

    /**
     * @test
     * @dataProvider workDataProvider
     */
    public function セルレンタル区分別($workId)
    {
        $url = '/work/' . $workId . '/products/rental';
        $response = $this->getWithAuth($url);
        $actual = json_decode($response->getContent(), true);
        $expected = json_decode(file_get_contents(__DIR__ . '/expected/' . $workId), true);
        unset($expected['data']['createdAt']);
        unset($expected['data']['updatedAt']);
        unset($actual['data']['createdAt']);
        unset($actual['data']['updatedAt']);
        $this->assertEquals($expected, $actual);
    }
}