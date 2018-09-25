<?php

namespace App\Repositories;

use App\Model\RelateadWork;
use App\Model\Work;
use App\Exceptions\NoContentsException;
use DB;

class RelateadWorkRepository extends BaseRepository
{

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        parent::__construct($sort, $offset, $limit);
    }

    public function getNarrow($workId)
    {
        // TODO: Waiting to confirm $saleType option
        $work = new Work();
        $himo = new HimoRepository();
        $workRepository = new  WorkRepository();
        $relateadWork = new RelateadWork();

        // STEP 1: 関連作品テーブルからリストを取得。なければHimoから新規で取得。
        $relatedWorkList = $relateadWork->setConditionByWork($workId)->select('related_work_id')->get();
        if(empty(count($relatedWorkList))) {
            $himoResult = $himo->xmediaRelatedWork([$workId])->get(true);
            if (!$himoResult['results']['rows']) {
                throw new NoContentsException();
            }
            // Get Only Work Ids
            $rows = $this->xmediaFormat($himoResult);
            foreach ($rows as $row) {
                $insertRelationWorkList[] = [
                    'work_id' => $workId,
                    'related_work_id' => $row['workId']
                ];
            }
            $relateadWork->insertBulk($insertRelationWorkList);
            // retry
            $relatedWorkList = $relateadWork->setConditionByWork($workId)->select('related_work_id')->get();
        }
        foreach ($relatedWorkList as $relatedWork) {
            $relatedWorkArray[] = $relatedWork->related_work_id;
        }
        // 問い合わせしてDBに格納
        $workRepository->getWorkList($relatedWorkArray);
        $work->getWorkWithProductIdsInEx($workId);
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
                if (in_array($key,$this->outputColumn())) {
                    $formatedItemSelectColumn[$key] = $value;
                }
            }
            $workItems[] = $formatedItemSelectColumn;
        }
        return $workItems;
    }

    public function xmediaFormat($rows)
    {
        $rows = $rows['results']['rows'];
        foreach ($rows as $row) {
            foreach ($row['works'] as $work) {
                $tmpWork['workId'] = $work['work_id'];
                $works[] = $tmpWork;
            }
            foreach($row['small_serieses'] as $smallSerieses) {
                    foreach ($smallSerieses['works'] as $work) {
                        $tmpWork['workId'] = $work['work_id'];
                        $works[] = $tmpWork;
                    }
                }
            }
        return $works;
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
            't2.sale_start_date',
            't2.product_type_id',
            't2.product_unique_id',
            'product_name',
            'maker_name',
            'game_model_name',
            'adult_flg',
            't2.msdb_item',
            'media_format_id',
            'number_of_volume',
            'item_cd',
            'maker_cd'
        ];
    }
}
