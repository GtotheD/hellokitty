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

    /*
     * Get Newest Product
     */
    public function setConditionByProduct($productUniqueId)
    {
        $this->dbObject = DB::table($this->table)
            ->where([
                'product_unique_id' => $productUniqueId,
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
}
