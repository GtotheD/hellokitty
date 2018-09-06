<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Repositories\WorkRepository;

class OneTimeCoupon extends Model
{
    const TABLE = 'ts_one_time_coupons';

    function __construct($table = self::TABLE)
    {
        parent::__construct($table);
    }

    public function setConditionByStoreCdAndTokuban($storeCd, $tokuban)
    {
        $this->dbObject = DB::table($this->table)
            ->where([
                'store_cd' => $storeCd,
                'tokuban' => $tokuban
            ]);
        return $this;
    }

    public function setConditionByStoreCdAndDeliveryDt($storeCd)
    {
        $this->dbObject = DB::table($this->table)
            ->where('store_cd', '=', $storeCd)
            ->whereRaw('delivery_start_date <= now()')
            ->whereRaw('delivery_end_date >= now()');

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
                    $insertData[$key][$column] = array_get($row, $column) ?: '';
                }
            }
            $insertData[$key]['created_at'] = date('Y-m-d H:i:s');
        }
        return $this->bulkInsertOnDuplicateKey($insertData, "delivery_start_date = VALUES(delivery_start_date), delivery_end_date = VALUES(delivery_end_date)");
    }

}
