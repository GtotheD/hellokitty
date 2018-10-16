<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Model\Product;

/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/27
 * Time: 19:47
 */
class Work extends Model
{
    const TABLE = 'ts_works';

    const REQUEST_ITEM_TYPE_DVD = 'dvd';
    const REQUEST_ITEM_TYPE_CD = 'cd';
    const REQUEST_ITEM_TYPE_BOOK = 'book';
    const REQUEST_ITEM_TYPE_GAME = 'game';

    // 本プログラムで使っている販売タイプの文字列
    const SALE_TYPE_SELL = 'sell';
    const SALE_TYPE_RENTAL = 'rental';
    const SALE_TYPE_THEATER = 'theater';
    const SALE_TYPE_OTHER = 'other';

    // MSDBアイテム種別
    const MSDB_ITEM_VIDEO = 'video';
    const MSDB_ITEM_MUSIC = 'music';
    const MSDB_ITEM_BOOK = 'book';
    const MSDB_ITEM_GAME = 'game';

    // 1=音楽、2=映像、3=書籍、4=ゲーム、5=グッズ、6=音楽単曲、7=映画
    const WORK_TYPE_CD = '1';
    const WORK_TYPE_DVD = '2';
    const WORK_TYPE_BOOK = '3';
    const WORK_TYPE_GAME = '4';
    const WORK_TYPE_GOODS = '5';
    const WORK_TYPE_MUSIC_UNIT = '6';
    const WORK_TYPE_THEATER = '7';

    function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function setConditionByWorkId($workId)
    {
        $this->dbObject = DB::table($this->table)
            ->where([
                'work_id' => $workId,
            ]);
        return $this;
    }

    public function setConditionByUrlCd($urlCd, $saleType = null)
    {
        $this->dbObject = DB::table($this->table);
        if (is_array($urlCd)) {
            $this->dbObject->whereIn('url_cd', $urlCd);
        } else {
            $this->dbObject->where(['url_cd' => $urlCd]);
        }
        if($saleType) {
            $existsWhere = $this->getClauseProductSaleType($saleType);
            $productsSubQuery = DB::table('ts_products AS tp')
                ->select(DB::raw('tp.work_id'))
                ->whereRaw($existsWhere);
            $this->dbObject = DB::table($this->table. ' AS t1')
                ->whereRaw('t1.work_id IN ('.$productsSubQuery->toSql().')');
        }
        return $this;
    }

    public function insert($data)
    {
        $insertData = [];
        $insertData['updated_at'] = date('Y-m-d H:i:s');
        $count = 0;
        $ignoreColumn = ['id', 'created_at', 'updated_at'];

        $dbObject = DB::table($this->table);
        $columns = Schema::getColumnListing(self::TABLE);
        foreach ($columns as $column) {
            if(!in_array($column, $ignoreColumn)) {
                if (isset($data[$column])) {
                    $insertData[$column] = $data[$column];
                }
            }
        }
        $count = $dbObject->where('work_id', $data['work_id'])->count();
        if($count) {
            return $dbObject->where('work_id', $data['work_id'])->update($insertData);
        } else {
            $insertData['created_at'] = date('Y-m-d H:i:s');
            return DB::table($this->table)->insertGetId($insertData);
        }
    }

    /**
     * Get all work_id not in workdIds array
     *
     * @param $workIds
    */
    public function getWorkIdsIn($workIds = []) {
        $this->dbObject = DB::table($this->table)
            ->whereIn('work_id', $workIds);
        return $this;
    }

    /**
     * Get all work_id not in workdIds array
     * products
     * @param $workIds
     */
    public function getWorkWithProductByWorkIdsIn($workIds = [], $saleType = null)
    {
        // 全て
        if($saleType === 'sell') {
            $existsWahere = 'product_type_id = 1';
        } else if ($saleType === 'rental') {
            $existsWahere = 'product_type_id = 2';
        } else {
            $existsWahere = 'product_type_id = 1 OR product_type_id = 2';
        }
        $this->dbObject = DB::table($this->table. ' as t1')
            ->whereExists(function ($query) use ($existsWahere){
                $query->select(DB::raw(1))
                    ->from('ts_products as t2')
                    ->where('t1.work_id', '=', 't2.work_id')
                    ->whereRaw($existsWahere);
            })->whereIn('t1.work_id', $workIds);
        return $this;
    }

    /**
     * Get all work_id not in workdIds array
     * products
     * @param $workIds
     */
    public function getWorkWithProductIdsIn($workIds = [], $saleType = null, $ignoreWorkId = null, $order = null, $itemType = null) {
        $selectSubGrouping =
            'p1.work_id,'
            .'product_type_id';
        $selectSub = ',MIN(product_unique_id) AS product_unique_id ';
        $subQuery = DB::table('ts_products AS p1')->select(DB::raw($selectSubGrouping.$selectSub))
            ->whereRaw(DB::raw(' item_cd not like \'_1__\' '))
            ->whereRaw(DB::raw(' service_id  in  (\'tol\', \'st\')'))
            ->whereIn('work_id', $workIds)
            ->groupBy(DB::raw($selectSubGrouping));
        if($ignoreWorkId) {
            $subQuery->whereRaw(DB::raw("work_id <> '{$ignoreWorkId}'"));
        }
        $this->dbObject = DB::table(DB::raw("({$subQuery->toSql()}) as t1"))
            ->join('ts_products as p2', 'p2.product_unique_id', '=', 't1.product_unique_id')
            ->join('ts_works as w1', 'w1.work_id', '=', 't1.work_id')
            ->mergeBindings($subQuery)
        ;
        if (!empty($itemType)) {
            $this->dbObject->where('w1.work_type_id', $itemType);
        }

        if ($saleType === 'sell') {
            $this->dbObject->where('p2.product_type_id', '1')
                ->orWhereRaw(DB::raw(' p2.product_type_id = \'\' '));
        } elseif ($saleType === 'rental') {
            $this->dbObject->where('p2.product_type_id', '2')
            ->orWhereRaw(DB::raw(' p2.product_type_id = \'\' '));
        } elseif ($saleType === 'theater') {
            $this->dbObject->where('p2.product_type_id', '1')
                ->orWhereRaw(DB::raw(' p2.product_type_id = \'\' '));
        }
        if ($order === 'old') {
            $this->dbObject
                ->orderBy('p2.sale_start_date', 'asc')
                ->orderBy('p2.ccc_family_cd', 'asc');
        } else {
            $this->dbObject
                ->orderBy('p2.sale_start_date', 'desc')
                ->orderBy('p2.ccc_family_cd', 'desc');
        }
        return $this;
    }

    /**
     * Get all work_id not in workdIds array
     * products
     * @param $workIds
     */
    public function getWorkWithProductIdsInEx($workId, $saleType = null, $order = null) {
        $selectSubGrouping =
            'p1.work_id,'
            .'product_type_id';
        $selectSub = ',MIN(product_unique_id) AS product_unique_id ';
        $subQuery = DB::table('ts_products AS p1')->select(DB::raw($selectSubGrouping.$selectSub))
            ->join('ts_related_works as rw', 'rw.related_work_id', '=', 'p1.work_id')
            ->whereRaw(DB::raw(' item_cd not like \'_1__\' '))
            ->whereRaw(DB::raw(' service_id  in  (\'tol\', \'st\')'))
            ->whereRaw(DB::raw(" rw.work_id = '{$workId}'"))
            ->whereRaw(DB::raw(" rw.related_work_id <> '{$workId}'"))
            ->groupBy(DB::raw($selectSubGrouping));
        $this->dbObject = DB::table(DB::raw("({$subQuery->toSql()}) as t1"))
            ->join('ts_products as t2', 't2.product_unique_id', '=', 't1.product_unique_id')
            ->join('ts_works as w1', 'w1.work_id', '=', 't1.work_id');
        if ($saleType === 'sell') {
            $this->dbObject->where('t2.product_type_id', '1')
                ->orWhereRaw(DB::raw(' (p2.product_type_id = \'\' AND service_id = \'st\' '));
        } elseif ($saleType === 'rental') {
            $this->dbObject->where('t2.product_type_id', '2')
                ->orWhereRaw(DB::raw(' (p2.product_type_id = \'\' AND service_id = \'st\' '));
        } elseif ($saleType === 'theater') {
            $this->dbObject->where('p2.product_type_id', '1')
                ->orWhereRaw(DB::raw(' p2.product_type_id = \'\' '));
        }
        if ($order === 'old') {
            $this->dbObject
                ->orderBy('t2.sale_start_date', 'asc')
                ->orderBy('t2.ccc_family_cd', 'asc');
        } else {
            $this->dbObject
                ->orderBy('t2.sale_start_date', 'desc')
                ->orderBy('t2.ccc_family_cd', 'desc');
        }
        return $this;
    }

    /**
     * Get work data from product sale type
     * @param type|array $works 
     * @param type $saleType 
     * @return type
     */
    public function getWorkBySaleType($workIds = [], $saleType = null)
    {
        $productsSubQuery = DB::table('ts_products AS tp')
                ->select(DB::raw('tp.work_id'));
        if($saleType) {
            $productsSubQuery->whereRaw($this->getClauseProductSaleType($saleType))
                ->orWhereRaw('t1.only_other = \'1\'');
        }
        $this->dbObject = DB::table($this->table. ' AS t1')
                ->whereRaw('t1.work_id IN ('.$productsSubQuery->toSql().')')
                ->whereIn('t1.work_id', $workIds);
        return $this;
    }

    /**
     * Get clause product sale type
     * @param type $saleType 
     * @return type
     */
    function getClauseProductSaleType($saleType) 
    {
        // 全て
        if($saleType === 'sell') {
            $existsWhere = 'product_type_id = 1 OR (product_type_id = \'\' AND service_id = \'st\')';
        } else if ($saleType === 'rental') {
            $existsWhere = 'product_type_id = 2 OR (product_type_id = \'\' AND service_id = \'st\')';
        } else {
            $existsWhere = 'product_type_id = 1 OR product_type_id = 2  OR (product_type_id = \'\' AND service_id = \'st\')';
        }
        return $existsWhere;
    }

    /**
     * Insert bulk records
     *
     * @param array $data
     *
     * @return mixed
     */
    public function insertBulk($works = [], $workIds)
    {
        $insertData = [];
        $ignoreColumn = ['id', 'created_at', 'updated_at'];
        $columns = Schema::getColumnListing(self::TABLE);

        $exists = $this->getWorkIdsIn($workIds)->get()->pluck('work_id')->toArray();

        foreach ($works as $key => $row) {
            if (!empty($exists) || in_array($row['work_id'], $exists)) {
                continue;
            }
            $insertData[$key]['updated_at'] = date('Y-m-d H:i:s');
            foreach ($columns as $column) {
                if (!in_array($column, $ignoreColumn)) {
                    $insertData[$key][$column] = array_get($row, $column) ?: '';
                }
            }
            $insertData[$key]['created_at'] = date('Y-m-d H:i:s');
        }

        return $this->bulkInsertOnDuplicateKey($insertData);
    }
}
