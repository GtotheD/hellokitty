<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RelateadWork extends Model
{
    const TABLE = 'ts_related_works';

    function __construct()
    {
        parent::__construct(self::TABLE);
    }

    /*
     * Get Newest Product
     */
    public function setConditionByWork($workId)
    {
        $this->dbObject = DB::table($this->table)
            ->where([
                ['work_id' ,'=', $workId],
                ['related_work_id' ,'<>', $workId],
            ])
            ->orderBy('updated_at', 'desc');

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

    public function insertBulk ($data)
    {
        $insertData = [];
        $ignoreColumn = ['id', 'created_at', 'updated_at'];
        $columns = Schema::getColumnListing(self::TABLE);
        foreach ($data as $key => $row) {
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
