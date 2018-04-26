<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class People extends Model
{
    const TABLE = 'ts_people';

    function __construct()
    {
        parent::__construct(self::TABLE);
    }

    /*
     * Get Newest Product
     */
    public function getNewestPeople($conditions)
    {
        $this->dbObject = DB::table($this->table);
        if ($productUniqueId = array_get($conditions, 'product_unique_id')) {
            $this->dbObject->where(['product_unique_id' => $productUniqueId]);
        }
        if ($roleId = array_get($conditions, 'role_id')) {
            $this->dbObject->where(['role_id' => $roleId]);
        }

        $this->dbObject->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit(1);
        return $this;
    }

    /**
     * @param $productUniqueId
     *
     * @return $this
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

    /**
     * @param $productUniqueId
     *
     * @return $this
     */
    public function setConditionByRoleId($productUniqueId, $roleId)
    {
        $this->dbObject = DB::table($this->table)
            ->where([
                'product_unique_id' => $productUniqueId,
                'role_id' => $roleId,
            ])
            ->orderBy('updated_at', 'desc');

        return $this;
    }

    public function insert($insertData)
    {
        $insertData['updated_at'] = date('Y-m-d H:i:s');

        $dbObject = DB::table($this->table);

        $count = $dbObject->where('product_unique_id' , $insertData['product_unique_id'])
            ->where('person_id' , $insertData['person_id'])
            ->count();
        if($count) {
            return $dbObject->where('product_unique_id' , $insertData['product_unique_id'])
                ->where('person_id' , $insertData['person_id'])
                ->update($insertData);
        } else {
            $insertData['created_at'] = date('Y-m-d H:i:s');
            return DB::table($this->table)->insertGetId($insertData);
        }
    }

    public function insertBulk($people)
    {
        $insertData = [];
        $ignoreColumn = ['id', 'created_at', 'updated_at'];
        $columns = Schema::getColumnListing(self::TABLE);

        foreach ($people as $key =>  $row) {
            $insertData[$key]['updated_at'] = date('Y-m-d H:i:s');
            foreach ($columns as $column) {
                if(!in_array($column, $ignoreColumn)) {
                    $insertData[$key][$column] = array_get($row, $column) ?: '';
                }
            }
            $insertData[$key]['created_at'] = date('Y-m-d H:i:s');
        }
        return DB::table($this->table)->insert($insertData);
    }
}