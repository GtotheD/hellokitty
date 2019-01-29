<?php

namespace App\Repositories;

use App\Exceptions\NoContentsException;
use App\Model\Product;
use App\Model\Work;
use App\Repositories\WorkRepository;

class PeopleRepository extends BaseRepository
{

    protected $apiHost;
    protected $apiKey;

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        parent::__construct($sort, $offset, $limit);
    }

    public function getNarrow($workId, $saleType)
    {
        $productRepository = new ProductRepository();
        $work = new Work();
        $workData = $work->setConditionByWorkId($workId)->getOne();
        $isMovie = false;
        if($workData->work_type_id == 2) {
            $isMovie = true;
        }
        $newestProduct = $productRepository->getNewestProductByWorkId($workId, $saleType, $isMovie)->getOne();
        if (!$newestProduct) {
            throw new NoContentsException();
        }
        $peopleCollection = collect(json_decode($newestProduct->people));
        $this->totalCount = $peopleCollection->count();
        $this->rows = $peopleCollection->splice($this->offset, $this->limit);
        if (count($this->rows) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }

        return $this;
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
        $productRepository = new ProductRepository();
        $newestProduct = $productRepository->getNewestProductByWorkId($workId, $saleType)->getOne();
        if(!$newestProduct) {
            throw new NoContentsException();
        }
        $peopleCollection = collect(json_decode($newestProduct->people));
        if($roleId) {
            $person = $peopleCollection->where('role_id', $roleId)->first();
        } else {
            $person = $peopleCollection->first();
        }
        return $person;
    }
}
