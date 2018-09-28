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

    public function import()
    {
        Artisan::call('import', [
            '--test' => 'default',
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
        return [
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
                ]
            ]
        ];
    }

    private static function createSectionJson($goodsType, $saleType, $updateSuffix = null, $addRows = false)
    {
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
                    'productTitle' => '',
                    'jan' => $goodsType . $saleType . '0000001' . $updateSuffix,
                    'himoId'=> $goodsType . $saleType . '0000011' . $updateSuffix,
                    'imageUrl' => '',
                    'displayStartDate' => '2017/12/01 00:00:00',
                    'displayEndDate' => '2025/12/31 23:59:59'
                ],
                [
                    'sort' => 2,
                    'no' => 2,
                    'productTitle' => '',
                    'jan' => $goodsType . $saleType . '0000002',
                    'himoId' => $goodsType . $saleType . '0000012',
                    'imageUrl' => '',
                    'displayStartDate' => '2017/12/01 00:00:00',
                    'displayEndDate' => '2025/12/31 23:59:59'
                ],
                [
                    'sort' => 3,
                    'no' => 3,
                    'productTitle' => '',
                    'jan' => $goodsType . $saleType . '0000003' . $updateSuffix,
                    'himoId' => $goodsType . $saleType . '0000013' . $updateSuffix,
                    'imageUrl' => '',
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
                'himoId' => $goodsType . $saleType . '0000014',
                'imageUrl' => '',
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
                    'displayEndDate' => '2019/01/09 10:00:00'
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
                    'displayEndDate' => '2019/01/09 10:00:00'
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
                    'displayEndDate' => '2019/01/09 10:00:00'
                ]
            ]
        ];
    }

}