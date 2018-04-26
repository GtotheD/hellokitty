<?php

namespace App\Repositories;

use App\Exceptions\NoContentsException;
use App\Model\People;
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
        if (count($result) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }
        foreach ($result as $workItem) {
            $row = (array)$workItem;
            // 仮
            $row['adultFlg'] = '';
            $response[] = $work->formatAddOtherData($row, false);
        }
        return $response;
    }

    public function getWorksByArtist($workId)
    {
        $people = new PeopleRepository();
        $himo = new HimoRepository();
        $work = new WorkRepository();

        $people = $people->getNewsPeople($workId)->getOne();
        if (!$people) {
            throw new NoContentsException;
        }
        $this->totalCount = $this->peopleRelatedWork->setConditionById($people->person_id)->count();
        $result = $this->peopleRelatedWork->toCamel(['id', 'person_id'])->get($this->limit, $this->offset);
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
            $result = $this->peopleRelatedWork->setConditionById($people->person_id)->toCamel(['id', 'person_id'])->get($this->limit, $this->offset);
        }
        if (count($result) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }
        foreach ($result as $workItem) {
            $row = (array)$workItem;
            // 仮
            $row['adultFlg'] = '';
            $response[] = $work->formatAddOtherData($row, false);
        }
        return $response;
    }

    public function format($personId, $row)
    {
        $workRepository = new WorkRepository();
        $base = $workRepository->format($row, true);
        $base['person_id'] = $personId;
        return $base;
    }
}
