<?php

namespace App\Repositories;

use App\Model\Structure;
use App\Repositories\TWSRepository;
use App\Repositories\TAPRepository;
use App\Repositories\SectionRepository;
use App\Repositories\FixtureRepository;
use App\Model\Section;
use App\Model\Banner;
use App\Exceptions\NoContentsException;

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
    protected $supplementVisible;

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

    /**
     * @param mixed $supplementVisible
     */
    public function setSupplementVisible($supplementVisible)
    {
        if (!is_bool($supplementVisible)) {
            if ($supplementVisible === 'true') {
                $this->supplementVisible = true;
            } else if ($supplementVisible === 'false') {
                $this->supplementVisible = false;
            }
        } else {
            $this->supplementVisible = $supplementVisible;
        }
    }

    public function normal($goodsType, $saleType, $sectionFileName)
    {
        $rows = null;
        $sections = [];
        $structureRepository = new StructureRepository();
        $structure = new Structure();
        $goodsType = $structureRepository->convertGoodsTypeToId($goodsType);
        $saleType = $structureRepository->convertSaleTypeToId($saleType);
        $structureList = $structure->conditionFindFilenameWithDispTime($goodsType, $saleType, $sectionFileName)->getOne();
        if(count($structureList) == 0) {
            $this->totalCount = 0;
        } else {
            $this->section->setConditionByTsStructureId($structureList->id);
            $this->totalCount = $this->section->count();
            $sections = $this->section->get();
        }
        if (count($sections) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }
        foreach ($sections as $section) {
            $row = [
                // 'saleStartDate' => $this->dateFormat($section->sale_start_date),
                'imageUrl' => $section->image_url,
                'title' => $section->title,
                'supplement' => $this->supplementVisible ? '' : $section->supplement, // アーティスト名、著者、機種等
                'code' => $section->code,
                'urlCode' => $section->url_code
            ];
            if ($saleType == $structureRepository::RENTAL) {
                $row['saleStartDate'] = $structureList->is_release_date == 1 ? $this->dateFormat($section->rental_start_date) : null;
            } else {
                $row['saleStartDate'] = $structureList->is_release_date == 1 ? $this->dateFormat($section->sale_start_date) : null;
            }

            $rows[] = $row;
        }
        $this->rows = $rows;
        return $this;
    }

    public function ranking($codeType, $genreCode, $period)
    {
        $title = null;
        // himoだった場合は集約コードに変更する
        if ($codeType == 'himo') {
            $fixtureRepository = new FixtureRepository;
            $genreMap = $fixtureRepository->getGenreMap();
            if (key_exists($genreCode, $genreMap)) {
                $rankingConcentrationCd = $genreMap[$genreCode]['AggregationCode'];
            } else {
                throw new NoContentsException();
            }
            $title = $genreMap[$genreCode]['HimoBigGenreName'] . ':' . $genreMap[$genreCode]['HimoMiddleGenreName'];
        } else {
            $rankingConcentrationCd = $genreCode;
        }

        if ($period) {
            $period = $this->getPeriod($period);
        } else {
            $period = null;
        }
        $tws = new TWSRepository;
        $tws->setLimit($this->limit);
        $rows = $tws->ranking($rankingConcentrationCd, $period)->get();
        $response = [
            'hasNext' => null,
            'totalCount' => $rows['totalResults'],
            'rows' => $this->convertFormatFromRanking($rows),
        ];
        if ($title) {
            $response['title'] = $title;
        }
        if (empty($response['rows'])) {
            return null;
        }
        return $response;
    }


    // 01:レンタルDVD 02:レンタルCD 03:レンタルコミック 04:販売DVD 05:販売CD 06:販売ゲーム 07:販売本・コミック
    public function releaseManual($category, $releaseDateTo)
    {
        $tap = new TAPRepository;
        $rows = $tap->release($category, $releaseDateTo)->get();
        if (!array_key_exists('release', $rows)) {
            throw new NoContentsException();
        }
        $response = [
            'hasNext' => false,
            'totalCount' => $rows['count'],
            'rows' => $this->convertFormatFromTAPRelease($rows['release']),
        ];
        if (empty($response['rows'])) {
            return null;
        }
        return $response;
    }

    public function releaseAuto($genreId, $storeProductItemCd)
    {
        $tws = new TWSRepository;
        $rows = $tws->release($genreId, $storeProductItemCd)->get();
        $response = [
            'hasNext' => false,
            'totalCount' => $rows['totalResults'],
            'rows' => $this->convertFormatFromTWSRelease($rows),
        ];
        if (empty($response['rows'])) {
            return null;
        }
        return $response;
    }

    /*
     * 成形用メソッド：TAPからのリリースカレンダーのレスポンスを成形する
     */
    private function convertFormatFromTAPRelease($rows)
    {
        foreach ($rows as $row) {
            if (empty($row)) {
                return null;
            }
            $formattedRow =
                [
                    'imageUrl' => $row['imageUrl'],
                    'title' => $row['productName'],
                    'code' => $row['productId'],
                    'urlCode' => $row['urlCd']
                ];
            if (array_key_exists('releaseDate', $row)) {
                $formattedRow['saleStartDate'] = $this->dateFormat($row['releaseDate']);
            } else {
                $formattedRow['saleStartDate'] = null;
            }
            if (!$this->supplementVisible) {
                if (array_key_exists('cast', $row)) {
                    $formattedRow['supplement'] = $row['cast'];
                } else if (array_key_exists('model', $row)) {
                    $formattedRow['supplement'] = $row['model'];
                }
            } else {
                $formattedRow['supplement'] = null;
            }
            $formattedRows[] = $formattedRow;

        }
        return $formattedRows;
    }


    /*
     * 成形用メソッド：TWSからのリリースカレンダーのレスポンスを成形する
     */
    private function convertFormatFromTWSRelease($rows)
    {
        foreach ($rows['entry'] as $row) {
            if (empty($row)) {
                return null;
            }
            $formattedRow = [
//                'saleStartDate' => $this->dateFormat($row['saleDate']),
                'saleStartDate' => null, // リリース情報のみの出力するように変更。
                'imageUrl' => $row['image']['large'],
                'title' => $row['productName'],
//                'code' => $row['janCd'],
                'code' => $row['productKey'],
                'urlCode' => $row['urlCd']
            ];
            if (!$this->supplementVisible) {
                if (array_key_exists('artistList', $row)) {
                    $formattedRow['supplement'] = $row['artistList'][0]['artistName'];
                } else {
                    $formattedRow['supplement'] = null;
                }
            } else {
                $formattedRow['supplement'] = null;
            }
            $formattedRows[] = $formattedRow;
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
            $rowUnit = [
                'saleStartDate' => null,
                'imageUrl' => $row['productImage']['large'],
                'title' => $row['productTitle'],
                'code' => $row['productKey'],
                'urlCode' => $row['urlCd']
            ];
            // modelNameがあったゲームなので、ゲーム名を取得するようにする。
            if (!$this->supplementVisible) {
                if (array_key_exists('modelName', $row)) {
                    $rowUnit['supplement'] = $row['modelName'];
                } else if (array_key_exists('artistInfoList', $row)) {
                    $rowUnit['supplement'] = $this->getOneArtist($row['artistInfoList']['artistInfo'])['artistName'];
                } else {
                    $rowUnit['supplement'] = null;
                }
            } else {
                $rowUnit['supplement'] = null;
            }
            $formattedRows[] = $rowUnit;
        }
        return $formattedRows;
    }

    public function getOneArtist($data)
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

    private function dateFormat($date)
    {
        if (!empty($date) && $date != '0000-00-00 00:00:00') {
            return date('Y-m-d', strtotime($date));
        } else {
            return null;
        }
    }
}