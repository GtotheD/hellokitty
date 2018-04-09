<?php

namespace App\Repositories;

use App\Model\HimoKeyword;

class HimoKeywordRepository
{

    protected $sort;
    protected $offset;
    protected $limit;
    protected $totalCount;
    protected $hasNext;

    private $himoKeyword;

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        $this->sort = $sort;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->himoKeyword = new HimoKeyword();
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit)
    {
        $this->limit = $limit;
    }
    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset(int $offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return string
     */
    public function getSort(): string
    {
        return $this->sort;
    }

    /**
     * @param string $sort
     */
    public function setSort(string $sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return mixed
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @return mixed
     */
    public function getHasNext()
    {
        return $this->hasNext;
    }

    public function get($keyword)
    {
        $keywords = [];
        $this->totalCount = $this->himoKeyword->setConditionByKeyword($keyword)->count();
        $results = $this->himoKeyword->select('keyword')->get($this->limit, $this->offset);
        if (count($results) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }
        foreach ($results as $result) {
            $keywords[] = $result->keyword;
        }

        return $keywords;
    }
}
