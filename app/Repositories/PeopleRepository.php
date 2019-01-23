<?php

namespace App\Repositories;

use App\Exceptions\NoContentsException;
use App\Model\People;
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
        $ignoreColumn = [
            'id',
            'product_unique_id',
            'created_at',
            'updated_at'
        ];

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
        $peopleModel = new People();
        $column = [
            'person_id',
            'person_name',
            'role_id',
            'role_name',
        ];

        $peopleCount = $peopleModel->setConditionByProduct($newestProduct->product_unique_id)->count();

        $this->totalCount = $peopleCount ?: 0;
        if ($this->totalCount === 0) return null;

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
