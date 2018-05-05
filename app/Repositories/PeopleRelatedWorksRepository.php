<?php

namespace App\Repositories;

use App\Exceptions\NoContentsException;
use App\Model\People;
use App\Model\Work;
use Log;
use App\Model\PeopleRelatedWork;
use App\Model\Product;
use App\Repositories\HimoRepository;
use App\Repositories\WorkRepository;
use App\Repositories\PeopleRepository;

class PeopleRelatedWorksRepository extends ApiRequesterRepository
{

    protected $sort;
    protected $offset;
    protected $limit;
    protected $hasNext;
    protected $totalCount;
    protected $saleType;

    private $peopleRelatedWork;

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        parent::__construct();
        $this->sort = $sort;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->peopleRelatedWork = new PeopleRelatedWork();
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
     * @param mixed $offset
     */
    public function setSaleType($saleType)
    {
        $this->saleType = $saleType;
    }

    public function getWorks($workId)
    {
        $product = new Product();
        $people = new People();
        $himo = new HimoRepository();
        $work = new WorkRepository();

        $productResult = $product->setConditionByWorkIdNewestProduct($workId)->getOne();
        if(empty($productResult)) {
            return null;
        }
        $people = $people->setConditionByProduct($productResult->product_unique_id)->getOne();
        if(empty($people)) {
            return null;
        }
        $this->totalCount = $this->peopleRelatedWork->setConditionById($people->person_id)->count();
        $result = $this->peopleRelatedWork->toCamel(['id', 'person_id'])->get($this->limit, $this->offset);
        if (empty(count($result))) {
            $himoResult = $himo->searchPeople([$people->person_id], '0301', ['book'])->get();
            if(empty($himoResult)) {
                return null;
            }
            foreach ($himoResult['results']['rows'] as $row) {
                foreach ($row['works'] as $work) {
                    $insertData[] = $this->format($people->person_id, $work);
                }
            }
            $this->peopleRelatedWork->insertBulk($insertData);
            $result = $this->peopleRelatedWork->setConditionById($people->person_id)->toCamel(['id', 'person_id'])->get($this->limit, $this->offset);
        }
        foreach ($result as $resultItem) {
            $resultArray[] = $resultItem->workId;
        }
        $work->getWorkList($resultArray);
        return $this->getWorkWithProductIdsIn($resultArray, $workId);
    }

    public function getWorksByArtist($workId)
    {
        $people = new PeopleRepository();
        $himo = new HimoRepository();

        $people = $people->getNewsPeople($workId)->getOne();
        if (!$people) {
            throw new NoContentsException;
        }
        $this->totalCount = $this->peopleRelatedWork->setConditionById($people->person_id)->count();
        $result = $this->peopleRelatedWork->selectCamel(['work_id'])->get();
        if (empty(count($result))) {
            $himoResult = $himo->searchPeople([$people->person_id], '0301', ['audio', 'video', 'book', 'game'])->get();
            if (empty($himoResult['results']['rows'])) {
                throw new NoContentsException;
            }
            foreach ($himoResult['results']['rows'] as $row) {
                foreach ($row['works'] as $work) {
                    $insertData[] = $this->format($people->person_id, $work);
                }
            }
            $this->peopleRelatedWork->insertBulk($insertData);
            $result = $this->peopleRelatedWork->setConditionById($people->person_id)->selectCamel(['work_id'])->get();
        }
        foreach ($result as $resultItem) {
            $resultArray[] = $resultItem->workId;
        }
        return $this->getWorkWithProductIdsIn($resultArray, $workId);
    }

    public function getWorkWithProductIdsIn($data ,$workId)
    {
        $workRepository = new WorkRepository();
        $work = new Work();

        $work->getWorkWithProductIdsIn($data, $this->saleType, $workId);
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


    public function format($personId, $row)
    {
        $workRepository = new WorkRepository();
        $base = $workRepository->format($row, true);
        $base['person_id'] = $personId;
        return $base;
    }

    private function outputColumn()
    {
        return [
            'workId',
            'urlCd',
            'cccWorkCd',
            'workTitle',
            'productName',
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
            'w1.jacket_l',
            'p2.sale_start_date',
            'p2.product_type_id',
            'p2.product_unique_id',
            'product_name',
            'maker_name',
            'game_model_name',
            'adult_flg',
            'msdb_item'
        ];
    }
}
