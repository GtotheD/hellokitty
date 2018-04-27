<?php

namespace App\Repositories;

use App\Exceptions\NoContentsException;
use App\Model\Recommend;
use App\Model\Work;
use App\Repositories\WorkRepository;

class RecommendOtherRepository
{

    protected $sort;
    protected $offset;
    protected $limit;
    protected $hasNext;
    protected $totalCount;

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        $this->sort = $sort;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->recommend = new Recommend();
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
     * @return mixed
     */
    public function getHasNext()
    {
        return $this->hasNext;
    }

    /**
     * @return mixed
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }


    public function getWorks($workId, $saleType = null)
    {

        $work = new Work;
        $workRepository = new WorkRepository;
        $bk2Recoomend =  $this->recommend->setConditionByWorkId($workId)->getOne();
        if(empty($bk2Recoomend)) {
            return null;
        }
        $workIdList = explode(',', $bk2Recoomend->list_work_id);
        $workIdList = array_slice($workIdList, 0, 20);
        $workRepository->getWorkList($workIdList);

        $work->getWorkWithProductIdsIn($workIdList, $saleType);
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
