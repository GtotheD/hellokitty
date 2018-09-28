<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Model\Work;

/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/27
 * Time: 19:47
 */
class Product extends Model
{
    const TABLE = 'ts_products';
    const PRODUCT_TYPE_ID_SELL = 1;
    const PRODUCT_TYPE_ID_RENTAL = 2;

    const DUMMY_DATA_IS_NOT_DUMMY = 0;
    const DUMMY_DATA_IS_DUMMY = 1;

    function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function setConditionByProductUniqueId($productUniqueId)
    {
        $this->dbObject = DB::table($this->table)
            ->where([
                'product_unique_id' => $productUniqueId,
            ]);
        return $this;
    }

    public function setConditionByProductUniqueIdIn(array $productUniqueIds)
    {
        $this->dbObject = DB::table($this->table)
            ->whereIn('product_unique_id', $productUniqueIds);
        return $this;
    }

    public function setConditionByWorkId($workId)
    {
        $this->dbObject = DB::table($this->table)
            // TOL,ST以外を排他
            ->whereRaw(DB::raw(' service_id  in  (\'tol\', \'st\')'))
            ->where('work_id', $workId);
        return $this;
    }

    public function setConditionByWorkIdForRentalCd($workId)
    {
        $this->dbObject = DB::table($this->table . ' AS t2')
            ->join('ts_works AS t1', 't2.work_id', '=', 't1.work_id')
            ->where('t2.work_id', $workId)
            ->where('is_dummy', '=', self::DUMMY_DATA_IS_NOT_DUMMY)
            ->where('product_type_id', '=', self::PRODUCT_TYPE_ID_RENTAL)
            ->whereRaw(DB::raw(' service_id  in  (\'tol\')'))
            ->whereRaw(DB::raw(' item_cd not like \'_1__\' '))
//            ->whereRaw(DB::raw(' jan not like \'9999_________\' '))
            ->orderBy('t2.ccc_product_id', 'desc') // 最古のものを一番上にもってきて取得する為
        ;
        return $this;
    }

    public function setConditionByUrlCd($urlCd, $saleType)
    {
        $this->dbObject = DB::table($this->table . ' AS t2')
            ->join('ts_works AS t1', 't2.work_id', '=', 't1.work_id')
            ->where('t1.url_cd', $urlCd)
            ->where('is_dummy', '=', self::DUMMY_DATA_IS_NOT_DUMMY)
            ->whereRaw(DB::raw(' item_cd not like \'_1__\' '));
//            ->whereRaw(DB::raw(' jan not like \'9999_________\' '));
            if($saleType) {
                $this->dbObject->where('product_type_id', $this->convertSaleType($saleType));
            }
        $this->dbObject->orderBy('t2.ccc_product_id', 'asc') // 最新のものを取得
        ;
        return $this;
    }

    /*
     * Get Newest Product
     */
    public function setConditionByWorkIdNewestProduct($workId, $saleType = null, $isMovie = false)
    {
        $this->dbObject = DB::table($this->table . ' as t1')
            ->whereRaw(DB::raw(' service_id  in  (\'tol\', \'st\')'))
            ->where([
                'work_id' => $workId,
            ]);
        // Add sale type filter
        if ($saleType) {
            $this->dbObject->where([
                'product_type_id' => $this->convertSaleType($saleType),
            ]);
        }
        if ($isMovie) {
            // PPTのみの場合も該当する
            $this->dbObject->whereRaw(DB::raw(' item_cd like \'__21\' '));
        }
        $this->dbObject->orderBy('ccc_family_cd', 'desc')
            ->orderBy('sale_start_date', 'desc')
            // todo: この条件をいれないとccc_family_cdとsale_start_dateだけではかぶってしまう
            // ->orderBy('jan', 'desc')
            ->limit(1);
        return $this;
    }

    public function selectWithDvdJacketL()
    {
        $column = $this->getColumn();
        array_forget($column, 'jacket_l');
        $jacketSubQuery = DB::table($this->table . ' as t2')
            ->select('jacket_l')
            ->whereRaw(DB::raw('t2.work_id = t1.work_id'))
            ->whereRaw(DB::raw('t2.product_type_id = t1.product_type_id'))
            ->whereRaw(DB::raw(' service_id  in  (\'tol\', \'st\')'))
            ->orderBy('ccc_family_cd', 'desc')
            ->orderBy('item_cd_right_2', 'asc')
            ->orderBy('sale_start_date', 'desc')
            ->limit(1);
        $column[] = '('.$jacketSubQuery->toSql().') as jacketL';
        $this->selectCamel($column);
        return $this;
    }

    public function setConditionSelectJacket($workId, $saleType)
    {
        $this->dbObject = DB::table($this->table)
            ->select('jacket_l as jacketL')
            ->whereRaw(DB::raw('(jacket_l <> null OR jacket_l <> \'\')'))
            ->where([
                'work_id' => $workId,
            ]);
        if ($saleType) {
            $this->dbObject->where([
                'product_type_id' => $this->convertSaleType($saleType),
            ]);
        }
        $this->dbObject->orderBy('ccc_family_cd', 'desc')
            ->orderBy('item_cd_right_2', 'asc')
            ->orderBy('sale_start_date', 'desc')
            ->limit(1);
        return $this;

    }

    public function setConditionByRentalProductCdFamilyGroup($rentalProductCd, $isAudio = false)
    {
        $this->dbObject = DB::table($this->table . ' AS p1')
            ->join($this->table . ' AS p2', function($join) use($isAudio){
                $join->on('p1.ccc_family_cd','=','p2.ccc_family_cd')
                    ->on('p1.product_type_id','=','p2.product_type_id')
                    ->on('p1.item_cd_right_2', '=', 'p2.item_cd_right_2');
                if ($isAudio) {
                    $join->on('p1.base_product_code','=','p2.base_product_code');
                }
            })
            ->select(DB::raw('p2.*'))
            ->whereRaw(DB::raw(' service_id  in  (\'tol\', \'st\')'))
            ->where([
                ['p1.rental_product_cd', '=', $rentalProductCd],
                ['p2.product_type_id', '=', self::PRODUCT_TYPE_ID_RENTAL]
            ]);
        return $this;
    }

    public function setConditionByJanFamilyGroup($jan)
    {
        $this->dbObject = DB::table($this->table . ' AS p1')
            ->join($this->table . ' AS p2', 'p1.ccc_family_cd', '=', 'p2.ccc_family_cd')
            ->select(DB::raw('p1.*'))
            ->whereRaw(DB::raw(' service_id  in  (\'tol\', \'st\')'))
            ->where([
                ['p1.work_id', '=', 'p2.work_id'],
                ['p1.jan', '=', $jan],
                ['p2.rental_product_cd', '<>', '']
            ]);
        return $this;
    }

    public function setConditionByRentalProductCd($rentalProductCd)
    {
        $this->dbObject = DB::table($this->table . ' AS p1')
            ->where([
                ['p1.rental_product_cd', '=', $rentalProductCd],
            ]);
        return $this;
    }

    public function setConditionByWorkIdSaleType($workId, $saleType = null, $order = null)
    {
        $this->dbObject = DB::table($this->table)
            ->whereRaw(DB::raw(' item_cd not like \'__20\' '))
            ->where([
                'work_id' => $workId,
            ]);
        if($saleType) {
            $this->dbObject->where('product_type_id', $this->convertSaleType($saleType));
        }
        if($order) {
            $this->dbObject->orderBy('sale_start_date', $order);
        }

        return $this;
    }

    public function setConditionByWorkIdSaleTypeSaleStartDate($workId, $saleType = null, $saleStartDateFrom = null, $saleStartDateTo = null)
    {
        $this->dbObject = DB::table($this->table)
            ->whereRaw(DB::raw(' item_cd not like \'__20\' '))
            ->where([
                'work_id' => $workId,
            ]);
        if($saleType) {
            $this->dbObject->where('product_type_id', $this->convertSaleType($saleType));
        }
        $this->dbObject->orderBy('number_of_volume', 'desc');
        if($saleStartDateFrom) {
            $this->dbObject->where('sale_start_date', '>=', $saleStartDateFrom);
        }
        if($saleStartDateTo) {
            $this->dbObject->where('sale_start_date', '<=', $saleStartDateTo);
        }
        return $this;
    }

    /*
     * $workId
     * $saleType
     * $order
     * $isAudio
     * $withPpt 含める場合はTrue、含めない場合はFalse
     */
    public function setConditionProductGroupingByWorkIdSaleType($workId, $saleType = null, $order = null ,$isAudio, $withPpt = true)
    {
        $selectSubGrouping = 'item_cd_right_2,'
            .'product_type_id,'
            .'product_name,'
            .'ccc_family_cd ';
        $selectSub = ',MAX(CASE WHEN SUBSTRING(item_cd, 2, 1) = \'0\' THEN product_unique_id END) AS no_ppt,'
            .'MAX(CASE WHEN SUBSTRING(item_cd, 2, 1) = \'1\' THEN product_unique_id END) AS ppt ';
        $subQueryBase = DB::table($this->table)->select(DB::raw($selectSubGrouping.$selectSub))
            ->whereRaw(DB::raw(' work_id = \''.$workId .'\''))
            // プロダクトは上映映画の時は呼ばないのでtolのみで絞る
            ->whereRaw(DB::raw(' service_id in  (\'tol\')'))
//            ->whereRaw(DB::raw(' item_cd not like \'_1__\' '))
            //->whereRaw(DB::raw(' item_cd not like \'__20\' ')) // VHSも出力するように変更
//            ->whereRaw(DB::raw(' jan not like \'9999_________\' '))
            ->groupBy(DB::raw($selectSubGrouping));
        if ($isAudio && $saleType === 'rental') {
            $subQueryBase->whereRaw(DB::raw(' is_dummy = 0 '));
        }
        $subQuerySelect = 'item_cd_right_2,'
            .'product_type_id,'
            .'product_name,'
            .'ccc_family_cd,'
            .'no_ppt,'
            .'ppt,'
            .'CASE WHEN no_ppt is not null THEN no_ppt ELSE ppt END AS product_unique_id';
        $subQuery = DB::table(DB::raw("({$subQueryBase->toSql()}) as t1"))
            ->select(DB::raw($subQuerySelect));
        $this->dbObject = DB::table(DB::raw("({$subQuery->toSql()}) as t1"))
            ->join($this->table.' as t2', 't2.product_unique_id', '=', 't1.product_unique_id')
            ->where('work_id','=',$workId);
        if ($saleType === 'sell') {
            $this->dbObject->where('t2.product_type_id', '1');
        } elseif ($saleType === 'rental') {
            $this->dbObject->where('t2.product_type_id', '2');
        }
        if ($order === 'old') {
            $this->dbObject
                ->orderBy('t2.sale_start_date', 'asc')
                ->orderBy('t2.ccc_family_cd', 'asc')
                ->orderBy('t2.jan', 'asc')
            ;
        } else {
            $this->dbObject
                ->orderBy('t2.sale_start_date', 'desc')
                ->orderBy('t2.ccc_family_cd', 'desc')
                ->orderBy('t2.jan', 'desc')
            ;
        }
        return $this;
    }

    public function setConditionRentalGroup($workId, $order = null, $ignoreOtherMedia = false)
    {
        $groupingColumn = 'work_id, product_name, ccc_family_cd';
//        $saleStartDate = 'MAX(sale_start_date) AS sale_start_date';
        $saleStartDate = 'MIN(sale_start_date) AS sale_start_date';
        $dvdQuery = 'MAX(CASE WHEN (item_cd = \'0021\' OR item_cd = \'0121\') THEN rental_product_cd ELSE NULL END) AS dvd';
        $blurayQuery = 'MAX(CASE WHEN (item_cd = \'0022\' OR item_cd = \'0122\') THEN rental_product_cd ELSE NULL END) AS bluray';
        $selectQuery = $groupingColumn. ','.
            $saleStartDate. ','.
            $dvdQuery. ','.
            $blurayQuery;
        $subQuery = DB::table($this->table)->select(DB::raw($selectQuery))
            ->whereRaw(DB::raw('work_id = \''.$workId . '\''))
            ->whereRaw(DB::raw(' product_type_id = 2 '))
            ->whereRaw(DB::raw(' service_id in  (\'tol\')'))
//            ->whereRaw(DB::raw(' jan not like \'9999_________\' '))
            ->groupBy(DB::raw($groupingColumn))
        ;
        $this->dbObject = DB::table(DB::raw("({$subQuery->toSql()}) as sub"))
            ->where(['work_id' => $workId]);

        if ($ignoreOtherMedia) {
            $this->dbObject
                ->whereRaw(DB::raw(' NOT (dvd is null AND bluray is null) '));
        }
        if ($order === 'old') {
            $this->dbObject->orderBy('sale_start_date', 'asc')
                ->orderBy('ccc_family_cd', 'asc')
            ;
        } else {
            $this->dbObject->orderBy('sale_start_date', 'desc')
                ->orderBy('ccc_family_cd', 'desc')
            ;
        }
        return $this;
    }

    public function setConditionRentalGroupNewestCccProductId($workId, $cccFamilyCd, $saleStartData)
    {
        $groupingColumn = 'work_id, ccc_family_cd, sale_start_date';
        $columns = 'MAX(ccc_product_id) as ccc_product_id';
        $selectQuery = $groupingColumn.','.$columns;
        $subQuery = DB::table($this->table)->select(DB::raw($selectQuery))
            ->whereRaw(DB::raw(' work_id = \''.$workId . '\''))
            ->whereRaw(DB::raw(' ccc_family_cd = \''.$cccFamilyCd . '\''))
            ->whereRaw(DB::raw(' sale_start_date = \''.$saleStartData . '\''))
            ->groupBy(DB::raw($groupingColumn));
        $this->dbObject = DB::table(DB::raw("({$subQuery->toSql()}) as t1"))
            ->join($this->table.' as t2', function ($join) {
                $join->on('t2.work_id', '=', 't1.work_id')
//                    ->whereRaw(DB::raw(' jan not like \'9999_________\' '))
                    ->whereRaw(DB::raw(' product_type_id = 2 '))
                    ->whereRaw(DB::raw(' service_id in  (\'tol\')'))
                    ->whereRaw(DB::raw('t2.ccc_family_cd = t1.ccc_family_cd'))
                    ->whereRaw(DB::raw('t2.sale_start_date = t1.sale_start_date'));
            });
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
        $count = $dbObject->where('product_unique_id' , $data['product_unique_id'])->count();
        if($count) {
            return $dbObject->where('product_unique_id' , $data['product_unique_id'])->update($insertData);
        } else {
            $insertData['created_at'] = date('Y-m-d H:i:s');
            return DB::table($this->table)->insertGetId($insertData);
        }
    }

    public function convertSaleType($type)
    {
        switch ($type) {
            case 'sell': return 1; break;
            case 'rental': return 2; break;
        }
    }

    public function insertBulk ($products)
    {
        $insertData = [];
        $ignoreColumn = ['id', 'created_at', 'updated_at'];
        $columns = Schema::getColumnListing(self::TABLE);
        foreach ($products as $key => $row) {
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

    public function rentalItemCount($workId)
    {
        // DVDとブルーレイのカウント
        $dvdQuery = 'count(CASE WHEN item_cd_right_2  = \'21\' OR  item_cd_right_2  = \'22\'  THEN 1  END) as dvd';
        $otherQuery = 'count(CASE WHEN !(item_cd_right_2  = \'21\' OR  item_cd_right_2  = \'22\')  THEN 1  END) as other';

        $this->dbObject = DB::table($this->table)
            ->where('work_id', $workId)
            ->where('product_type_id', '2')
            ->select(DB::raw($dvdQuery.','.$otherQuery))
        ;
        return $this;
    }

    public function isOnlyOtherItem($workId)
    {
        $this->dbObject = DB::table($this->table)
            ->where('work_id', $workId)
            ->whereNotIn('service_id', ['discas','ec','musico']);
        $count = $this->dbObject->count();
        return ($count == 0);
    }
}
