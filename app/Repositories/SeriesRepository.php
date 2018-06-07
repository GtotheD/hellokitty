<?php

namespace App\Repositories;

use App\Model\Series;
use App\Model\Work;
use App\Exceptions\NoContentsException;
use DB;

class SeriesRepository
{

    protected $sort;
    protected $offset;
    protected $limit;
    protected $apiHost;
    protected $apiKey;
    protected $saleType;
    protected $ageLimitCheck;
    protected $totalCount;
    protected $seriesId;
    protected $hasNext;


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
     * @param mixed $ageLimitCheck
     */
    public function setAgeLimitCheck($ageLimitCheck)
    {
        $this->ageLimitCheck = $ageLimitCheck;
    }

    public function getNarrow($workId, $saleType)
    {
        // TODO: Waiting to confirm $saleType option
        $this->saleType = $saleType;
        $work = new Work();
        $himo = new HimoRepository();
        $workRepository = new  WorkRepository();
        $series = new Series();
        $workIdsInSeries = [];

        $series = $series->setConditionByWorkId($workId);
        // DBにデータがなかった場合
        if($series->count() === 0) {
            // STEP 1: Get all workIds in series
            $himoResult = $himo->xmediaSeries([$workId])->get(true, 'POST');
            if (!$himoResult['results']['rows']) {
                throw new NoContentsException();
            }
            // seriesテーブルに格納するデータを作成
            foreach ($himoResult['results']['rows'] as $row) {
                foreach ($row['works'] as $workRow) {
                    if ($workRow['work_id'] === $workId) continue;
                    $getWorkIds[] = $workRow['work_id'];
                    $insertData[] = [
                        'work_id' => $workId,
                        'related_work_id' => $workRow['work_id'],
                    ];
                }
            }
            $series->insertBulk($insertData);
            $workRepository->getWorkList($getWorkIds);
        }

        // 再抽出
        $series = new Series();
        $saleTypeId = null;
        if ($saleType === 'sell') {
            $saleTypeId = 1;
        } else if ($saleType === 'rental') {
            $saleTypeId = 2;
        }
        $seriesWorks = $series->setConditionGetWorksByWorkId($workId, $saleTypeId);
        if (!$seriesWorks) {
            throw new NoContentsException();
        }
        $this->totalCount = $seriesWorks->count() ?: 0;
        if (!$this->totalCount) {
            throw new NoContentsException();
        }

        $workList = $seriesWorks->selectCamel($this->selectColumn())
            ->limit($this->limit)
            ->offset($this->offset)
            ->get();

        if (count($workList) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }

        // Fetch workList and get response
        $rows = [];

        $workRepository->setAgeLimitCheck($this->ageLimitCheck);
        foreach ($workList as $work) {
            $base = $workRepository->formatAddOtherData((array)$work, null, null, true);
            $rows[] = [
                'workId' => $base['workId'],
                'urlCd' => $base['urlCd'],
                'cccWorkCd' =>  $base['cccWorkCd'],
                'workTitle' => $base['workTitle'],
                'jacketL' => $base['jacketL'],
                'supplement' => $base['supplement'], // Template value, waiting for confirm
                'saleType' => $this->saleType, // Template value, waiting for confirm
                'itemType' => $workRepository->convertWorkTypeIdToStr( $base['workTypeId']),
                'adultFlg' =>$base['adultFlg'],
            ];
        }
        return [
            'hasNext' => $this->hasNext,
            'totalCount' => $this->totalCount,
            'rows' => $rows
        ];
    }

    /**
     * @param $workId
     * @param $seriesId
     *
     * @return array
     */
    public function format ($workId, $seriesId) {
        $seriesBase = [];
        $seriesBase['work_id'] = $workId;
        $seriesBase['small_series_id'] = $seriesId;
        return $seriesBase;
    }

    /**
     * @param $workId
     * @param $seriesId
     *
     * @return mixed
     */
    public function insert ($workId, $seriesId) {
        $series = new Series();
        return $series->insert( $this->format($workId, $seriesId));
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
            'w.work_id',
            'work_type_id',
            'work_title',
            'work_format_id',
            'rating_id',
            'big_genre_id',
            'medium_genre_id',
            'small_genre_id',
            'url_cd',
            'ccc_work_cd',
            'jacket_l',
            'sale_start_date',
            'adult_flg'
        ];
    }
}
