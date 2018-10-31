<?php

namespace App\Repositories;

use App\Exceptions\NoContentsException;
use App\Model\Recommend;
use App\Model\Work;
use App\Repositories\WorkRepository;

/*
 * お薦め作品一覧取得API用（この作品を見た人はこんな作品も見ています）
 * BK2からの情報を元に、リコメンドを作成する。
 *
 */
class RecommendOtherRepository extends BaseRepository
{
    private $recommend;

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        parent::__construct($sort, $offset, $limit);
        $this->recommend = new Recommend();
    }

    public function getWorks($workId, $saleType = null)
    {

        $work = new Work;
        $workRepository = new WorkRepository;
        $productRepository = new ProductRepository();
        $bk2Recoomend =  $this->recommend->setConditionByWorkId($workId)->getOne();
        if(empty($bk2Recoomend)) {
            return null;
        }
        $workIdList = explode(',', $bk2Recoomend->list_work_id);
        $workRepository->setSaleType($saleType);
        $workRepository->getWorkList($workIdList);
        // 自分自身のアイテム種別を取得
        $baseWork = $work->setConditionByWorkId($workId)->getOne();
        // ベースの作品がなかったらnullを返却
        if(empty($baseWork)) {
            return null;
        }
        if ($baseWork->work_format_id == $workRepository::WORK_FORMAT_ID_MUSICVIDEO) {
            $baseWork->work_type_id = $workRepository::WORK_TYPE_DVD;
        }
        $work->getWorkWithProductIdsIn($workIdList, $saleType, $workId, null, $baseWork->work_type_id);
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
                if (in_array($key,$this->outputColumn())) {
                    $formatedItemSelectColumn[$key] = $value;
                }
            }
            $workItems[] = $formatedItemSelectColumn;
        }
        return $workItems;
    }

    private function outputColumn()
    {
        return [
            'workId',
            'urlCd',
            'cccWorkCd',
            'workTitle',
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
            'w1.work_id',
            'work_type_id',
            'work_title',
            'work_format_id',
            'scene_l', // 上映映画対応
            'rating_id',
            'big_genre_id',
            'medium_genre_id',
            'small_genre_id',
            'url_cd',
            'ccc_work_cd',
            'w1.jacket_l',
            'adult_flg',
            'msdb_item',
            'product_type_id'
        ];
    }
}
