<?php

namespace tests;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class TestData
{
    public $testDir;

    public function __construct()
    {
        $this->testDir = base_path('tests/Data/fixture/');
    }

    public function getTestDir()
    {
        return $this->testDir;
    }

    public function jsonInitialize()
    {
        File::deleteDirectory($this->testDir, true);
        $jsonDir = [
            'banner',
            'category',
            'category/banner',
            'category/dvd',
            'category/dvd/rental',
            'category/dvd/sell',
            'category/dvd/rental/section',
            'category/dvd/sell/section',
            'category/cd',
            'category/cd/rental',
            'category/cd/sell',
            'category/cd/rental/section',
            'category/cd/sell/section',
            'category/book',
            'category/book/rental',
            'category/book/sell',
            'category/book/rental/section',
            'category/book/sell/section',
            'category/game',
            'category/game/sell',
            'category/game/sell/section',
        ];
        foreach ($jsonDir as $dir) {
            File::makeDirectory($this->testDir . $dir);
        }

        File::put($this->testDir . 'banner/static.json', json_encode($this->createFixedBanner()));

        $baseJsonDir = [
            'category/dvd/rental' => ['goodsType' => 1, 'saleType' => 1],
            'category/dvd/sell' => ['goodsType' => 1, 'saleType' => 2],
            'category/cd/rental' => ['goodsType' => 2, 'saleType' => 1],
            'category/cd/sell' => ['goodsType' => 2, 'saleType' => 2],
            'category/book/rental' => ['goodsType' => 3, 'saleType' => 1],
            'category/book/sell' => ['goodsType' => 3, 'saleType' => 2],
            'category/game/sell' => ['goodsType' => 4, 'saleType' => 2]
        ];
        foreach ($baseJsonDir as $keyDir => $param) {
            File::put($this->testDir . $keyDir . '/base.json', json_encode($this->createBaseJson($param['goodsType'], $param['saleType'])));
            File::put($this->testDir . $keyDir . '/section/' . $param['goodsType'] . '_' . $param['saleType'] . '_2.json',
                json_encode($this->createSectionJson($param['goodsType'], $param['saleType'])));
            File::put($this->testDir . 'category/banner/' . $param['goodsType'] . '_' . $param['saleType'] . '_1.json',
                json_encode($this->createBannerJson($param['goodsType'], $param['saleType'])));
        }
    }

    public function jsonInitializePremium()
    {
        $jsonDir = [
            'category/premium',
            'category/premium/dvd',
            'category/premium/dvd/rental',
            'category/premium/dvd/rental/section',
        ];
        foreach ($jsonDir as $dir) {
            File::makeDirectory($this->testDir . $dir);
        }


        $baseJsonDir = [
            'category/premium/dvd/rental' => ['goodsType' => 5, 'saleType' => 1],
        ];
        foreach ($baseJsonDir as $keyDir => $param) {
            File::put($this->testDir . $keyDir . '/base.json', json_encode($this->createBaseJson($param['goodsType'], $param['saleType'])));
            File::put($this->testDir . $keyDir . '/section/' . $param['goodsType'] . '_' . $param['saleType'] . '_7.json',
                json_encode($this->createSectionJson($param['goodsType'], $param['saleType'])));
        }
    }

    public function import()
    {
        Artisan::call('import', [
//            '--test' => 'default',
            '--dir' => $this->testDir
        ]);
        return true;
    }

    public function updateBaseJson()
    {
        $baseJsonDir = [
            'category/dvd/rental' => ['goodsType' => 1, 'saleType' => 1],
            'category/cd/rental' => ['goodsType' => 2, 'saleType' => 1],
            'category/book/rental' => ['goodsType' => 3, 'saleType' => 1],
            'category/game/sell' => ['goodsType' => 4, 'saleType' => 2]
        ];
        foreach ($baseJsonDir as $keyDir => $param) {
            File::put($this->testDir . $keyDir . '/base.json',
                json_encode($this->createBaseJson($param['goodsType'], $param['saleType'], '_update'))
            );
        }
    }

    public function updateSectionAndBannerJson()
    {
        $baseJsonDir = [
            'category/dvd/rental' => ['goodsType' => 1, 'saleType' => 1],
            'category/cd/rental' => ['goodsType' => 2, 'saleType' => 1],
            'category/book/rental' => ['goodsType' => 3, 'saleType' => 1],
            'category/game/sell' => ['goodsType' => 4, 'saleType' => 2]
        ];
        foreach ($baseJsonDir as $keyDir => $param) {
            File::put($this->testDir . $keyDir . '/section/' . $param['goodsType'] . '_' . $param['saleType'] . '_2.json',
                json_encode($this->createSectionJson($param['goodsType'], $param['saleType'], '_update')));
            File::put($this->testDir . 'category/banner/' . $param['goodsType'] . '_' . $param['saleType'] . '_1.json',
                json_encode($this->createBannerJson($param['goodsType'], $param['saleType'], '_update')));
        }
    }

    public function updateFixedBannerJson()
    {
        File::put($this->testDir . 'banner/static.json', json_encode($this->createFixedBanner('_update')));
    }

    public function addRowSectionAndBannerJson()
    {
        $baseJsonDir = [
            'category/dvd/rental' => ['goodsType' => 1, 'saleType' => 1],
            'category/cd/rental' => ['goodsType' => 2, 'saleType' => 1],
            'category/book/rental' => ['goodsType' => 3, 'saleType' => 1],
            'category/game/sell' => ['goodsType' => 4, 'saleType' => 2]
        ];
        foreach ($baseJsonDir as $keyDir => $param) {
            File::put($this->testDir . $keyDir . '/section/' . $param['goodsType'] . '_' . $param['saleType'] . '_2.json',
                json_encode($this->createSectionJson($param['goodsType'], $param['saleType'], '_update', true)));
            File::put($this->testDir . 'category/banner/' . $param['goodsType'] . '_' . $param['saleType'] . '_1.json',
                json_encode($this->createBannerJson($param['goodsType'], $param['saleType'], '_update', true)));
        }
    }

    private static function createBaseJson($goodsType, $saleType, $updateSuffix = null)
    {
        $data = [
            'goodsType' => $goodsType,
            'saleType' => $saleType,
            'importDateTime' => '2017/12/03 19:00:00',
            'rows' => [
                [
                    'masterType' => 1,
                    'no' => 11,
                    'disp' => 1,
                    'sort' => 1,
                    'isAuto' => 2,
                    'manualFileName' => '',
                    'sectionType' => 1,
                    'displayStartDate' => '2017/11/24 12:00:00',
                    'displayEndDate' => '2025/12/31 23:59:59',
                    'title' => $goodsType . '_' . $saleType . '_1' . $updateSuffix,
                    'linkUrl' => '',
                    'isTapOn' => 1,
                    'apiUrl' => 'section/banner/',
                    'sectionFileName' => $goodsType . '_' . $saleType . '_1',
                    'bannerHeight' => 140,
                    'bannerWidth' => 750
                ],
                [
                    'masterType' => 2,
                    'no' => 10,
                    'disp' => 2,
                    'sort' => 2,
                    'isAuto' => 2,
                    'manualFileName' => '',
                    'sectionType' => 2,
                    'displayStartDate' => '2017/11/24 12:00:00',
                    'displayEndDate' => '2025/12/31 23:59:59',
                    'title' => $goodsType . '_' . $saleType . '_2' . $updateSuffix,
                    'linkUrl' => '',
                    'isTapOn' => 1,
                    'apiUrl' => 'section/book/rental/',
                    'sectionFileName' => $goodsType . '_' . $saleType . '_2',
                ],
                [
                    'masterType' => 3,
                    'no' => 1,
                    'disp' => 1,
                    'sort' => 3,
                    'isAuto' => 1,
                    'manualFileName' => '',
                    'sectionType' => 3,
                    'displayStartDate' => '2017/11/24 12:00:00',
                    'displayEndDate' => '2025/12/31 23:59:59',
                    'title' => $goodsType . '_' . $saleType . '_3' . $updateSuffix,
                    'linkUrl' => '',
                    'isTapOn' => 0,
                    'isRanking' => 0,
                    'apiUrl' => 'testApi'
                ],
                [
                    'masterType' => 3,
                    'no' => 1,
                    'disp' => 1,
                    'sort' => 4,
                    'isAuto' => 1,
                    'manualFileName' => '',
                    'sectionType' => 4,
                    'displayStartDate' => '2017/11/24 12:00:00',
                    'displayEndDate' => '2025/12/31 23:59:59',
                    'title' => $goodsType . '_' . $saleType . '_4' . $updateSuffix,
                    'linkUrl' => '',
                    'isTapOn' => 0,
                    'isRanking' => 0,
                    'apiUrl' => 'testApi'
                ],
                [
                    'masterType' => 3,
                    'no' => 1,
                    'disp' => 1,
                    'sort' => 5,
                    'isAuto' => 1,
                    'manualFileName' => '',
                    'sectionType' => 5,
                    'displayStartDate' => '2017/11/24 12:00:00',
                    'displayEndDate' => '2025/12/31 23:59:59',
                    'title' => $goodsType . '_' . $saleType . '_5' . $updateSuffix,
                    'linkUrl' => '',
                    'isTapOn' => 0,
                    'isRanking' => 0,
                    'apiUrl' => 'PDMPAPI'
                ],
                [
                    'masterType' => 2,
                    'no' => 1,
                    'disp' => 1,
                    'sort' => 5,
                    'isAuto' => 1,
                    'manualFileName' => '',
                    'sectionType' => 6,
                    'displayStartDate' => '2017/11/24 12:00:00',
                    'displayEndDate' => '2025/12/31 23:59:59',
                    'title' => $goodsType . '_' . $saleType . '_6' . $updateSuffix,
                    'linkUrl' => '',
                    'isTapOn' => 0,
                    'isRanking' => 0,
                    'apiUrl' => 'section/premium/dvd/rental/recommend',
                ],
                [
                    'masterType' => 2,
                    'no' => 1,
                    'disp' => 1,
                    'sort' => 5,
                    'isAuto' => 1,
                    'manualFileName' => '',
                    'sectionType' => 7,
                    'displayStartDate' => '2017/11/24 12:00:00',
                    'displayEndDate' => '2025/12/31 23:59:59',
                    'title' => $goodsType . '_' . $saleType . '_7' . $updateSuffix,
                    'linkUrl' => '',
                    'isTapOn' => 0,
                    'isRanking' => 0,
                    'apiUrl' => 'section/premium/dvd/rental/',
                    'sectionFileName' => $goodsType . '_' . $saleType . '_7'
                ]
            ]
        ];
        return $data;
    }

    private static function createSectionJson($goodsType, $saleType, $updateSuffix = null, $addRows = false)
    {
        if ($goodsType === 1) {
            if ($saleType === 1) {
                $jan[0] = '089937132'; // キングダム 19
                $jan[1] = '082394367'; // 黒子のバスケ 9
                $jan[2] = '089939640'; // 尾根のかなたに ～父と息子の日航機墜落事故～ 後編
                $text = 'text_1_1';
                $subtitle = 'subtitle_1_1';
                $linkUrl = 'linkUrl_1_1';
            } else {
                $jan[0] = '4988142453822'; // ダイ・ハード 2
                $jan[1] = '4988111144690'; // ウォーキング・デッド3 Blu-ray BOX-2
                $jan[2] = '4959241980366'; // 千と千尋の神隠し
                $text = 'text_1_2';
                $subtitle = 'subtitle_1_2';
                $linkUrl = 'linkUrl_1_2';
            }
        } elseif ($goodsType === 2) {
            if ($saleType === 1) {
                $jan[0] = '005634334'; // スリラー
                $jan[1] = '005773147'; // 和と洋
                $jan[2] = '005841435'; // 11月のアンクレット(通常盤A)
                $text = 'text_2_1';
                $subtitle = 'subtitle_2_1';
                $linkUrl = 'linkUrl_2_1';
            } else {
                $jan[0] = '4988003508821'; // #好きなんだ(A)
                $jan[1] = '4547366354164'; // シンクロニシティ(C)
                $jan[2] = '4547366377972'; // レイメイ(期間生産限定盤)
                $text = 'text_2_2';
                $subtitle = 'subtitle_2_2';
                $linkUrl = 'linkUrl_2_2';
            }

        } elseif ($goodsType === 3) {
            if ($saleType === 1) {
                $jan[0] = '102421256'; // ジョジョの奇妙な冒険 Part6 ストーンオーシャン11
                $jan[1] = '103390522'; // キングダム
                $jan[2] = '103388956'; // 進撃の巨人
                $text = 'text_3_1';
                $subtitle = 'subtitle_3_1';
                $linkUrl = 'linkUrl_3_1';
            } else {
                $jan[0] = '9784063970494'; // 進撃の巨人<限定版> DVD付き
                $jan[1] = '9784088814964'; // ONE PIECE
                $jan[2] = '9784592144403'; // ベルセルク
                $text = 'text_3_2';
                $subtitle = 'subtitle_3_2';
                $linkUrl = 'linkUrl_3_2';
            }

        } elseif ($goodsType === 4) {
            $jan[0] = '4976219095631'; // モンスターハンター:ワールド BestPrice
            $jan[1] = '4938833022950'; // Battlefield V
            $jan[2] = '4571237660672'; // 妖怪ウォッチバスターズ 白犬隊
            $text = 'text_4_1';
            $subtitle = 'subtitle_4_1';
            $linkUrl = 'linkUrl_4_1';
        } elseif ($goodsType === 5) {
            $jan[0] = '089937132'; // キングダム 19
            $jan[1] = '082394367'; // 黒子のバスケ 9
            $jan[2] = '089939640'; // 尾根のかなたに ～父と息子の日航機墜落事故～ 後編
            $text = 'text_5_1';
            $subtitle = 'subtitle_5_1';
            $linkUrl = 'linkUrl_5_1';
        }
        $data = [
            'goodsType' => $goodsType,
            'saleType' => $saleType,
            'specialTitle' => ' リリース情報_コミックレンタル',
            'isReleaseDate' => '1',
            'importDateTime' => '2017/12/01 10:00:00',
            'rows' => [
                [
                    'sort' => 1,
                    'no' => 1,
                    'productTitle' => $goodsType . $saleType . '0000001' . $updateSuffix,
                    'jan' => $jan[0],
                    'imageUrl' => '',
                    'text' => $text . '_01',
                    'subtitle' => $subtitle . '_01',
                    'linkUrl' => $linkUrl . '_01',
                    'isTapOn' => 0,
                    'displayStartDate' => '2017/12/01 00:00:00',
                    'displayEndDate' => '2025/12/31 23:59:59'
                ],
                [
                    'sort' => 2,
                    'no' => 2,
                    'productTitle' => $goodsType . $saleType . '0000002',
                    'jan' => $jan[1],
                    'imageUrl' => '',
                    'text' => $text . '_02',
                    'subtitle' => $subtitle . '_02',
                    'linkUrl' => $linkUrl . '_02',
                    'isTapOn' => 1,
                    'displayStartDate' => '2017/12/01 00:00:00',
                    'displayEndDate' => '2025/12/31 23:59:59'
                ],
                [
                    'sort' => 3,
                    'no' => 3,
                    'productTitle' => $goodsType . $saleType . '0000003' . $updateSuffix,
                    'jan' => $jan[2],
                    'imageUrl' => '',
                    'text' => $text . '_03',
                    'subtitle' => $subtitle . '_03',
                    'linkUrl' => $linkUrl . '_03',
                    'isTapOn' => 0,
                    'displayStartDate' => '2017/12/01 00:00:00',
                    'displayEndDate' => '2025/12/31 23:59:59'
                ]
            ]
        ];
        if ($addRows) {
            $data['rows'][] = [
                'sort' => 4,
                'no' => 4,
                'productTitle' => '',
                'jan' => $goodsType . $saleType . '0000004',
                'imageUrl' => '',
                'text' => $text . '_04',
                'subtitle' => $subtitle . '_04',
                'linkUrl' => $linkUrl . '_04',
                'isTapOn' => 1,
                'displayStartDate' => '2017/12/01 00:00:00',
                'displayEndDate' => '2025/12/31 23:59:59'
            ];
        }

        return $data;
    }

    private static function createBannerJson($goodsType, $saleType, $updateSuffix = null, $addRows = false)
    {
        $data = [
            'bannerTitle' => '',
            'importDateTime' => '2017/12/01 10:00:00',
            'rows' => [
                [
                    'no' => 1,
                    'sort' => 1,
                    'projectName' => '',
                    'imageUrl' => 'banner_1_' . $goodsType . '_' . $saleType,
                    'linkUrl' => '',
                    'isTapOn' => 1,
                    'loginType' => 2,
                    'displayStartDate' => '2017/12/01 10:00:00',
                    'displayEndDate' => '2025/12/31 23:59:59'
                ],
                [
                    'no' => 2,
                    'sort' => 2,
                    'projectName' => '',
                    'imageUrl' => 'banner_2_' . $goodsType . '_' . $saleType . $updateSuffix,
                    'linkUrl' => '',
                    'isTapOn' => 1,
                    'loginType' => 2,
                    'displayStartDate' => '2017/12/01 10:00:00',
                    'displayEndDate' => '2025/12/31 23:59:59'
                ]
            ]
        ];
        if ($addRows) {
            $data['rows'][] = [
                'no' => 3,
                'sort' => 3,
                'projectName' => '',
                'imageUrl' => 'banner_3_' . $goodsType . '_' . $saleType . $updateSuffix,
                'linkUrl' => '',
                'isTapOn' => 1,
                'loginType' => 2,
                'displayStartDate' => '2017/12/01 10:00:00',
                'displayEndDate' => '2025/12/31 23:59:59'
            ];
        }
        return $data;
    }

    private static function createFixedBanner($updateSuffix = null)
    {
        return [
            'bannerHeight' => '140',
            'bannerWidth' => '750',
            'bannerTitle' => '固定枠バナー',
            'displayStartDate' => '2000/01/01 00:00:00',
            'displayEndDate' => '2025/12/31 23:59:59',
            'importDateTime' => '2017/12/01 10:00:00',
            'rows' => [
                [
                    'no' => 1,
                    'sort' => 1,
                    'projectName' => 'fixed_1',
                    'imageUrl' => 'fixed_1' . $updateSuffix,
                    'linkUrl' => '',
                    'isTapOn' => 1,
                    'loginType' => 0,
                    'displayStartDate' => '2017/12/01 10:00:00',
                    'displayEndDate' => '2025/01/09 10:00:00'
                ],
                [
                    'no' => 2,
                    'sort' => 2,
                    'projectName' => 'fixed_2',
                    'imageUrl' => 'fixed_2' . $updateSuffix,
                    'linkUrl' => '',
                    'isTapOn' => 0,
                    'loginType' => 1,
                    'displayStartDate' => '2017/12/01 10:00:00',
                    'displayEndDate' => '2025/01/09 10:00:00'
                ],
                [
                    'no' => 2,
                    'sort' => 2,
                    'projectName' => 'fixed_3',
                    'imageUrl' => 'fixed_3',
                    'linkUrl' => '',
                    'isTapOn' => 0,
                    'loginType' => 2,
                    'displayStartDate' => '2017/12/01 10:00:00',
                    'displayEndDate' => '2025/01/09 10:00:00'
                ]
            ]
        ];
    }

}