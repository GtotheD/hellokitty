<?php

use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $structuresTable = 'ts_structures';
        $sectionsTable = 'ts_sections';

        DB::table($structuresTable)->truncate();
        DB::table($structuresTable)->insert($this->getStructureTestData());

        DB::table($sectionsTable)->truncate();
        DB::table($sectionsTable)->insert($this->getSectionTestData());
    }

    private function getStructureTestData() {
        return [
            [
                'sort' => 1,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 1,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'リリース情報',
                'link_url' => 'https://tsutaya.tsite.jp/',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '/section/release/manual/01',
                'section_file_name' => ''
            ],
            [
                'sort' => 2,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 2,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'ランキング：総合',
                'link_url' => 'https://tsutaya.tsite.jp/',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '/section/ranking/agg/D045',
                'section_file_name' => ''
            ],
            [
                'sort' => 2,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 2,
                'display_start_date' => '2017-12-01',
                'display_end_date' => '2018-01-01',
                'title' => 'DVD-RENTAL 12月からなので表示しちゃいけない通常セクション',
                'link_url' => 'https://tsutaya.tsite.jp/',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '/section/normal',
                'section_file_name' => 'dvd_rental_normal_section1'
            ],
            [
                'sort' => 3,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 5,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'あなたにオススメ！',
                'link_url' => 'https://tsutaya.tsite.jp/',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '/section/ranking/himo/{code}',
                'section_file_name' => ''
            ],
            [
                'sort' => 3,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 5,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'あなたにオススメ！',
                'link_url' => 'https://tsutaya.tsite.jp/',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '/section/ranking/himo/{code}',
                'section_file_name' => ''
            ],
            [
                'sort' => 3,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 5,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'あなたにオススメ！',
                'link_url' => 'https://tsutaya.tsite.jp/',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '/section/ranking/himo/{code}',
                'section_file_name' => ''
            ],
            [
                'sort' => 3,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 5,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'あなたにオススメ！',
                'link_url' => 'https://tsutaya.tsite.jp/',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '/section/ranking/himo/{code}',
                'section_file_name' => ''
            ],
            [
                'sort' => 3,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 5,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'あなたにオススメ！',
                'link_url' => 'https://tsutaya.tsite.jp/',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '/section/ranking/himo/{code}',
                'section_file_name' => ''
            ],
            [
                'sort' => 3,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 2,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => '洋画',
                'link_url' => 'https://tsutaya.tsite.jp/',
                'is_tap_on' => 0,
                'is_ranking' => 1,
                'api_url'  => '/section/dvd/rental',
                'section_file_name' => 'a'
            ],
            [
                'sort' => 4,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 2,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => '邦画',
                'link_url' => 'https://tsutaya.tsite.jp/',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '/section/dvd/rental',
                'section_file_name' => 'b'
            ],
            [
                'sort' => 4,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 2,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'アニメ／キッズ',
                'link_url' => 'https://tsutaya.tsite.jp/',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '/section/dvd/rental',
                'section_file_name' => 'c'
            ],
            [
                'sort' => 4,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 2,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'アジアTVドラマ',
                'link_url' => 'https://tsutaya.tsite.jp/',
                'is_tap_on' => 0,
                'is_ranking' => 1,
                'api_url'  => '/section/release/auto/27/011',
                'section_file_name' => ''
            ],
            [
                'sort' => 4,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 2,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => '海外TVドラマ',
                'link_url' => 'https://tsutaya.tsite.jp/',
                'is_tap_on' => 0,
                'is_ranking' => 1,
                'api_url'  => '/section/release/auto/29/011',
                'section_file_name' => ''
            ],
            [
                'sort' => 4,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 3,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'お気に入りに登録したDVD作品',
                'link_url' => 'https://tsutaya.tsite.jp/',
                'is_tap_on' => 0,
                'is_ranking' => 1,
                'api_url'  => '',
                'section_file_name' => ''
            ],
            [
                'sort' => 4,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 4,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'チェックしたレンタルDVD作品',
                'link_url' => 'https://tsutaya.tsite.jp/',
                'is_tap_on' => 0,
                'is_ranking' => 1,
                'api_url'  => '',
                'section_file_name' => ''
            ],
            [
                'sort' => 4,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 2,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => '特集',
                'link_url' => 'https://tsutaya.tsite.jp/',
                'is_tap_on' => 0,
                'is_ranking' => 1,
                'api_url'  => '/section/dvd/rental',
                'section_file_name' => 'd'
            ],

            // DVD-SELL
            [
                'sort' => 1,
                'goods_type' => 1,
                'sale_type' => 2,
                'section_type' => 1,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'DVD-SELL バナーセクション',
                'link_url' => 'http://',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '/section/banner',
                'section_file_name' => 'dvd_sell_banner_section1'
            ],
            [
                'sort' => 2,
                'goods_type' => 1,
                'sale_type' => 2,
                'section_type' => 2,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'DVD-SELL 通常セクション',
                'link_url' => 'http://',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '/section/normal',
                'section_file_name' => 'dvd_sell_normal_section1'
            ],
            [
                'sort' => 3,
                'goods_type' => 1,
                'sale_type' => 2,
                'section_type' => 3,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'DVD-SELL 手動運用リリカレセクション（tap api経由）',
                'link_url' => 'http://',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '/section/release',
                'section_file_name' => ''
            ],
            [
                'sort' => 3,
                'goods_type' => 1,
                'sale_type' => 2,
                'section_type' => 4,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'DVD-SELL 自動運用リリカレセクション',
                'link_url' => 'http://',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '/section/release/D045',
                'section_file_name' => ''
            ],
            [
                'sort' => 3,
                'goods_type' => 1,
                'sale_type' => 2,
                'section_type' => 5,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'DVD-SELL ランキングセクション',
                'link_url' => 'http://',
                'is_tap_on' => 0,
                'is_ranking' => 1,
                'api_url'  => '/section/ranking',
                'section_file_name' => ''
            ],
            [
                'sort' => 4,
                'goods_type' => 1,
                'sale_type' => 2,
                'section_type' => 6,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'DVD-SELL お気に入りセクション',
                'link_url' => 'http://',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '',
                'section_file_name' => ''
            ],
            [
                'sort' => 4,
                'goods_type' => 1,
                'sale_type' => 2,
                'section_type' => 7,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'DVD-SELL チェックセクション',
                'link_url' => 'http://',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '',
                'section_file_name' => ''
            ],
            [
                'sort' => 4,
                'goods_type' => 1,
                'sale_type' => 2,
                'section_type' => 8,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'DVD-SELL レコメンドセクション',
                'link_url' => 'http://',
                'is_tap_on' => 0,
                'is_ranking' => 1,
                'api_url'  => '/section/recommend/ranking',
                'section_file_name' => ''
            ],
        ];
    }

    private function getSectionTestData() {
        return [
            // バナーセクション定義
            [
                'ts_structure_id' => 1,
                'code_type' => 1,
                'code' => '4988013468993'
            ],
            [
                'ts_structure_id' => 1,
                'code_type' => 1,
                'code' => '4988013468993'
            ],
            [
                'ts_structure_id' => 1,
                'code_type' => 1,
                'code' => '4988013468993'
            ],
            [
                'ts_structure_id' => 1,
                'code_type' => 1,
                'code' => '4988013468993'
            ],
            [
                'ts_structure_id' => 1,
                'code_type' => 1,
                'code' => '4988013468993'
            ],
            // 通常セクション定義
            [
                'ts_structure_id' => 2,
                'code_type' => 1,
                'code' => '4988013468993'
            ],
            [
                'ts_structure_id' => 2,
                'code_type' => 1,
                'code' => '4532612131866'
            ],
            [
                'ts_structure_id' => 2,
                'code_type' => 1,
                'code' => '4988142949721'
            ],
            [
                'ts_structure_id' => 2,
                'code_type' => 1,
                'code' => '4988142946720'
            ],
            [
                'ts_structure_id' => 2,
                'code_type' => 1,
                'code' => '4988142311719'
            ],
            [
                'ts_structure_id' => 2,
                'code_type' => 1,
                'code' => '4988142284914'
            ],
            [
                'ts_structure_id' => 2,
                'code_type' => 1,
                'code' => '4547462074416'
            ],
            [
                'ts_structure_id' => 2,
                'code_type' => 1,
                'code' => '4548967343472'
            ],
            [
                'ts_structure_id' => 2,
                'code_type' => 1,
                'code' => '4988126207625'
            ],
            [
                'ts_structure_id' => 2,
                'code_type' => 1,
                'code' => '4907953084902'
            ]
        ];
    }
}
