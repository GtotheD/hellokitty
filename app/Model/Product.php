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

    public function setConditionByWorkIdSaleType($workId, $saleType = null)
    {
        $this->dbObject = DB::table($this->table)
            ->where([
                'work_id' => $workId,
            ]);
        if($saleType) {
            $this->dbObject->where('product_type_id', $this->convertSaleType($saleType));
        }
        return $this;
    }

    public function setConditionRentalGroup($workId)
    {
        $groupingColumn = 'work_id, product_name, sale_start_date, ccc_family_cd';
        $jacketQuery = 'MAX(jacket_l) AS jacket_l';
        $dvdQuery = 'MAX(CASE item_cd WHEN \'0021\' THEN rental_product_cd ELSE NULL END) AS dvd';
        $blurayQuery = 'MAX(CASE item_cd WHEN \'0022\' THEN rental_product_cd ELSE NULL END) AS bluray';
        $selectQuery = $groupingColumn. ','.
            $jacketQuery. ','.
            $dvdQuery. ','.
            $blurayQuery;
        $subQuery = DB::table($this->table)->select(DB::raw($selectQuery))
            ->groupBy(DB::raw($groupingColumn))
            ->havingRaw(' (dvd is not null AND bluray is not null)');
        $this->dbObject = DB::table(DB::raw("({$subQuery->toSql()}) as sub"))
            ->where([
            'work_id' => $workId,
        ]);
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

    private function convertSaleType($type)
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
        return DB::table($this->table)->insert($insertData);
    }
}
