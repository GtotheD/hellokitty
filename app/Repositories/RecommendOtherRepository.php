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
        $max = 20;
        $limitOnceMax = 10;
        $bk2Recoomend =  $this->recommend->setConditionByWorkId($workId)->getOne();
        if(empty($bk2Recoomend)) {
            return null;
        }
        $workIdList = explode(',', $bk2Recoomend->list_work_id);
        $loopCount = 0;
        $limitOnce = 0;
        $mergeWorks = [];
        // 10件ずつ問い合わせ。アプリ上で何件だすかで制御を変更する。
        foreach ($workIdList as $workId) {
            $loopCount++;
            $limitOnce++;
            $getList[] = $workId;
            if ($limitOnce >= $limitOnceMax ||
                (count($workIdList) - $loopCount) === 0 ||
                $loopCount == $max
            ) {
                $works =  $workRepository->getWorkList($getList);
                if(empty($works)) {
                    return null;
                }
                $mergeWorks = array_merge($mergeWorks, $works['rows']);
                // リセットをかける
                $limitOnce = 0;
                $getList = [];
                if($loopCount == $max) {
                    break;
                }
            }
        }

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
            'adult_flg',
            'msdb_item'
            ];
    }
}
