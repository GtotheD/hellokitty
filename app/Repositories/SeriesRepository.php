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

    public function getNarrow($workId, $saleType)
    {
        // TODO: Waiting to confirm $saleType option

        $this->saleType = $saleType;
        $work = new Work();
        $himo = new HimoRepository();
        $workRepository = new  WorkRepository();
        $series = new Series();
        $workIdsInSeries = [];

        $himoResult = $himo->xmediaSeries($workId)->get();
        if(!$himoResult['results']['rows']) {
            throw new NoContentsException();
        }

        foreach ($himoResult['results']['rows'] as $row) {
            $this->seriesId = array_get($row, 'id');
            foreach ($row['works'] as $workRow) {
                $workIdsInSeries[] = $workRow['work_id'];
            }
        }

        $workIdsInSeries = array_values(array_unique($workIdsInSeries));
       // $workIdsInSeries = ['PTA0000G4CSA', 'PTA0000SF309', 'PTA0000SFCIH']; // Local data
        $workIdsExisted = $work->getWorkIdsIn($workIdsInSeries)->get()->pluck('work_id')->toArray();

        if(!$workIdsExisted ) {
            $workIdsNew = $workIdsInSeries;
        }else {
            $workIdsNew = array_values(array_diff($workIdsInSeries, $workIdsExisted));
        }

        // Call API and update insert to DB
        if($workIdsNew) {
            $insertResult = $workRepository->get($workIdsNew);
            // Insert to ts_series table
            foreach ($insertResult as $insertedWork) {
                $series->insert([
                    'small_series_id' => $this->seriesId,
                    'work_id' => $insertedWork
                ]);
            }
        }
        // Get response from DB
        $workCount = $work->getWorkIdsIn($workIdsInSeries)->count();
        $this->totalCount = $workCount ?: 0;
        $workList = $work->getWorkIdsIn($workIdsInSeries)
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
        dd($workList);
        foreach ($workList as $work) {
            $rows[] = [
                'workId' => $work->work_id,
                'urlCd' => $work->url_cd,
                'cccWorkCd' => $work->ccc_work_cd,
                'workTitle' => $work->work_title,
                'jacketL' => $work->jacket_l,
                'supplement' => '（仮）監督・著者・アーティスト・機種', // Template value, waiting for confirm
                'saleType' => $this->saleType, // Template value, waiting for confirm
                'itemType' => $workRepository->convertWorkTypeIdToStr($work->work_type_id),
                'adultFlg' => ($work->adult_flg === '1') ? true : false,
            ];
        }

        return [
            'hasNext' => $this->hasNext,
            'totalCount' => $this->totalCount ,
            'rows' => $rows
        ];
    }

}
