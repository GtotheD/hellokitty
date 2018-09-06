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
    protected $ageLimitCheck;

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

    /**
     * @param mixed $ageLimitCheck
     */
    public function setAgeLimitCheck($ageLimitCheck)
    {
        $this->ageLimitCheck = $ageLimitCheck;
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
        $workRepository->setSaleType($saleType);
        $workRepository->getWorkList($workIdList);
        // 自分自身のアイテム種別を取得
        $baseWork = $work->setConditionByWorkId($workId)->getOne();
        if ($baseWork->work_format_id == $workRepository::WORK_FORMAT_ID_MUSICVIDEO) {
            $baseWork->work_type_id = $workRepository::WORK_TYPE_DVD;
        }
        $work->getWorkWithProductIdsIn($workIdList, $saleType, $workId, null, $baseWork->work_type_id);
        $this->totalCount = $work->count();
        $workList = $work->selectCamel($this->selectColumn())->get($this->limit, $this->offset);
        if (count($workList) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }

        // STEP 7:フォーマットを変更して返却
        $workItems = [];
        $workRepository->setAgeLimitCheck($this->ageLimitCheck);
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
            'w1.work_id',
            'work_type_id',
            'work_title',
            'work_format_id',
            'rating_id',
            'big_genre_id',
            'medium_genre_id',
            'small_genre_id',
            'url_cd',
            'ccc_work_cd',
            'w1.jacket_l',
            'p2.sale_start_date',
            'p2.product_type_id',
            'p2.product_unique_id',
            'product_name',
            'maker_name',
            'game_model_name',
            'adult_flg',
            'p2.msdb_item',
            'media_format_id',
            'number_of_volume',
            'item_cd',
            'maker_cd'
        ];
    }
}
