<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HimoReleaseOrder extends Model
{
    const TABLE = 'ts_himo_release_orders';

    function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function setConditionGenreIdAndMonthAndProductTypeId(
        $genreId,
        $month,
        $productTypeId,
        $order = null,
        $mediaFormat = null,
        $saleStartDateFrom = null,
        $saleStartDateTo = null
    )
    {
        $selectSubGrouping =
            'p1.work_id,'
            .'p1.sale_start_date,'
            .'hro.sort';
        $selectSub = ',MIN(product_unique_id) AS product_unique_id ';
        $subQuery = DB::table('ts_himo_release_orders AS hro')->select(DB::raw($selectSubGrouping.$selectSub))
            ->join('ts_products as p1', 'hro.work_id', '=', 'p1.work_id')
            ->whereRaw(DB::raw(' item_cd not like \'_1__\' '))
            ->where('product_type_id', $productTypeId)
            ->where('tap_genre_id', $genreId)
            ->where('month', $month)
            ->groupBy(DB::raw($selectSubGrouping));
        $this->dbObject = DB::table(DB::raw("({$subQuery->toSql()}) as p2"))
            ->mergeBindings($subQuery)
            ->join('ts_products as p3', 'p2.product_unique_id', '=', 'p3.product_unique_id')
            ->join('ts_works as w1', 'w1.work_id', '=', 'p2.work_id');
        if ($mediaFormat) {
            $this->dbObject
                ->where('w1.work_format_id', $mediaFormat);
        }
        if(!empty($saleStartDateFrom)) {
            $this->dbObject
                ->where('p2.sale_start_date', '>',$saleStartDateFrom);
        }
        if(!empty($saleStartDateTo)) {
            $this->dbObject
                ->where('p2.sale_start_date', '<',$saleStartDateTo);
        }

        if ($order === 'new') {
            $this->dbObject
                ->orderBy('p2.sale_start_date', 'desc')
                ->orderBy('p2.product_unique_id', 'desc')
            ;
        } else if ($order === 'old') {
            $this->dbObject
                ->orderBy('p2.sale_start_date', 'asc')
                ->orderBy('p2.product_unique_id', 'asc')
                ;
        } else {
            $this->dbObject
                ->orderBy('sort', 'asc');
        }
        return $this;
    }

    public function setConditionByGenreIdAndMonth($genreId, $month)
    {
        $this->dbObject = DB::table($this->table)
            ->where([
                ['tap_genre_id', $genreId],
                ['month', $month]
            ]);
        return $this;
    }

    /**
     * Insert bulk records
     *
     * @param array $data
     *
     * @return mixed
     */
    public function insertBulk($orders)
    {
        $insertData = [];
        $ignoreColumn = ['id', 'created_at', 'updated_at'];
        $columns = Schema::getColumnListing(self::TABLE);
        foreach ($orders as $key => $row) {
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
