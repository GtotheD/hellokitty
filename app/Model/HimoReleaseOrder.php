<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Repositories\WorkRepository;

class HimoReleaseOrder extends Model
{
    const TABLE = 'ts_himo_release_orders';

    function __construct($table = self::TABLE)
    {
        parent::__construct($table);
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
        $workRepsitory = new WorkRepository;
        $selectSubGrouping =
            'p1.work_id,'
            .'p1.product_type_id,'
            .'p1.sale_start_date,'
            .'hro.sort';
        $selectSub = ',MAX(p1.ccc_family_cd) AS ccc_family_cd ';
        $subQuery = DB::table('ts_himo_release_orders AS hro')
            ->select(DB::raw($selectSubGrouping.$selectSub))
            ->join('ts_products as p1', 'hro.work_id', '=', 'p1.work_id')
            ->where('product_type_id', $productTypeId)
            ->where('tap_genre_id', $genreId)
            ->where('month', $month)
            ->groupBy(DB::raw($selectSubGrouping));
        $selectSubGroupingFinal =
              'p2.sort,'
             .'p3.work_id,'
             .'p3.ccc_family_cd,'
             .'p3.sale_start_date';
        $selectSubFinal = ',MAX(p3.product_unique_id) AS product_unique_id ';
        $subQueryFinal = DB::table(DB::raw("({$subQuery->toSql()}) as p2"))
            ->mergeBindings($subQuery)
            ->select(DB::raw($selectSubGroupingFinal.$selectSubFinal))
            ->join('ts_products as p3', function ($join) {
                $join->on('p3.ccc_family_cd', '=', 'p2.ccc_family_cd')
                    ->on('p3.sale_start_date', '=', 'p2.sale_start_date')
                    ->on('p3.product_type_id', '=', 'p2.product_type_id')
                    ->on('p3.work_id', '=', 'p2.work_id');
            })
            ->whereRaw(DB::raw(' item_cd not like \'_1__\' '))
            ->whereRaw(DB::raw(' item_cd not like \'__20\' '))
            ->whereRaw(DB::raw(' service_id in  (\'tol\')'))
            ->groupBy(DB::raw($selectSubGroupingFinal));
        $this->dbObject = DB::table(DB::raw("({$subQueryFinal->toSql()}) as final"))
            ->mergeBindings($subQueryFinal)
            ->join('ts_products as p4', 'final.product_unique_id', '=', 'p4.product_unique_id')
            ->join('ts_works as w1', 'final.work_id', '=', 'w1.work_id');
        if ($mediaFormat == 1) {
            $this->dbObject
                ->where('p4.media_format_id', '<>', $workRepsitory::HIMO_MEDIA_FORMAT_ID);

        } else if ($mediaFormat == 2) {
            $this->dbObject
                ->where('p4.media_format_id', $workRepsitory::HIMO_MEDIA_FORMAT_ID);

        }
        if(!empty($saleStartDateFrom)) {
            $this->dbObject
                ->where('p4.sale_start_date', '>=',$saleStartDateFrom);
        }
        if(!empty($saleStartDateTo)) {
            $this->dbObject
                ->where('p4.sale_start_date', '<=',$saleStartDateTo);
        }

        if ($order === 'new') {
            $this->dbObject
                ->orderBy('p4.sale_start_date', 'desc')
                ->orderBy('p4.product_unique_id', 'desc')
            ;
        } else if ($order === 'old') {
            $this->dbObject
                ->orderBy('p4.sale_start_date', 'asc')
                ->orderBy('p4.product_unique_id', 'asc')
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
