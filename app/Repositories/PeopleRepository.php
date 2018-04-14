<?php

namespace App\Repositories;

use App\Exceptions\NoContentsException;
use App\Model\People;
use App\Model\Product;
use App\Model\Work;

class PeopleRepository
{

    protected $sort;
    protected $offset;
    protected $limit;
    protected $apiHost;
    protected $apiKey;
    protected $saleType;
    protected $totalCount;

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
        $ignoreColumn = [
            'id',
            'product_unique_id',
            'created_at',
            'updated_at'
        ];

        $productRepository = new ProductRepository();
        $newestProduct = $productRepository->getNewestProductByWorkId($workId, $saleType)->getOne();
        if (!$newestProduct) {
            throw new NoContentsException();
        }
        $peopleModel = new People();
        $column = [
            'person_id',
            'person_name',
            'role_id',
            'role_name',
        ];

        $peopleCount = $peopleModel->setConditionByProduct($newestProduct->product_unique_id)->count();

        $this->totalCount = $peopleCount ?: 0;

        $people = $peopleModel->setConditionByProduct($newestProduct->product_unique_id)
            ->select($column)
            ->toCamel($ignoreColumn)
            ->limit($this->limit)
            ->offset($this->offset)
            ->get($this->limit, $this->offset);

        if (count($people) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }
        $response = [
            'hasNext' => $this->hasNext,
            'totalCount' => $this->totalCount ,
            'rows' => $people
        ];


        return $response;
    }

    public function insert($productId,  $people)
    {
        $peopleModel = new People();
        $peopleBase = $this->format($productId,  $people);
        return $peopleModel->insert($peopleBase);
    }

    public function format($productId, $people)
    {
        $peopleBase = [];
        $peopleBase['product_unique_id'] = $productId;
        $peopleBase['person_id'] = $people['person_id'];
        $peopleBase['person_name'] = $people['person_name'];
        $peopleBase['role_id'] = $people['role_id'];
        $peopleBase['role_name'] = $people['role_name'];

        return $peopleBase;
    }

    /**
     * GET newest people by $workId. If work_id not exists in system. Call workRepository.
     *
     * @param $workId
     * @param null $saleType
     * @param null $roleId
     * @return $this
     *
     * @throws NoContentsException
     */
    public function getNewsPeople($workId, $saleType = null, $roleId = null) {
        $people = new People();
        $productRepository = new ProductRepository();
        $newestProduct = $productRepository->getNewestProductByWorkId($workId, $saleType)->getOne();
        if(!$newestProduct) {
            throw new NoContentsException();
        }
        $peopleConditions = [
            'product_unique_id'  => $newestProduct->product_unique_id
        ];
        if($roleId) {
            $peopleConditions['role_id'] = $roleId;
        }
        return $people->getNewestPeople($peopleConditions);
    }



}