<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Class PointDetails
 * 期間固定Tポイント情報
 * @package App\Model
 */
class PointDetails extends Model
{
    const TABLE = 'ts_point_details';

    function __construct()
    {
        parent::__construct(self::TABLE);
    }

    /**
     * ST内部管理番号に応じたポイント情報を取得する
     */
    public function setConditionBySt($st)
    {
        $this->dbObject = DB::table($this->table)
            ->where([
                'st' => $st,
            ]);
        return $this;
    }

    /**
     * @param $st
     * @param $param
     * @return $this
     */
    public function update($st, $param)
    {
        $this->dbObject = DB::table($this->table)
            ->where([
                'st' => $st,
            ])->update(

            );
        return $this;
    }

    /**
     * Insert bulk records
     *
     * @param array $data
     *
     * @return mixed
     */
    public function insertBulk($data)
    {
        $insertData = [];
        $ignoreColumn = ['created_at', 'updated_at'];
        $columns = Schema::getColumnListing(self::TABLE);

        foreach ($data as $key => $row) {
            $insertData[$key]['updated_at'] = date('Y-m-d H:i:s');
            foreach ($columns as $column) {
                if (!in_array($column, $ignoreColumn)) {
                    $insertData[$key][$column] = array_get($row, $column, '');
                }
            }
            $insertData[$key]['created_at'] = date('Y-m-d H:i:s');
        }
        $update = 'membership_type = VALUES(membership_type),' .
        'point = VALUES(point),' .
        'fixed_point_total = VALUES(fixed_point_total),' .
        'fixed_point_min_limit_time = VALUES(fixed_point_min_limit_time),' .
        'updated_at = VALUES(updated_at)';
        return $this->bulkInsertOnDuplicateKey($insertData, $update);
    }
}
