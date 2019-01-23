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
use App\Repositories\ProductRepository;

class PeopleRelatedWorksRepository extends BaseRepository
{
    private $peopleRelatedWork;

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        parent::__construct($sort = 'asc', $offset = 0, $limit = 10);
        $this->peopleRelatedWork = new PeopleRelatedWork();
    }

    public function getWorks($workId)
    {
        $product = new Product();
        $people = new People();
        $himoRepository = new HimoRepository();
        $workModel = new WorkRepository();

        $productResult = $product->setConditionByWorkIdNewestProduct($workId)->getOne();
        if (empty($productResult)) {
            return null;
        }
        $peopleCollection = collect(json_decode($productResult->people));
        $people = $peopleCollection->first();
        if (empty($people)) {
            return null;
        }
        $this->totalCount = $this->peopleRelatedWork->setConditionById($people->person_id)->count();
        $result = $this->peopleRelatedWork->toCamel(['id', 'person_id'])->get($this->limit, $this->offset);
        if (empty(count($result))) {
            $himoRepository->setLimit(100);
            $himoResult = $himoRepository->searchPeople([$people->person_id], '0301', ['book'])->get();
            if (empty($himoResult)) {
                return null;
            }
            foreach ($himoResult['results']['rows'] as $row) {
                foreach ($row['works'] as $work) {
                    $insertData[] = [
                        'person_id' => $people->person_id,
                        'work_id' => $work['work_id']
                    ];
                }
            }
            $this->peopleRelatedWork->insertBulk($insertData);
            $result = $this->peopleRelatedWork->setConditionById($people->person_id)->toCamel(['id', 'person_id'])->get($this->limit, $this->offset);
        }
        foreach ($result as $resultItem) {
            $resultArray[] = $resultItem->workId;
        }
        return $this->getWorkWithProductIdsIn($resultArray, $workId);
    }

    public function getWorksByArtist($workId)
    {
        $himoRepository = new HimoRepository();
        $workRepository = new WorkRepository;
        $productRepository = new ProductRepository;

        $newestProduct = $productRepository->getNewestProductByWorkId($workId)->getOne();
        if (!$newestProduct) {
            throw new NoContentsException;
        }
        $people = $workRepository->getPerson($newestProduct->msdb_item, $newestProduct->people);
        if (!$people) {
            throw new NoContentsException;
        }
        $this->peopleRelatedWork->setConditionById($people->person_id);
        $result = $this->peopleRelatedWork->selectCamel(['work_id'])->get();
        if (empty(count($result))) {
            // 取得件数を100件で絞る
            $himoRepository->setLimit(100);
            $himoResult = $himoRepository->crossworksArtistRelatedWork($people->person_id)->get();
            if (empty($himoResult['results']['rows'])) {
                throw new NoContentsException;
            }
            foreach ($himoResult['results']['rows'] as $row) {
                $insertData[] = [
                    'person_id' => $people->person_id,
                    'work_id' => $row['work_id']
                ];
            }
            $this->peopleRelatedWork->insertBulk($insertData);
            $result = $this->peopleRelatedWork->setConditionById($people->person_id)->selectCamel(['work_id'])->get();
        }
        foreach ($result as $resultItem) {
            $resultArray[] = $resultItem->workId;
        }
        return $this->getWorkWithProductIdsIn($resultArray, $workId);
    }

    public function getWorkWithProductIdsIn($data, $workId)
    {
        $workRepository = new WorkRepository();
        $productRepository = new ProductRepository();
        $work = new Work();
        $workRepository->getWorkList($data);
        $work->getWorkWithProductIdsIn($data, $this->saleType, $workId, $this->sort);
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
            $workRepository->setSaleType($productRepository->convertProductTypeToStr($workItem['productTypeId']));
            $formatedItem = $workRepository->formatAddOtherData($workItem, false);
            foreach ($formatedItem as $key => $value) {
                if (in_array($key, $this->outputColumn())) {
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
            'work_format_id',
            'scene_l', // 上映映画対応
            'w1.jacket_l',
            'rating_id',
            'big_genre_id',
            'medium_genre_id',
            'small_genre_id',
            'url_cd',
            'ccc_work_cd',
            'adult_flg',
            'w1.msdb_item',
            't1.product_type_id'
        ];
    }
}
