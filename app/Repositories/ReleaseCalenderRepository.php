<?php

namespace App\Repositories;

use App\Model\RelateadWork;
use App\Model\Work;
use App\Model\HimoReleaseOrder;
use App\Exceptions\NoContentsException;
use DB;

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

        $mappingData = $this->genreMapping($this->genreId);

        // パラメーターを取得する
        // キャッシュにデータがあるか確認しキャッシュがあればキャッシュからデータを取得する。
        // 確認はジャンルID
        if ($this->month === 'last') {
            $saleStartMonth = date('Y-m-01', strtotime('-1 months'));
        } else if ($this->month === 'this') {
            $saleStartMonth = date('Y-m-01');
        } else if ($$this->month === 'next') {
            $saleStartMonth = date('Y-m-01', strtotime('+1 months'));
        }
        $cacheData = $himoReleaseOrder->setConditionGenreIdAndMonth(
            $this->genreId,
            $saleStartMonth,
            $mappingData['productSellRentalFlg'],
            $this->sort
        )->count();

        if (empty($cacheData)) {
            // キャッシュがなければデータを新規で取得する
            $himo = new HimoRepository();
            $params = [
                'api' => 'release',
                'id' => $this->month . '_' . $this->genreId . '_0',
                'genreId' => $this->genreId,
                'month' => $this->month,
                'productSellRentalFlg' => $mappingData['productSellRentalFlg'],
                'genre' => $mappingData['genres'],
                'adultFlg' => $mappingData['adultFlg'],
                'msdbItem' => $mappingData['msdbItem'],
                'onlyReleased' => $this->onlyReleased,
            ];
            // 10件づつ処理
            $processLimit = 10;
            $himo->setLimit(10);
            // 検索
            $response = $himo->searchCrossworksForRelease($params)->get();
            // 初回だけ全体件数の取得
            $totalCount = (int)$response['results']['total'];

            $pageNum = 1;
            $orderNum = 1;
            for ($processCount = 0; $processCount < $totalCount; $processCount = $processCount + $processLimit) {
                $params['id'] = $this->month . '_' . $this->genreId . '_' . $processCount; // test data
                $response = $himo->searchCrossworksForRelease($params)->get();
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
                $pageNum++;
                $himo->setOffset($processCount);
            }
        }
        if ($this->mediaFormat === 'album') {
            $mediaFormat = '1';
        } else if ($this->mediaFormat === 'single') {
            $mediaFormat = '2';
        }
        $saleStartDateFrom = null;
        $saleStartDateTo = null;
        if ($this->onlyReleased === 'true') {
            $saleStartDateFrom = date('Y-m-01 00:00:00');
            $saleStartDateTo = date('Y-m-d 00:00:00');
        }
        $this->totalCount = $himoReleaseOrder->setConditionGenreIdAndMonth(
            $this->genreId,
            $saleStartMonth,
            $mappingData['productSellRentalFlg'],
            $this->sort,
            $mediaFormat,
            $saleStartDateFrom,
            $saleStartDateTo
        )->count();
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
        foreach ($results as $result) {
            $formatedData[] = $workRepository->formatAddOtherData((array)$result, false, (array)$result);
        }

        if (empty($formatedData)) {
            return null;
        }
        return $formatedData;
    }

    public function genreMapping($genreId)
    {
        $productSellRentalFlg = '';
        $adultFlg = '';
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
            if ($genreId >= 1 && $genreId <= 8) {
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
        $listString = implode(':: || ', $listArray[$genreId]) . '::';
        return $listString;
    }

    public function getGenreMap()
    {
        return [
            1 => ['EXT0000001DO',
                'EXT00000018Q',
                'EXT00000022S',
                'EXT0000002GF'
            ],
            18 => [
                'EXT00000009V',
                'EXT00000000R',
                'EXT000000011',
                'EXT00000000M',
            ],
            19 => [
                'EXT00000009V',
                'EXT00000000R',
                'EXT000000011',
                'EXT00000000M',
            ],
            23 => [
                'EXT00000009V',
                'EXT00000000R',
                'EXT000000011',
                'EXT00000000M',
            ],
            79 => ['EXT0000007QI'],
            80 => ['EXT000000Q1W'],
        ];
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
        ];
    }
}
