<?php

namespace App\Repositories;

use App\Exceptions\NoContentsException;
use App\Model\RelateadWork;
use App\Model\Work;
use App\Model\HimoReleaseOrder;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class ReleaseCalenderRepository
{

    protected $sort;
    protected $offset;
    protected $limit;
    protected $saleType;
    protected $rows;
    protected $totalCount;
    protected $hasNext;
    protected $genreId;
    protected $month;
    protected $onlyReleased;
    protected $mediaFormat;

    const HIMO_TAP_RECOMMEND_KEYWORD = 'riricaleinfo';

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        $this->sort = $sort;
        $this->offset = $offset;
        $this->limit = $limit;
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
     * @param mixed $offset
     */
    public function setGenreId($genreId)
    {
        $this->genreId = $genreId;
    }


    /**
     * @param mixed $month
     */
    public function setMonth($month)
    {
        $this->month = $month;
    }

    /**
     * @param mixed $onlyReleased
     */
    public function setOnlyReleased($onlyReleased)
    {
        $this->onlyReleased = $onlyReleased;
    }

    /**
     * @param string $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }


    /**
     * @param mixed $mediaFormat
     */
    public function setMediaFormat($mediaFormat)
    {
        $this->mediaFormat = $mediaFormat;
    }

    public function get()
    {
        // 音楽の場合single albumなどを指定する為。
        $mediaFormat = null;
        $workRepository = new WorkRepository();
        $himoReleaseOrder = new HimoReleaseOrder();

        // パラメーターを取得する　未指定の場合は当月
        if ($this->month === 'last') {
            $saleStartMonth = date('Y-m', strtotime('-1 months'));
            $saleStartDateFrom = Carbon::parse('last month')->startOfMonth();
            $saleStartDateTo = Carbon::parse('last month')->endOfMonth();
        } else if ($this->month === 'next') {
            $saleStartMonth = date('Y-m', strtotime('+1 months'));
            $saleStartDateFrom = Carbon::parse('next month')->startOfMonth();
            $saleStartDateTo = Carbon::parse('next month')->endOfMonth();
        } else {
            $saleStartMonth = date('Y-m');
            $saleStartDateFrom = Carbon::now()->startOfMonth();
            $saleStartDateTo = Carbon::now()->endOfMonth();
        }
        $saleStartDateFrom = $saleStartDateFrom->format('Y-m-d');
        $saleStartDateTo = $saleStartDateTo->format('Y-m-d');

        // ソートの指定
        $sortBy = 'auto:asc';
        if ($this->sort == 'new') {
            $sortBy = 'sale_start_date:desc';
        } else if ($this->sort == 'old') {
            $sortBy = 'sale_start_date:asc';
        }

        // ジャンルIDをもとにキャッシュにデータがあるか確認しキャッシュがあればキャッシュからデータを取得する。
        $mappingData = $this->genreMapping($this->genreId);
        $cacheData = $himoReleaseOrder->setConditionGenreIdAndMonthAndProductTypeId(
            $this->genreId,
            $saleStartMonth . '-01',
            $mappingData['productSellRentalFlg'],
            $this->sort
        )->count();

        if (empty($cacheData)) {
            // キャッシュがなければデータを新規で取得する
            $himoRepository = new HimoRepository();
            $params = [
                'api' => 'release',
                'id' => $this->month . '_' . $this->genreId . '_0',
                'genreId' => $this->genreId,
                //'saleStartMonth' => $saleStartMonth,
                'saleStartDateFrom' => $saleStartDateFrom,
                'saleStartDateTo' => $saleStartDateTo,
                'productSellRentalFlg' => $mappingData['productSellRentalFlg'],
                'adultFlg' => $mappingData['adultFlg'],
                'msdbItem' => $mappingData['msdbItem'],
                'onlyReleased' => $this->onlyReleased,
                'sort' => $sortBy
            ];
            if ($mappingData['genres'] === 'recommendation') {
                $params['workTags'] = self::HIMO_TAP_RECOMMEND_KEYWORD;
            } else {
                $params['genre'] = $mappingData['genres'];
            }
            // 10件づつ処理
            $processLimit = 10;
            $himoRepository->setLimit($processLimit);
            // 検索
            $response = $himoRepository->searchCrossworksForRelease($params)->get();
            // 初回だけ全体件数の取得
            $totalCount = (int)$response['results']['total'];
            $pageNum = 1;
            $orderNum = 1;
            for ($processCount = 0; $processCount < $totalCount; $processCount = $processCount + $processLimit) {
                $params['id'] = $this->month . '_' . $this->genreId . '_' . $processCount; // test data
                $himoRepository->setOffset($processCount);
                $response = $himoRepository->searchCrossworksForRelease($params)->get();
                if (empty($response)) {
                    break;
                }
                $himoReleaseOrderData = [];
                foreach ($response['results']['rows'] as $row) {
                    $himoReleaseOrderData[] = [
                        'work_id' => $row['work_id'],
                        'month' => date('Y-m-d', strtotime($row['sale_start_month'])),
                        'tap_genre_id' => $this->genreId,
                        'page_no' => $pageNum,
                        'sort' => $orderNum,
                    ];
                    $orderNum++;
                }
                // データを取得する際は、常にお薦めで取得し、順序をDBに登録する。
                $himoReleaseOrder->insertBulk($himoReleaseOrderData);
                $workRepository->insertWorkData($response);
                if ($orderNum > $totalCount) {
                    break;
                }
                $pageNum++;
            }
        }
        if ($mappingData['msdbItem'] === 'audio') {
            if ($this->mediaFormat === 'album') {
                $mediaFormat = '1';
            } else if ($this->mediaFormat === 'single') {
                $mediaFormat = '2';
            }
        }
        $saleStartDateFrom = null;
        $saleStartDateTo = null;
        if ($this->onlyReleased === 'true') {
            $saleStartDateFrom = date('Y-m-01 00:00:00');
            $saleStartDateTo = date('Y-m-d 00:00:00');
        }
        $himoReleaseOrder->setConditionGenreIdAndMonthAndProductTypeId(
            $this->genreId,
            $saleStartMonth . '-01',
            $mappingData['productSellRentalFlg'],
            $this->sort,
            $mediaFormat,
            $saleStartDateFrom,
            $saleStartDateTo
        );
        $this->totalCount = $himoReleaseOrder->count();
        // キャッシュしたデータから対象の作品及び商品情報を集約し取得する。
        $results = $himoReleaseOrder
            ->selectCamel($this->selectColumn())
            ->get(
                $this->limit,
                $this->offset,
                $mappingData['productSellRentalFlg']);
        if (count($results) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }
        // アダルト表示は常に許可しておく。
        $workRepository->setAgeLimitCheck('true');
        foreach ($results as $result) {
            $tmpData = $workRepository->formatAddOtherData((array)$result, false, (array)$result);

            // Change productName -> productTitle
            $formatedData[] = arrayChangeKey($tmpData, 'productName', 'productTitle');
        }

        if (empty($formatedData)) {
            return null;
        }
        return $formatedData;
    }

    public function genreMapping($genreId)
    {
        $productSellRentalFlg = '';
        $adultFlg = '2';
        if ($genreId >= 1 && $genreId <= 16) {
            $msdbItem = ['video'];
            if ($genreId >= 1 && $genreId <= 8) {
                $productSellRentalFlg = '2';
            } else {
                $productSellRentalFlg = '1';
            }
        } else if ($genreId >= 17 && $genreId <= 27) {
            $msdbItem = ['audio'];
            if ($genreId >= 17 && $genreId <= 21) {
                $productSellRentalFlg = '2';
            } else {
                $productSellRentalFlg = '1';
            }
        } else if ($genreId >= 28 && $genreId <= 50) {
            $msdbItem = ['book'];
            if ($genreId >= 28 && $genreId <= 38) {
                $productSellRentalFlg = '2';
            } else {
                $productSellRentalFlg = '1';
            }
        } else if ($genreId >= 51 && $genreId <= 55) {
            $msdbItem = ['game'];
            $productSellRentalFlg = '1';
        } else if ($genreId >= 56 && $genreId <= 80) {
            $msdbItem = ['video'];
            $adultFlg = '1';
            $productSellRentalFlg = '2';
        } else if ($genreId >= 81 && $genreId <= 81) {
            $msdbItem = ['book'];
            $adultFlg = '1';
            $productSellRentalFlg = '1';
        }
        return [
            'productSellRentalFlg' => $productSellRentalFlg,
            'genres' => $this->genreMapToHimoParam($genreId),
            'adultFlg' => $adultFlg,
            'msdbItem' => $msdbItem
        ];
    }

    public function genreMapToHimoParam($genreId)
    {
        $listArray = config('release_genre_map');
        if (count($listArray[$genreId]) == 1) {
            $listString = $listArray[$genreId][0] . '::';
        } else {
            $listString = implode(':: || ', $listArray[$genreId]) . '::';
        }
        return $listString;
    }

    public function hasRecommend()
    {
        $himoReleaseOrder = new HimoReleaseOrder;
        $month['last'] = date('Y-m-01', strtotime('-1 months'));
        $month['this'] = date('Y-m-01');
        $month['next'] = date('Y-m-01', strtotime('+1 months'));
        $recommendList = [];
        $listArray = config('release_genre_map');
        foreach ($listArray as $key => $genre) {
            foreach ($genre as $value) {
                if ($value === 'recommendation') {
                    $recommendList[] = $key;
                }
            }
        }

        foreach ($month as $target => $targetMonth) {
            foreach ($recommendList as $genreId) {
                $result[$target][] = [
                    'genreId' => (string)$genreId,
                    'exist' => ($himoReleaseOrder->setConditionByGenreIdAndMonth($genreId, $targetMonth)->count() > 0) ? true : false,
                ];
            }
        }
        return $result;

    }

    private function outputColumn()
    {
        return [
            'workId',
            'urlCd',
            'cccWorkCd',
            'workTitle',
            'newFlg',
            'jacketL',
            'supplement',
            'saleType',
            'itemType',
            'adultFlg'
        ];
    }

    private function selectColumn()
    {
        return [
            'p2.work_id',
            'work_title',
            'work_type_id',
            'rating_id',
            'big_genre_id',
            'url_cd',
            'ccc_work_cd',
            'p3.jacket_l',
            'p3.sale_start_date',
            'p3.product_type_id',
            'p3.product_unique_id',
            'adult_flg',
            'product_name',
            'maker_name',
            'msdb_item',
            'media_format_id'
        ];
    }
}
