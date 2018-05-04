<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

    /*
     * Get Newest Product
     */
    public function setConditionByWorkIdNewestProduct($workId, $saleType = null)
    {
        $this->dbObject = DB::table($this->table)
            ->where([
                'work_id' => $workId,
            ]);

        // Add sale type filter
        if ($saleType) {
            $this->dbObject->where([
                'product_type_id' => $this->convertSaleType($saleType),
            ]);
        }
        $this->dbObject->orderBy('ccc_family_cd', 'desc')
            ->orderBy('sale_start_date', 'desc')
            ->limit(1);
        return $this;
    }

    public function setConditionByRentalProductCd($rentalProductCd)
    {
        $this->dbObject = DB::table($this->table . ' AS p1')
            ->join($this->table . ' AS p2', function($join) {
                $join->on('p1.ccc_family_cd','=','p2.ccc_family_cd')
                    ->on('p1.product_type_id','=','p2.product_type_id')
                    ->on(DB::raw('RIGHT(p1.item_cd, 2)'), '=', DB::raw('RIGHT(p2.item_cd, 2)'));
            })
            ->select(DB::raw('p1.work_id, p1.ccc_family_cd, p2.product_type_id, p2.rental_product_cd'))
            ->where([
                ['p1.rental_product_cd', '=', $rentalProductCd],
                ['p2.product_type_id', '=', self::PRODUCT_TYPE_ID_RENTAL]
            ]);

        return $this;
    }

    public function setConditionByJan($jan)
    {
        $this->dbObject = DB::table($this->table . ' AS p1')
            ->join($this->table . ' AS p2', 'p1.ccc_family_cd', '=', 'p2.ccc_family_cd')
            ->select(DB::raw('p1.work_id, p1.ccc_family_cd, p2.rental_product_cd, p1.jan'))
            ->where([
                ['p1.jan', '=', $jan],
                ['p2.rental_product_cd', '<>', '']
            ]);
        return $this;
    }

    public function setConditionByWorkIdSaleType($workId, $saleType = null, $order = null)
    {
        $this->dbObject = DB::table($this->table)
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

    public function setConditionProductGroupingByWorkIdSaleType($workId, $saleType = null, $order = null)
    {
        $select = 't2.product_name,'
            .'t2.rental_product_cd,'
            .'t2.jan,'
            .'t2.product_type_id,'
            .'t2.item_cd,'
            .'t2.item_name,'
            .'t2.ccc_family_cd,'
            .'t2.sale_start_date';
        $selectSubGrouping = 'item_cd,'
            .'product_type_id,'
            .'product_name,'
            .'ccc_family_cd ';
        $selectSub = ',MIN(product_unique_id) AS product_unique_id ';
        $subQuery = DB::table($this->table)->select(DB::raw($selectSubGrouping.$selectSub))
            ->whereRaw(DB::raw(' item_cd not like \'_1__\' '))
            ->groupBy(DB::raw($selectSubGrouping));
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
                ->orderBy('t2.ccc_family_cd', 'asc');
        } else {
            $this->dbObject
                ->orderBy('t2.sale_start_date', 'desc')
                ->orderBy('t2.ccc_family_cd', 'desc');
        }

//        dd($this->dbObject->toSql());

        return $this;
    }

    public function setConditionRentalGroup($workId, $order = null)
    {
        $groupingColumn = 'work_id, product_name, ccc_family_cd';
        $productUniqueId = 'MAX(product_unique_id) AS product_unique_id';
        $saleStartDate = 'MAX(sale_start_date) AS sale_start_date';
        $jacketQuery = 'MAX(jacket_l) AS jacket_l';
        $dvdQuery = 'MAX(CASE WHEN (item_cd = \'0021\' OR item_cd = \'0121\') THEN rental_product_cd ELSE NULL END) AS dvd';
        $blurayQuery = 'MAX(CASE WHEN (item_cd = \'0022\' OR item_cd = \'0122\') THEN rental_product_cd ELSE NULL END) AS bluray';
        $selectQuery = $groupingColumn. ','.
            $productUniqueId. ','.
            $saleStartDate. ','.
            $jacketQuery. ','.
            $dvdQuery. ','.
            $blurayQuery;
        $subQuery = DB::table($this->table)->select(DB::raw($selectQuery))
            ->whereRaw(DB::raw('item_cd not like \'_1__\''))
            ->groupBy(DB::raw($groupingColumn))
            ->havingRaw(' NOT (dvd IS NULL AND bluray IS NULL)');
        $this->dbObject = DB::table(DB::raw("({$subQuery->toSql()}) as sub"))
            ->where(['work_id' => $workId]);
        if ($order === 'old') {
            $this->dbObject->orderBy('sale_start_date', 'asc');
        } else {
            $this->dbObject->orderBy('sale_start_date', 'desc');
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
}
