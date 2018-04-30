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

        // STEP 1: 関連作品テーブルからリストを取得。なければHimoから新規で取得。
        $relatedWorkList = $relateadWork->setConditionByWork($workId)->select('related_work_id')->get();
        if(empty(count($relatedWorkList))) {
            $himoResult = $himo->xmediaRelatedWork([$workId])->get(true);
            if (!$himoResult['results']['rows']) {
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
        // 問い合わせしてDBに格納
        $workRepository->getWorkList($relatedWorkArray);

        $work->getWorkWithProductIdsIn($relatedWorkArray);
        $this->totalCount = $work->count();
        $workList = $work->selectCamel($this->selectColumn())->get($this->limit, $this->offset);
        if (count($workList) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }

        // STEP 7:フォーマットを変更して返却
        $workItems = [];
        foreach ($workList as $workItem) {
            $workItem = (array)$workItem;
            $formatedItem = $workRepository->formatAddOtherData($workItem, false, $workItem);
            foreach ($formatedItem as $key => $value) {
                if (in_array($key,$this->outputColumn())) {
                    $formatedItemSelectColumn[$key] = $value;
                }
            }
            $workItems[] = $formatedItemSelectColumn;
        }
        return $workItems;
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
            't1.work_id',
            'work_type_id',
            'work_title',
            'rating_id',
            'big_genre_id',
            'url_cd',
            'ccc_work_cd',
            't1.jacket_l',
            't2.sale_start_date',
            't2.product_type_id',
            'product_unique_id',
            'product_name',
            'maker_name',
            'game_model_name',
            'adult_flg',
            'msdb_item'
        ];
    }
}
