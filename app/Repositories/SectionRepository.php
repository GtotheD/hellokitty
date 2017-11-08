<?php
namespace App\Repositories;

use App\Repositories\TWSRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/13
 * Time: 15:02
 */

class SectionRepository
{

    public function fixedBanner() {
        $rows = [
            [
                'linkUrl' => 'https://tsutaya.jp/a.html',
                'imageUrl'=> 'https://tsutaya.jp/image/a.jpg',
                'isTapOn' => false

            ],
            [
                'linkUrl' => 'https://tsutaya.jp/b.html',
                'imageUrl'=> 'https://tsutaya.jp/image/b.jpg',
                'isTapOn' => true

            ],
            [
                'linkUrl' => 'https://tsutaya.jp/c.html',
                'imageUrl'=> 'https://tsutaya.jp/image/d.jpg',
                'isTapOn' => false

            ]
        ];
        return $rows;
    }

    public function normal($goodsName, $typeName, $sectionName) {

        $rows = [
            'hasNext' => null,
            'totalCount' => null,
            'limit' => null,
            'offset' => null,
            'page' => null,
            'rows' => [
                [
                    'saleStartDate'=> '2017-01-01',
                    'rentalStartDate'=> '2017-01-01',
                    'imageUrl'=> 'https://tsutaya.jp/image/a.jpg',
                    'title' => 'ラ・ラ・ランド',
                    'supplement' => 'エマ・ストーン', // アーティスト名、著者、機種等
                    'code' => 'JAN_CODE',
                    'urlCode' => 'url code'
                ],
                [
                    'saleStartDate'=> '2017-01-01',
                    'rentalStartDate'=> '2017-01-01',
                    'imageUrl'=> 'https://tsutaya.jp/image/a.jpg',
                    'title' => 'ワイルド・スピード　ＩＣＥ　ＢＲＥＡＫ',
                    'supplement' => 'ヴィン・ディーゼル', // アーティスト名、著者、機種等
                    'code' => 'JAN_CODE',
                    'urlCode' => 'url code'
                ],
                [
                    'saleStartDate'=> '2017-01-01',
                    'rentalStartDate'=> '2017-01-01',
                    'imageUrl'=> 'https://tsutaya.jp/image/a.jpg',
                    'title' => '美女と野獣',
                    'supplement' => 'エマ・ワトソン', // アーティスト名、著者、機種等
                    'code' => 'JAN_CODE',
                    'urlCode' => 'url code'
                ],
            ]
        ];
        return $rows;
    }

    public function banner($goodsName, $typeName, $sectionName) {
        $rows = [
            [
                'linkUrl' => 'https://tsutaya.jp/a.html',
                'imageUrl'=> 'https://tsutaya.jp/image/a.jpg',
                'isTapOn' => false

            ],
            [
                'linkUrl' => 'https://tsutaya.jp/b.html',
                'imageUrl'=> 'https://tsutaya.jp/image/b.jpg',
                'isTapOn' => true

            ],
            [
                'linkUrl' => 'https://tsutaya.jp/c.html',
                'imageUrl'=> 'https://tsutaya.jp/image/d.jpg',
                'isTapOn' => false

            ]
        ];
        return $rows;
    }

    public function ranking($genreCode) {
        $genreMap = config('genre_map');
        if (key_exists($genreCode, $genreMap)) {
            $rankingConcentrationCd = $genreMap[$genreCode];
        } else {
            throw new NotFoundHttpException();
        }
        $tws = new TWSRepository;
        $rows =$tws->ranking($rankingConcentrationCd)->get();
//        return $tws->ranking($rankingConcentrationCd)->get();
        $response = [
            'hasNext' => null,
            'totalCount' => null,
            'limit' => null,
            'offset' => null,
            'page' => null,
            'rows' =>  $this->convertFormatFromRanking($rows),
        ];
        return $response;
    }

    public function releaseAuto($genreId, $storeProductItemCd, $itemCode) {
//        dd($genreId, $storeProductItemCd, $itemCode);
        $tws = new TWSRepository;
        $rows = $tws->release($genreId, $storeProductItemCd, $itemCode)->get();
        $response = [
            'hasNext' => null,
            'totalCount' => $rows['totalResults'],
            'limit' => null,
            'offset' => null,
            'page' => null,
            'rows' => $this->convertFormatFromRelease($rows),
        ];
        return $response;
    }

    /*
     * 成形用メソッド：TWSからのリリースカレンダーのレスポンスを成形する
     */
    private function convertFormatFromRelease($rows) {
        foreach ($rows['entry'] as $row) {
            $formatedRows[] =
                [
                    'saleStartDate'=> $row['saleDate'],
                    'rentalStartDate'=> null,
                    'imageUrl'=> $row['image']['large'],
                    'title' => $row['productName'],
                    'supplement' => $row['artistList'][0]['artistName'], // アーティスト名、著者、機種等
                    'code' => $row['janCd'],
                    'urlCode' => $row['urlCd']
                ];
        }
        return $formatedRows;
    }

    /*
     * 成形用メソッド：TWSからのランキングのレスポンスを成形する
     */
    private function convertFormatFromRanking($rows) {
        foreach ($rows['entry'] as $row) {
            $formatedRows[] =
                [
                    'saleStartDate'=> null,
                    'rentalStartDate'=> null,
                    'imageUrl'=> $row['productImage']['large'],
                    'title' => $row['productTitle'],
                    'supplement' => $this->getOneArtist($row['artistInfoList']['artistInfo'])['artistName'], // アーティスト名、著者、機種等
                    // todo: １人の場合はレスポンス形式が異なる為、成形ロジックは別で実装する
                    'code' => $row['productKey'],
                    'urlCode' => $row['urlCd']
                ];
        }
        return $formatedRows;
    }

    private function getOneArtist($data) {
        if (array_key_exists('0', $data)) {
            $artist = array_values($data)[0];
        } else {
            $artist = $data;
        }
        return $artist;
    }
}