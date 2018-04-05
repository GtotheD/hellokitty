<?php

namespace App\Repositories;

use App\Model\People;

class PeopleRepository
{

    protected $sort;
    protected $offset;
    protected $limit;
    protected $apiHost;
    protected $apiKey;
    protected $saleType;

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
        $product = new ProductRepository();
        $ignoreColumn = [
            'id',
            'created_at',
            'updated_at'
        ];
        $newestProduct = $product->getNewestProductWorkIdSaleType($workId, $saleType);
        if (!$newestProduct) {
            throw new NoContentsException;
        }
        $peopleModel = new People();
        $column = [
            'person_id',
            'person_name',
            'role_id',
            'role_name',
        ];
        $result = $peopleModel->setConditionByProduct($newestProduct->productUniqueId)->select($column)->toCamel($ignoreColumn)->get();
        return $result;
    }

    public function insert($productId,  $people)
    {
        $peopleModel = new People();
        $peopleBase = [];

        $peopleBase['product_unique_id'] = $productId;
        $peopleBase['person_id'] = $people['person_id'];
        $peopleBase['person_name'] = $people['person_name'];
        $peopleBase['role_id'] = $people['role_id'];
        $peopleBase['role_name'] = $people['role_name'];

        return $peopleModel->insert($peopleBase);
    }

}