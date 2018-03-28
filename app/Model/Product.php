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

    public function insert($workId, $data)
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
                    $insertData['work_id'] = $workId;
                    $insertData[$column] = $data[$column];
                }
            }
        }
        $count = $dbObject->where('product_id' , $data['product_id'])->count();
        if($count) {
            return $dbObject->where('product_id' , $data['product_id'])->update($insertData);
        } else {
            $insertData['created_at'] = date('Y-m-d H:i:s');
            return DB::table($this->table)->insertGetId($insertData);
        }
    }

}