<?php

namespace App\Repositories;

use Illuminate\Support\Carbon;
use App\Model\Structure;
use App\Repositories\TWSRepository;
use App\Repositories\TAPRepository;
use App\Repositories\FixtureRepository;
use App\Repositories\WorkRepository;
use App\Repositories\ReleaseCalenderRepository;
use App\Model\Section;
use App\Model\Banner;
use App\Model\TopReleaseNewest;
use App\Model\TopReleaseLastest;
use App\Model\Product;
use App\Exceptions\NoContentsException;

/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/13
 * Time: 15:02
 */
class SectionRepository extends BaseRepository
{

    protected $section;
    protected $supplementVisible;
    protected $aggregationPeriod;
    protected $rankingTitle;

    const PARAM_MOVIE_GENRE = [1,9,11,12,13];

    public function __construct()
    {
        parent::__construct();
        $this->section = New Section;
    }

    /**
     * @param mixed $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return mixed
     */
    public function getAggregationPeriod()
    {
        return $this->aggregationPeriod;
    }

    /**
     * @return mixed
     */
    public function getRankingTitle()
    {
        return $this->rankingTitle;
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
        $saleTypeRequest = $saleType;
        $saleType = $structureRepository->convertSaleTypeToId($saleType);
        $structureList = $structure->conditionFindFilenameWithDispTime($goodsType, $saleType, $sectionFileName)->getOne();
        if (empty($structureList)) {
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
            // saleTypeは基本リクエストのものをそのまま渡すが、
            if($section->sale_type == WorkRepository::SALE_TYPE_THEATER) {
                $saleTypeTmp = WorkRepository::SALE_TYPE_THEATER;
            } else {
                $saleTypeTmp = $saleTypeRequest;
            }
            $row = [
                'imageUrl' => $section->image_url,
                'title' => $section->title,
                'supplement' => $this->supplementVisible ? '' : $section->supplement, // アーティスト名、著者、機種等
                'code' => $section->code,
                'urlCd' => $section->url_code,
                'workId' => $section->work_id,
                'saleType' => $saleTypeTmp,
            ];
            // Himoに切り替わって、saleType別にてsale_start_dateをアップデートしているのでsale_start_dateに統一
            $row['saleStartDate'] = $structureList->is_release_date == 1 ? $this->dateFormat($section->sale_start_date) : null;
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
                Log::error('Genre code is not found');
                return null;
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
        $tws->setPage($this->page);
        $rows = $tws->ranking($rankingConcentrationCd, $period)->get();
        if (empty($rows['totalResults'])) {
            return null;
        }

        // TWSからセルレンタル区分を取り出す
        $saleType = ($rows['rentalSalesSection'] == '1') ? 'rental' : 'sell';

        // TOL API でlimit/offset処理に問題があるため、一旦50件一括取得（hasnext=false固定）
        $this->aggregationPeriod = $this->aggregationPeriodFormat($rows['totalingPeriod']);
        $this->totalCount = $rows['totalResults'];
        $this->hasNext = false;
        $this->rows = $this->convertFormatFromRanking($rows, $saleType);

        if ($title) {
            $this->rankingTitle = $title;
        }
        if (empty($this->rows)) {
            return null;
        }
        return $this->rows;
    }

    public function aggregationPeriodFormat($totalingPeriod)
    {
        $replacementString = mb_ereg_replace("(月$)|(日\(.\))", '', $totalingPeriod);
        $replacementString = mb_ereg_replace("年|月", '/', $replacementString);
        // 日があった場合は日次の変換
        if (mb_ereg_match('.*～.*', $totalingPeriod)) {
            $explodedArray = explode('～', $replacementString);
            $startDate = date('Y/m/d', strtotime($explodedArray[0]));
            $endDate = date('Y/m/d', strtotime($explodedArray[1]));
            $replacementString = $startDate . '～' . $endDate;
        } else if (mb_ereg_match('.*日(.*)$', $totalingPeriod)) {
            $replacementString = date('Y/m/d', strtotime($replacementString));
        } else {
            $replacementString = date('Y/m', strtotime($replacementString . '/01'));
        }

        // 〜があった場合は週次の変換
        // 上記以外は月次の変換
        return $replacementString;
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

    public function releaseHimo($periodType, $genreId)
    {
        $rows = null;
        if ($periodType === 'newest') {
            $topReleaseNewest = new TopReleaseNewest();
            $rows = $topReleaseNewest->setConditionByGenreId($genreId)->get();
        } else if ($periodType === 'lastest') {
            $topReleaseLastest = new TopReleaseLastest();
            $rows = $topReleaseLastest->setConditionByGenreId($genreId)->get();
        } else {
            return null;
        }
        $formatRowData = $this->convertFormatFromHiMORelease($rows, $periodType);
        $response = [
            'hasNext' => false,
            'totalCount' => count($formatRowData),
            'rows' => $formatRowData,
        ];
        if (empty($response['rows'])) {
            return null;
        }
        return $response;
    }

    /*
     * 成形用メソッド：HiMOからのリリースカレンダーのレスポンスを成形する
     */
    private function convertFormatFromHiMORelease($rows, $periodType)
    {
        $nowMonth = Carbon::now()->startOfMonth();
        $count = 1;
        foreach ($rows as $row) {
            $workRepository = new WorkRepository;
            $releaseCalenderRepository = new ReleaseCalenderRepository;
            if (empty($row)) {
                return null;
            }

            // 作品情報（url_cd）の取得
            $work = $workRepository->get($row->work_id);

            // 販売種別の判定
            $mappingData = $releaseCalenderRepository->genreMapping($row->tap_genre_id);
            $saleType = $mappingData['productSellRentalFlg'];
            $saleType = ($saleType == '1') ? 'sell' : 'rental';

            $isMovie = (in_array($row->tap_genre_id, self::PARAM_MOVIE_GENRE)) ? true : false;

            if ($periodType === 'newest') {
                $dt = new Carbon($row->month);
                if ($dt->eq($nowMonth)) {
                    $from = Carbon::today();
                    $from->startOfWeek();
                } else {
                    $from = Carbon::parse($row->month)->startOfMonth();
                }
                $to = Carbon::parse($row->month)->endOfMonth();
            } else {
                $dt = new Carbon($row->month);
                if ($dt->eq($nowMonth)) {
                    $to = Carbon::today();
                } else {
                    $to = $dt->endOfMonth();
                }
                $from = Carbon::parse($row->month)->startOfMonth();
            }
            // 商品情報の取得
            $productModel = new Product();
            $product = $productModel->setConditionByWorkIdSaleTypeSaleStartDate($row->work_id, $saleType, $from, $to)->getOne();
            if (!empty($product)) {

                $productName = $product->product_name;

                if ((substr($product->item_cd, -2) === '75' && !empty($product->number_of_volume)) ||
                    (substr($product->item_cd, -2) === '76' && !empty($product->number_of_volume))) {
                    $productName = $product->product_name . "（{$product->number_of_volume}）";
                }

                $formattedRow =
                    [
                        'imageUrl' => $product->jacket_l,
                        'title' => $productName,
                        'workTitle' => $work['workTitle'],
                        'workId' => $row->work_id,
                        'code' => $product->product_id,
                        'urlCd' => $work['urlCd'],
                        'sort' => $count,
                        'msdbItem' => $work['msdbItem'],
                    ];
                $formattedRow['saleStartDate'] = $this->dateFormat($product->sale_start_date);
                if (!$this->supplementVisible) {
                    $formattedRow['supplement'] = $work['supplement'];
                } else {
                    $formattedRow['supplement'] = null;
                }
                $formattedRows[] = $formattedRow;
                $count++;
            }
        }

        if (empty($formattedRows)) {
            return null;
        }

        foreach ((array) $formattedRows as $key => $value) {
            $sortSaleStartDate[$key] = $value['saleStartDate'];
            $sortSort[$key] = $value['sort'];
        }

        if ($periodType === 'newest') {
            array_multisort($sortSaleStartDate, SORT_ASC, $sortSort, SORT_ASC, $formattedRows);
        } else {
            array_multisort($sortSaleStartDate, SORT_DESC, $sortSort, SORT_DESC, $formattedRows);
        }

        $index = 0;
        foreach ($formattedRows as $k => $v) {
            if ($index >= 20) unset($formattedRows[$k]);
            $index++;
        }

        // todo ProductのFormatは共通化できそうだったらする。
        foreach ($formattedRows as $formattedRowKey => $formattedRow) {
            // 映像の場合は、ジャケ写を最新刊のブルーレイ優先で取得する。
            if ($formattedRow['msdbItem'] === $workRepository::MSDB_ITEM_VIDEO) {
                // saleTypeの指定がない場合は関係なく出す。
                $jacket = (array)$productModel->setConditionSelectJacket($formattedRow['workId'], $saleType)->getOne();
                // ジャケットがある場合のみ差し替え
                if (count($jacket) > 0) {
                    $formattedRows[$formattedRowKey]['imageUrl'] = $jacket['jacketL'];
                }
            }
        }
        return $formattedRows;
    }

    /*
     * 成形用メソッド：TAPからのリリースカレンダーのレスポンスを成形する
     */
    private function convertFormatFromTAPRelease($rows)
    {
        foreach ($rows as $row) {
            $workRepository = new WorkRepository;
            if (empty($row)) {
                return null;
            }
            $work = $workRepository->get($row['urlCd'], [], '0105');
            $formattedRow =
                [
                    'imageUrl' => $row['imageUrl'],
                    'title' => $row['productName'],
                    'workTitle' => $work['workTitle'],
                    'workId' => $work['workId'],
                    'code' => $row['productId'],
                    'urlCd' => $row['urlCd']
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
                'urlCd' => $row['urlCd']
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
    private function convertFormatFromRanking($rows, $saleType)
    {
        $formattedRows = [];
        $workRepository = new WorkRepository;
        if (empty($rows['entry'])) {
            return null;
        }

        // 作品/商品情報を取得する際の販売区分を指定する
        $workRepository->setSaleType($saleType);

        foreach ($rows['entry'] as $row) {
            $work = $workRepository->get($row['productKey'], [], $this->productKeyType($row['productKey']), false);
            if (empty($work)) {
                continue;
            }
            if (empty($row['lastRankNo'])) {
                $comparison = 'new';
            } else if ($row['rankNo'] == $row['lastRankNo']) {
                $comparison = 'keep';
            } else if ($row['rankNo'] < $row['lastRankNo']) {
                $comparison = 'up';
            } else if ($row['rankNo'] > $row['lastRankNo']) {
                $comparison = 'down';
            }
            $rowUnit['title'] = $row['productTitle'];
            $rowUnit['productTitle'] = $row['productTitle'];
            $rowUnit['workTitle'] = $work['workTitle'];
            $rowUnit['workId'] = $work['workId'];
            $rowUnit['code'] = $row['productKey'];
            $rowUnit['urlCd'] = !empty($row['urlCd']) ? $row['urlCd'] : "";
            $rowUnit['rankNo'] = $row['rankNo'];
            $rowUnit['comparison'] = $comparison;
//            $rowUnit['jacketL'] = $work['jacketL'];
            $rowUnit['jacketL'] = $row['productImage']['large'];
//            $rowUnit['imageUrl'] = $work['jacketL'];
            $rowUnit['imageUrl'] = $row['productImage']['large'];
            $rowUnit['newFlg'] = $work['newFlg'];
            $rowUnit['supplement'] = $work['supplement'];
            $rowUnit['saleType'] = $work['saleType'];
            $rowUnit['itemType'] = $work['itemType'];
            $rowUnit['adultFlg'] = $work['adultFlg'];
//            $rowUnit['saleStartDate'] = $work['saleStartDate'];
            $rowUnit['saleStartDate'] = null;

            // modelNameがあったゲームなので、ゲーム名を取得するようにする。
            if (!$this->supplementVisible) {
                if (array_key_exists('modelName', $row)) {
                    $rowUnit['supplement'] = $row['modelName'];
                } else if (array_key_exists('artistInfoList', $row)) {
                    if ($work['itemType'] === 'dvd') {
                    } else {
                        $rowUnit['supplement'] = $this->getOneArtist($row['artistInfoList']['artistInfo'])['artistName'];
                    }
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

    private function productKeyType($productKey)
    {
        $length = strlen($productKey);
        // rental_product_cd
        if ($length === 9) {
            $idCode = '0206';
            //jan
        } elseif ($length === 13) {
            $idCode = '0205';
        }
        return $idCode;
    }
}
