<?php
namespace App\Repositories;

use App\Model\Structure;
use App\Repositories\TWSRepository;
use App\Repositories\SectionRepository;
use App\Model\Section;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/13
 * Time: 15:02
 */

class SectionRepository
{

    protected $section;
    protected $limit;
    protected $offset;
    protected $hasNext;
    protected $totalCount;
    protected $page;
    protected $rows;


    public function __construct()
    {
        $this->section = New Section;
    }

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

    public function normal($goodsType, $saleType, $sectionName) {
//        $structureRepository = new StructureRepository();
//        $goodsType = $structureRepository->convertGoodsTypeToId($goodsType);
//        $saleType = $structureRepository->convertSaleTypeToId($saleType);
//
//        $this->section->set($goodsType, $saleType, $sectionName);
//        $count = $this->section->count();

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

    public function banner($sectionName) {
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

    public function ranking($codeType, $genreCode, $period) {
        // himoだった場合は集約コードに変更する
        if ($codeType == 'himo') {
            $genreMap = config('genre_map');
            if (key_exists($genreCode, $genreMap)) {
                $rankingConcentrationCd = $genreMap[$genreCode];
            } else {
                throw new NotFoundHttpException();
            }
        } else {
            $rankingConcentrationCd = $genreCode;
        }

        if ($period) {
            $period = $this->getPeriod($period);
        } else {
            $period = null;
        }
        $tws = new TWSRepository;
        $rows =$tws->ranking($rankingConcentrationCd, $period)->get();
        $response = [
            'hasNext' => null,
            'totalCount' => null,
            'limit' => null,
            'offset' => null,
            'page' => null,
            'rows' =>  $this->convertFormatFromRanking($rows),
        ];
        if (empty($response['rows'])) {
            return null;
        }
        return $response;
    }


    // 01:レンタルDVD 02:レンタルCD 03:レンタルコミック 04:販売DVD 05:販売CD 06:販売ゲーム 07:販売本・コミック
    public function releaseManual() {

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
        if (empty($response['rows'])) {
            return null;
        }
        return $response;
    }

    /*
     * 成形用メソッド：TWSからのリリースカレンダーのレスポンスを成形する
     */
    private function convertFormatFromRelease($rows) {
        foreach ($rows['entry'] as $row) {
            if (empty($row)) {
                return null;
            }
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
            if (empty($row)) {
                return null;
            }
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

    private function getPeriod ($period) {
        $targetDay = date("Ym01");
        return date("Ym01",strtotime($targetDay . ' -' .$period.' month'));
    }
}