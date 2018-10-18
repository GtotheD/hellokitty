<?php

use tests\TestData;

/*
 * recommend/theater（上映映画ページ用リコメンド） APIテスト
 *
 */
class WorkRecommendTheaterTest extends TestCase
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
            ['PTATEST00001'], // 該当作品のジャンルがアニメで原作者がいて、原作者の作品一覧を出力される
            ['PTATEST00002'], // 該当作品のジャンルがアニメで原作者がなく、アニメのデイリーランキングが出力される
            ['PTATEST00003'], // 該当作品のジャンルが洋画アクションで、ジャンル一覧で洋画アクションが出力される
            ['PTA0000VJ2YY'], // 該当作品のジャンルが洋画ホラーで、ジャンル一覧で洋画ホラーが出力される
            ['PTA0000VK74E'], // 該当作品のジャンルが洋画ドラマで、ジャンル一覧で洋画ドラマが出力される
            ['PTA0000VEW03'], // 該当作品のジャンルが洋画SFで、ジャンル一覧で洋画SFが出力される
            ['PTA0000R7QY9'], // 該当作品のジャンルが邦画ホラー/SFで、最初の出演者の作品が出力される。
            ['PTA0000UR2CS'], // 該当作品のジャンルが邦画アクションで、出演者が存在せず最初の監督の作品が出力される。
            ['PTA0000VOQY2'], // 該当作品のジャンルが邦画ドラマで、出演者・監督の双方が存在しないため、邦画のランキングが出力される。
            ['PTA0000RDEXD'], // 該当作品のジャンルが上記以外で、全ジャンルのデイリーランキングが出力される
        ];
    }

    /**
     * @test
     * @dataProvider workDataProvider
     */
    public function 出力結果テスト($workId)
    {
        $this->getWithAuth('/work/PTA00007Z7HS');
        $this->getWithAuth('/work/PTA00007XQY7');
        $url = '/work/' . $workId . '/recommend/theater';
        $response = $this->getWithAuth($url);
        $this->actualDifference($workId, $response);
    }
}