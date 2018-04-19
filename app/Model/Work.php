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
class Work extends Model
{
    const TABLE = 'ts_works';

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

    public function setConditionByUrlCd($urlCd)
    {
        $this->dbObject = DB::table($this->table)
            ->where(['url_cd' => $urlCd]);
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
     * Insert bulk records
     *
     * @param array $data
     *
     * @return mixed
     */
    public function insertBulk($works = []) {
        $insertData = [];
        $ignoreColumn = ['id', 'created_at', 'updated_at'];
        $columns = Schema::getColumnListing(self::TABLE);
        foreach ($works as $key =>  $row) {
            $insertData[$key]['updated_at'] = date('Y-m-d H:i:s');
            foreach ($columns as $column) {
                if(!in_array($column, $ignoreColumn)) {
                    $insertData[$key][$column] = array_get($row, $column) ?: '';
                }
            }
            $insertData[$key]['created_at'] = date('Y-m-d H:i:s');
        }
        return DB::table($this->table)->insert(array_values($insertData));
    }
}
