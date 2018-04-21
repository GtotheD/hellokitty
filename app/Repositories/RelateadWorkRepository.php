<?php

namespace App\Repositories;

use App\Model\RelateadWork;
use App\Model\Work;
use App\Exceptions\NoContentsException;
use DB;

class RelateadWorkRepository
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

    public function getNarrow($workId)
    {
        // TODO: Waiting to confirm $saleType option
        $work = new Work();
        $himo = new HimoRepository();
        $workRepository = new  WorkRepository();
        $relateadWork = new RelateadWork();
        $workIdsIn = [];

        // STEP 1: 関連作品テーブルからリストを取得。なければHimoから新規で取得。
        $relatedWorkList = $relateadWork->setConditionByWork($workId)->select('related_work_id')->get();
        if(empty(count($relatedWorkList))) {
            $himoResult = $himo->xmediaRelatedWork([$workId])->get(true, 'POST');
            if (!$himoResult) {
                throw new NoContentsException();
            }
            // Get Only Work Ids
            $rows = $this->xmediaFormat($himoResult);
            foreach ($rows as $row) {
                $insertRelationWorkList[] = [
                    'work_id' => $workId,
                    'related_work_id' => $row['workId']
                ];
            }
            $relateadWork->insertBulk($insertRelationWorkList);
            // retry
            $relatedWorkList = $relateadWork->setConditionByWork($workId)->select('related_work_id')->get();
        }
        foreach ($relatedWorkList as $relatedWork) {
            $relatedWorkArray[] = $relatedWork->related_work_id;
        }
        // STEP 2: 関連作品の詳細情報をworkテーブルから取得する為に、既に取得済みのIDを抽出する。
        $workIdsExisted = $work->getWorkIdsIn($relatedWorkArray)->select('work_id')->get();
        foreach ($workIdsExisted as $workIdsExistedItem) {
            $workIdsExistedArray[] = $workIdsExistedItem->work_id;
        }

        // STEP 3: IDが取得出来なかった場合は全てHimoから新規で詳細情報を取得するためのリストを作成。
        if (!$workIdsExisted) {
            $workIdsNew = $relatedWorkArray;
        } else {
            $workIdsNew = array_values(array_diff($workIdsIn, $workIdsExistedArray));
        }

        // STEP 4: 既存データから取ってこれなかったものをHimoから取得し格納する。
        // Get data by list workIds and return
        $himo = new HimoRepository();
        $himoResult = $himo->crosswork($workIdsNew)->get();
        // Himoから取得できなかった場合はスキップする
        if (!empty($himoResult)) {
            $insertResult = $workRepository->insertWorkData($himoResult);
        }

        // STEP 5: 条件をセット
        $work->getWorkIdsIn($relatedWorkArray);
        $this->totalCount = $work->count();
        if (!$this->totalCount) {
            throw new NoContentsException();
        }

        // STEP 6: 条件と指定して再度取得
        $workList = $work->toCamel(['id'])->get($this->limit, $this->offset);
        if (count($workList) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }

        // STEP 7:フォーマットを変更して返却
        $rows = [];
        foreach ($workList as $work) {
            $work = (array)$work;
            $works[] = $workRepository->formatAddOtherData($work);
        }
        return $works;
    }

    public function xmediaFormat($rows)
    {
        $rows = $rows['results']['rows'];
        foreach ($rows as $row) {
            foreach($row['big_serieses'] as $bigSerieses) {
                foreach($bigSerieses['small_serieses'] as $smallSerieses) {
                    foreach ($smallSerieses['works'] as $work) {
                        $tmpWork['workId'] = $work['work_id'];
                        $works[] = $tmpWork;
                    }
                }
            }
        }
        return $works;
    }
}
