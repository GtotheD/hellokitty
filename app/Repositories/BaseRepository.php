<?php
namespace App\Repositories;

abstract class BaseRepository
{
    protected $sort;
    protected $offset;
    protected $limit;
    protected $saleType;
    protected $totalCount;
    protected $hasNext;
    protected $rows;
    protected $page;
    protected $ageLimitCheck;

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

    /**
     * @param mixed $saleType
     */
    public function setSaleType($saleType)
    {
        $this->saleType = $saleType;
    }

    /**
     * @param mixed $ageLimitCheck
     */
    public function setAgeLimitCheck($ageLimitCheck)
    {
        $this->ageLimitCheck = $ageLimitCheck;
    }

    public function setSort($sort)
    {
        $this->sort = $sort;
    }
    
    public function setIsDummy($isDummy) {
        $this->isDummy = $isDummy;
    }

    public function getIsDummy() {
        return $this->isDummy;
    }

}