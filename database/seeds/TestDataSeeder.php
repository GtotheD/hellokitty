<?php

use Illuminate\Database\Seeder;
use League\Csv\Reader;

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
        $reader = Reader::createFromPath(base_path().'/tests/fixture/structure.csv', 'r')
            ->setHeaderOffset(0);
        $records = $reader->getRecords();
        $sortIndex = 1;
        foreach ($records as $record) {
            $structure[] =
                [
                    'sort' => $sortIndex,
                    'goods_type' => $record['goods_type'],
                    'sale_type' => $record['sale_type'],
                    'section_type' => $record['section_type'],
                    'display_start_date' => $record['display_start_date'],
                    'display_end_date' => $record['display_end_date'],
                    'title' => $record['title'],
                    'link_url' => $record['link_url'],
                    'is_tap_on' => $record['is_tap_on'],
                    'is_ranking' => $record['is_ranking'],
                    'api_url'  => $record['api_url'],
                    'section_file_name' => $record['section_file_name']
                ];
            $sortIndex++;
        }

        DB::table($structuresTable)->truncate();
        DB::table($structuresTable)->insert($structure);

        DB::table($sectionsTable)->truncate();
        DB::table($sectionsTable)->insert($this->getSectionTestData());
    }

    private function getStructureTestData() {
        return [
            [
                'sort' => 1,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 2,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'リリース情報',
                'link_url' => 'https://tsutaya.tsite.jp/',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => 'section/release/manual/01',
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
                'api_url'  => 'section/ranking/agg/D045',
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
                'api_url'  => 'section/normal',
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
                'api_url'  => 'section/ranking/himo/{code}',
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
                'api_url'  => 'section/ranking/himo/{code}',
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
                'api_url'  => 'section/ranking/himo/{code}',
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
                'api_url'  => 'section/ranking/himo/{code}',
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
                'api_url'  => 'section/ranking/himo/{code}',
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
                'api_url'  => 'section/dvd/rental',
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
                'api_url'  => 'section/dvd/rental',
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
                'api_url'  => 'section/dvd/rental',
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
                'api_url'  => 'section/release/auto/27/011',
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
                'api_url'  => 'section/release/auto/29/011',
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
                'api_url'  => 'section/dvd/rental',
                'section_file_name' => 'd'
            ]
// 販売リスト
            ,[
                'sort' => 4,
                'goods_type' => 1,
                'sale_type' => 2,
                'section_type' => 2,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'リリース情報',
                'link_url' => 'https://tsutaya.tsite.jp/',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '/section/release/manual/04',
                'section_file_name' => ''
            ]
            ,[
                'sort' => 4,
                'goods_type' => 1,
                'sale_type' => 2,
                'section_type' => 2,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'ランキング総合',
                'link_url' => 'https://tsutaya.tsite.jp/',
                'is_tap_on' => 0,
                'is_ranking' => 1,
                'api_url'  => '/section/ranking/agg/D050',
                'section_file_name' => ''
            ]
            ,[
                'sort' => 4,
                'goods_type' => 1,
                'sale_type' => 2,
                'section_type' => 2,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'ランキング：総合',
                'link_url' => 'https://tsutaya.tsite.jp/',
                'is_tap_on' => 0,
                'is_ranking' => 1,
                'api_url'  => '/section/ranking/agg/D051',
                'section_file_name' => ''
            ]

        ];
    }

    private function getSectionTestData() {
        return [
            // バナーセクション定義
//            [
//                'ts_structure_id' => 1,
//                'code_type' => 1,
//                'image_url' => 'asset/image/banner_sample/banner.jpg'
//            ],
//            [
//                'ts_structure_id' => 1,
//                'code_type' => 1,
//                'image_url' => 'asset/image/banner_sample/banner2.jpg'
//            ],
//            [
//                'ts_structure_id' => 1,
//                'code_type' => 1,
//                'image_url' => 'asset/image/banner_sample/banner3.jpg'
//            ],
//            [
//                'ts_structure_id' => 1,
//                'code_type' => 1,
//                'image_url' => 'asset/image/banner_sample/banner4.jpg'
//            ],

            // 通常セクション定義
            [
                'ts_structure_id' => 9,
                'code_type' => 1,
                'code' => '4988013468993'
            ],
            [
                'ts_structure_id' => 9,
                'code_type' => 1,
                'code' => '4532612131866'
            ],
            [
                'ts_structure_id' => 9,
                'code_type' => 1,
                'code' => '4988142949721'
            ],
            [
                'ts_structure_id' => 9,
                'code_type' => 1,
                'code' => '4988142946720'
            ],
            [
                'ts_structure_id' => 10,
                'code_type' => 1,
                'code' => '4988142311719'
            ],
            [
                'ts_structure_id' => 10,
                'code_type' => 1,
                'code' => '4988142284914'
            ],
            [
                'ts_structure_id' => 11,
                'code_type' => 1,
                'code' => '4547462074416'
            ],
            [
                'ts_structure_id' => 11,
                'code_type' => 1,
                'code' => '4548967343472'
            ],
            [
                'ts_structure_id' => 16,
                'code_type' => 1,
                'code' => '4988126207625'
            ],
            [
                'ts_structure_id' => 16,
                'code_type' => 1,
                'code' => '4907953084902'
            ]
        ];
    }
}
