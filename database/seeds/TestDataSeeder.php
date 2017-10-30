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
        //

        $table = 'ts_structures';
        DB::table($table)->truncate();
        DB::table($table)->insert([
            [
                'sort' => 1,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 1,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'DVD-RENTAL バナーセクション',
                'link_url' => 'http://',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '/section/banner',
                'section_file_name' => 'dvd_rental_banner_section1'
            ],
            [
                'sort' => 2,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 2,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'DVD-RENTAL 通常セクション',
                'link_url' => 'http://',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '/section/normal',
                'section_file_name' => 'dvd_rental_normal_section1'
            ],
            [
                'sort' => 2,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 2,
                'display_start_date' => '2017-12-01',
                'display_end_date' => '2018-01-01',
                'title' => 'DVD-RENTAL 12月からなので表示しちゃいけない通常セクション',
                'link_url' => 'http://',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '/section/normal',
                'section_file_name' => 'dvd_rental_normal_section1'
            ],
            [
                'sort' => 3,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 3,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'DVD-RENTAL 手動運用リリカレセクション（tap api経由）',
                'link_url' => 'http://',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '/section/release',
                'section_file_name' => ''
            ],
            [
                'sort' => 3,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 4,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'DVD-RENTAL 自動運用リリカレセクション',
                'link_url' => 'http://',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '/section/release/D045',
                'section_file_name' => ''
            ],
            [
                'sort' => 3,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 5,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'DVD-RENTAL ランキングセクション',
                'link_url' => 'http://',
                'is_tap_on' => 0,
                'is_ranking' => 1,
                'api_url'  => '/section/ranking',
                'section_file_name' => ''
            ],
            [
                'sort' => 4,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 6,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'DVD-RENTAL お気に入りセクション',
                'link_url' => 'http://',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '',
                'section_file_name' => ''
            ],
            [
                'sort' => 4,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 7,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'DVD-RENTAL チェックセクション',
                'link_url' => 'http://',
                'is_tap_on' => 0,
                'is_ranking' => 0,
                'api_url'  => '',
                'section_file_name' => ''
            ],
            [
                'sort' => 4,
                'goods_type' => 1,
                'sale_type' => 1,
                'section_type' => 8,
                'display_start_date' => '2017-01-01',
                'display_end_date' => '2018-01-01',
                'title' => 'DVD-RENTAL レコメンドセクション',
                'link_url' => 'http://',
                'is_tap_on' => 0,
                'is_ranking' => 1,
                'api_url'  => '/section/recommend/ranking',
                'section_file_name' => ''
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
        ]);


    }
}
