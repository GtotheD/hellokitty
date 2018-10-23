<?php

namespace App\Repositories;

use App\Model\Series;
use App\Model\Work;
use App\Exceptions\NoContentsException;
use DB;

class SeriesRepository extends BaseRepository
{

    protected $seriesId;

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        parent::__construct($sort, $offset, $limit);
    }

    public function getNarrow($workId, $saleType)
    {
        $this->saleType = $saleType;
        $work = new Work();
        $himo = new HimoRepository();
        $workRepository = new  WorkRepository();
        $productRepository = new ProductRepository();
        $series = new Series();
        $workIdsInSeries = [];

        $series = $series->setConditionByWorkId($workId);
        // DBにデータがなかった場合
        if($series->count() === 0) {
            // STEP 1: Get all workIds in series
            $himoResult = $himo->xmediaSeries([$workId])->get(true, 'POST');
            if (!$himoResult['results']['rows']) {
                throw new NoContentsException();
            }
            // seriesテーブルに格納するデータを作成
            foreach ($himoResult['results']['rows'] as $row) {
                foreach ($row['works'] as $workRow) {
                    if ($workRow['work_id'] === $workId) continue;
                    if (!empty($getWorkIds) && in_array($workRow['work_id'], $getWorkIds)) continue;
                    $getWorkIds[] = $workRow['work_id'];
                    $insertData[] = [
                        'work_id' => $workId,
                        'related_work_id' => $workRow['work_id'],
                    ];
                }
            }
            // インサートデータがなかった場合は終了する。
            if (empty($insertData)) {
                throw new NoContentsException();
            }
            $series->insertBulk($insertData);
            $workRepository->getWorkList($getWorkIds);
        }

        // 再抽出
        $series = new Series();
        $seriesWorks = $series->setConditionGetWorksByWorkId($workId, $saleType);
        if (!$seriesWorks) {
            throw new NoContentsException();
        }
        $this->totalCount = $seriesWorks->count() ?: 0;
        if (!$this->totalCount) {
            throw new NoContentsException();
        }

        $workList = $seriesWorks->selectCamel($this->selectColumn())
            ->limit($this->limit)
            ->offset($this->offset)
            ->get();

        if (count($workList) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }

        // Fetch workList and get response
        $rows = [];
        $workRepository->setAgeLimitCheck($this->ageLimitCheck);
        foreach ($workList as $work) {
            $work = (array)$work;
            $workRepository->setSaleType($productRepository->convertProductTypeToStr($work['productTypeId']));
            $base = $workRepository->formatAddOtherData($work, null, null, true);
            $rows[] = [
                'workId' => $base['workId'],
                'urlCd' => $base['urlCd'],
                'cccWorkCd' =>  $base['cccWorkCd'],
                'workTitle' => $base['workTitle'],
                'jacketL' => $base['jacketL'],
                'supplement' => $base['supplement'], // Template value, waiting for confirm
                'saleType' => $base['saleType'], // Template value, waiting for confirm
                'itemType' => $workRepository->convertWorkTypeIdToStr( $base['workTypeId']),
                'adultFlg' =>$base['adultFlg'],
            ];
        }
        return [
            'hasNext' => $this->hasNext,
            'totalCount' => $this->totalCount,
            'rows' => $rows
        ];
    }

    /**
     * @param $workId
     * @param $seriesId
     *
     * @return array
     */
    public function format ($workId, $seriesId) {
        $seriesBase = [];
        $seriesBase['work_id'] = $workId;
        $seriesBase['small_series_id'] = $seriesId;
        return $seriesBase;
    }

    /**
     * @param $workId
     * @param $seriesId
     *
     * @return mixed
     */
    public function insert ($workId, $seriesId) {
        $series = new Series();
        return $series->insert( $this->format($workId, $seriesId));
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
            'w.work_id',
            'work_type_id',
            'work_title',
            'work_format_id',
            'scene_l',
            'rating_id',
            'big_genre_id',
            'medium_genre_id',
            'small_genre_id',
            'url_cd',
            'ccc_work_cd',
            'jacket_l',
            'sale_start_date',
            'adult_flg',
            'msdb_item',
            'product_type_id'
        ];
    }
}
