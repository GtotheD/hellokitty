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

    public function setConditionGetWorksByWorkId($workId, $saleTypeId = null)
    {
        $this->dbObject = DB::table($this->table . ' AS s')
            ->join('ts_works AS w', 'w.work_id', '=', 's.related_work_id')
            ->where([
                's.work_id' => $workId,
            ]);
        if ($saleTypeId) {
            $this->dbObject->whereExists(function ($query) use ($saleTypeId) {
                $query->select(DB::raw(1))
                    ->from('ts_products AS p')
                    ->whereRaw(DB::raw(' item_cd not like \'__20\' '))
                    ->whereRaw('w.work_id = p.work_id AND product_type_id ='.$saleTypeId);
            });
        }
        $this->dbObject->orderBy('sale_start_date', 'desc');
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
