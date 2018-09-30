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
        $people = $people->setConditionByProduct($productResult->product_unique_id)->getOne();
        if (empty($people)) {
            return null;
        }
        $this->totalCount = $this->peopleRelatedWork->setConditionById($people->person_id)->count();
        $result = $this->peopleRelatedWork->toCamel(['id', 'person_id'])->get();
        if (empty(count($result))) {
            $himoRepository->setLimit(100);
            $himoResult = $himoRepository->searchPeople([$people->person_id], '0301', ['book'])->get();
            if (empty($himoResult)) {
                return null;
            }
            foreach ($himoResult['results']['rows'] as $row) {
                foreach ($row['works'] as $work) {
                    $insertData[] = $this->format($people->person_id, $work);
                }
            }
            $this->peopleRelatedWork->insertBulk($insertData);
            $result = $this->peopleRelatedWork->setConditionById($people->person_id)->toCamel(['id', 'person_id'])->get();
        }
        foreach ($result as $resultItem) {
            $resultArray[] = $resultItem->workId;
        }
        $workModel->getWorkList($resultArray);
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
        $people = $workRepository->getPerson($newestProduct->msdb_item, $newestProduct->product_unique_id);
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
            $formatedItem = $workRepository->formatAddOtherData($workItem, false, $workItem);
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
            'play_time', // 上映映画用
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
