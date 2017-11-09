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

    /**
     * @return mixed
     */
    public function getHasNext()
    {
        return $this->hasNext;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return (int)$this->limit;
    }

    /**
     * @return mixed
     */
    public function getOffset()
    {
        return (int)$this->offset;
    }

    /**
     * @return mixed
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @return Array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @param mixed $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    public function fixedBanner()
    {
        $rows = [
            [
                'linkUrl' => 'https://tsutaya.jp/a.html',
                'imageUrl' => 'https://tsutaya.jp/image/a.jpg',
                'isTapOn' => false

            ],
            [
                'linkUrl' => 'https://tsutaya.jp/b.html',
                'imageUrl' => 'https://tsutaya.jp/image/b.jpg',
                'isTapOn' => true

            ],
            [
                'linkUrl' => 'https://tsutaya.jp/c.html',
                'imageUrl' => 'https://tsutaya.jp/image/d.jpg',
                'isTapOn' => false

            ]
        ];
        return $rows;
    }

    public function normal($goodsType, $saleType, $sectionName)
    {
        $structureRepository = new StructureRepository();
        $goodsType = $structureRepository->convertGoodsTypeToId($goodsType);
        $saleType = $structureRepository->convertSaleTypeToId($saleType);
        $this->section->conditionSectionFromStructure($goodsType, $saleType, $sectionName);
        $this->totalCount = $this->section->count();
        $sections = $this->section->get();
        if (count($sections) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }
        foreach ($sections as $section) {
            $rows[] =
                [
                    'saleStartDate' => $section->rental_start_date,
                    'rentalStartDate' => $section->sale_start_date,
                    'imageUrl' => $section->image_url,
                    'title' => $section->title,
                    'supplement' => $section->supplement, // アーティスト名、著者、機種等
                    'code' => $section->code,
                    'urlCode' => $section->url_code
                ];

        }
        $this->rows = $rows;
        return $this;
    }

    public function banner($sectionName)
    {
        $rows = [
            [
                'linkUrl' => 'https://tsutaya.jp/a.html',
                'imageUrl' => 'https://tsutaya.jp/image/a.jpg',
                'isTapOn' => false

            ],
            [
                'linkUrl' => 'https://tsutaya.jp/b.html',
                'imageUrl' => 'https://tsutaya.jp/image/b.jpg',
                'isTapOn' => true

            ],
            [
                'linkUrl' => 'https://tsutaya.jp/c.html',
                'imageUrl' => 'https://tsutaya.jp/image/d.jpg',
                'isTapOn' => false

            ]
        ];
        return $rows;
    }

    public function ranking($codeType, $genreCode, $period)
    {
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
        $rows = $tws->ranking($rankingConcentrationCd, $period)->get();
        $response = [
            'hasNext' => null,
            'totalCount' => null,
            'rows' => $this->convertFormatFromRanking($rows),
        ];
        if (empty($response['rows'])) {
            return null;
        }
        return $response;
    }


    // 01:レンタルDVD 02:レンタルCD 03:レンタルコミック 04:販売DVD 05:販売CD 06:販売ゲーム 07:販売本・コミック
    public function releaseManual()
    {

    }

    public function releaseAuto($genreId, $storeProductItemCd, $itemCode)
    {
        $tws = new TWSRepository;
        $rows = $tws->release($genreId, $storeProductItemCd, $itemCode)->get();
        $response = [
            'hasNext' => null,
            'totalCount' => $rows['totalResults'],
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
    private function convertFormatFromRelease($rows)
    {
        foreach ($rows['entry'] as $row) {
            if (empty($row)) {
                return null;
            }
            $formattedRows[] =
                [
                    'saleStartDate' => $row['saleDate'],
                    'rentalStartDate' => null,
                    'imageUrl' => $row['image']['large'],
                    'title' => $row['productName'],
                    'supplement' => $row['artistList'][0]['artistName'], // アーティスト名、著者、機種等
                    'code' => $row['janCd'],
                    'urlCode' => $row['urlCd']
                ];
        }
        return $formattedRows;
    }

    /*
     * 成形用メソッド：TWSからのランキングのレスポンスを成形する
     */
    private function convertFormatFromRanking($rows)
    {
        foreach ($rows['entry'] as $row) {
            if (empty($row)) {
                return null;
            }
            $formattedRows[] =
                [
                    'saleStartDate' => null,
                    'rentalStartDate' => null,
                    'imageUrl' => $row['productImage']['large'],
                    'title' => $row['productTitle'],
                    'supplement' => $this->getOneArtist($row['artistInfoList']['artistInfo'])['artistName'], // アーティスト名、著者、機種等
                    'code' => $row['productKey'],
                    'urlCode' => $row['urlCd']
                ];
        }
        return $formattedRows;
    }

    private function getOneArtist($data)
    {
        if (array_key_exists('0', $data)) {
            $artist = array_values($data)[0];
        } else {
            $artist = $data;
        }
        return $artist;
    }

    private function getPeriod($period)
    {
        $targetDay = date("Ym01");
        return date("Ym01", strtotime($targetDay . ' -' . $period . ' month'));
    }

    // DB登録後の情報取得処理
    public function updateProductInfo()
    {
        $sections = $this->section->conditionAll()->get();
        $tws = new TWSRepository;
        foreach ($sections as $sectionRow) {
            $res = $tws->detail($sectionRow->code)->get();
            $updateValues = [
                'title' => $res['entry']['productName'],
                'image_url' => $res['entry']['image']['large'],
                'url_code' => $res['entry']['urlCd'],
                'sale_start_date' => $res['entry']['saleDate'],
                'supplement' => $this->getOneArtist($res['entry']['artistInfo'])['artistName']
            ];
            $this->section->update($sectionRow->id, $updateValues);
        }
    }
}