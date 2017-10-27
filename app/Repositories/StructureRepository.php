<?php
namespace App\Repositories;

/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/13
 * Time: 15:01
 */

class StructureRepository
{
    const DVD = '1';
    const BOOK = '2';
    const CD = '3';
    const GAME = '4';

    const RENTAL = '1';
    const SELL = '2';

    public function get($goodsType, $saleType) {
        return [
                'totalCount' => 6,
                'limit' => 10,
                'offset' => 0,
                'page' => 1,
                'hasNext' => true,
                'rows' =>
                    [
                        [
                            'sectionId' => 1,
                            'sectionType' => 1,
                            'startDate' => '2017-01-01',
                            'endDate' => '2017-01-01',
                            'image' => [
                                'height' => 130,
                                'width' => 600
                            ],
                            'apiUrl' => '/section/banner/banner_section_1'
                        ],
                        [
                            'sectionId' => 2,
                            'sectionType' => 2,
                            'startDate' => '2017-01-01',
                            'endDate' => '2017-01-01',
                            'title' => '最新のものをチェック！',
                            'linkUrl' => 'https://tsutaya.jp/a.html',
                            'isTapOn' => false,
                            'isRanking' => false,
                            'apiUrl' => '/section/dvd/rental/section_name_1'
                        ],
                        [
                            'sectionId' => 3,
                            'sectionType' => 2,
                            'startDate' => '2017-01-01',
                            'endDate' => '2017-01-01',
                            'title' => '話題作をチェック！',
                            'linkUrl' => 'https://tsutaya.jp/b.html',
                            'isTapOn' => true,
                            'isRanking' => false,
                            'api_url' => '/section/dvd/rental/section_name_2'
                        ],
                        [
                            'sectionId' => 4,
                            'sectionType' => 2,
                            'startDate' => '2017-01-01',
                            'endDate' => '2017-01-01',
                            'title' => '今週の人気ランキング！',
                            'linkUrl' => 'tsutayaapp://ranking/aaaaaa',
                            'isTapOn' => false,
                            'isRanking' => true,
                            'apiUrl' => '/section/dvd/rental/ranking'
                        ],
                        [
                            'sectionId' => 5,
                            'sectionType' => 3
                        ],
                        [
                            'sectionId' => 6,
                            'sectionType' => 4
                        ],
                        [
                            'sectionId' => 7,
                            'sectionType' => 5 // PDMPレコメンドエンジン経由出力
                        ]
                    ]
            ];
    }

    private function convertGoodsTypeToId ($goodsType) {
        switch ($goodsType) {
            case 'dvd':
                return self::DVD;
            case 'book':
                return self::BOOK;
            case 'cd':
                return self::CD;
            case 'game':
                return self::GAME;
            default:
                return false;
        }
    }

    private function convertSaleTypeToId ($saleType) {
        switch ($saleType) {
            case 'rental':
                return self::RENTAL;
            case 'sell':
                return self::SELL;
            default:
                return false;
        }
    }
}