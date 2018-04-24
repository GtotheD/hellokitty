<?php

namespace App\Repositories;

use App\Exceptions\NoContentsException;
use App\Model\Recommend;
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
        $work = new WorkRepository;
        $max = 100;
        $limitOnceMax = 5;
        $bk2Recoomend =  $this->recommend->setConditionByWorkId($workId)->getOne();
        $workIdList = explode(',', $bk2Recoomend->list_work_id);
        $this->totalCount = count($workIdList);
        $workIdList = array_slice($workIdList, $this->offset, $this->limit);
        $loopCount = 0;
        $limitOnce = 0;
        $mergeWorks = [];
        $work->setSaleType($saleType);
        // 10件ずつ問い合わせ。アプリ上で何件だすかで制御を変更する。
        foreach ($workIdList as $workId) {
            $loopCount++;
            $limitOnce++;
            $getList[] = $workId;
            if ($limitOnce >= $limitOnceMax ||
                (count($workIdList) - $loopCount) === 0 ||
                $loopCount == $max
            ) {
                $works =  $work->getWorkList($getList);
                $mergeWorks = array_merge($mergeWorks, $works['rows']);
                // リセットをかける
                $limitOnce = 0;
                $getList = [];
                if($loopCount == $max) {
                    break;
                }
            }
        }
        if (count($mergeWorks) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }
        return $mergeWorks;
    }
}
