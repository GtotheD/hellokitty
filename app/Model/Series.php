<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Series extends Model
{
    const TABLE = 'ts_series';

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

    public function setConditionGetWorksByWorkId($workId, $saleType = null)
    {
        $selectSubGrouping =
            'p.work_id,'
            .'product_type_id';
        $subQuery = DB::table('ts_products AS p')->select(DB::raw($selectSubGrouping))
            ->where('s.work_id', $workId)
            ->whereRaw(DB::raw(' item_cd not like \'_1__\' '))
            ->whereRaw(DB::raw(' service_id  in  (\'tol\', \'st\')'))
            ->join($this->table . ' AS s', 's.related_work_id', '=', 'p.work_id');
            if ($saleType === 'sell') {
                $subQuery->where('p.product_type_id', '1')
                    ->orWhereRaw(DB::raw(' (p.product_type_id = \'\' AND p.service_id = \'st\') '));
            } elseif ($saleType === 'rental') {
                $subQuery->where('p.product_type_id', '2')
                    ->orWhereRaw(DB::raw(' (p.product_type_id = \'\' AND p.service_id = \'st\') '));
            } elseif ($saleType === 'theater') {
                $subQuery->where('p.product_type_id', '2')
                    ->orWhereRaw(DB::raw(' (p.product_type_id = \'\' AND p.service_id = \'st\') '));
            }
            $subQuery->groupBy(DB::raw($selectSubGrouping));
        $this->dbObject = DB::table(DB::raw("({$subQuery->toSql()}) as t1"))
            ->join('ts_works as w', 'w.work_id', '=', 't1.work_id')
            ->mergeBindings($subQuery);
        return $this;
    }

    public function insert($insertData)
    {
        $insertData['updated_at'] = date('Y-m-d H:i:s');

        $dbObject = DB::table($this->table);

        $count = $dbObject->where('small_series_id' , $insertData['small_series_id'])
            ->where('work_id' , $insertData['work_id'])
            ->count();
        if($count) {
            return $dbObject->where('small_series_id' , $insertData['small_series_id'])
                ->where('work_id' , $insertData['work_id'])
                ->update($insertData);
        } else {
            $insertData['created_at'] = date('Y-m-d H:i:s');
            return DB::table($this->table)->insertGetId($insertData);
        }
    }

    public function insertBulk ($series)
    {
        $insertData = [];
        $ignoreColumn = ['id', 'created_at', 'updated_at'];
        $columns = Schema::getColumnListing(self::TABLE);
        foreach ($series as $key => $row) {
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
