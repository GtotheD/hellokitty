<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PeopleRelatedWork extends Model
{
    const TABLE = 'ts_people_related_works';

    function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function setConditionById($personId)
    {
        $this->dbObject = DB::table($this->table)
            ->where('person_id', $personId);
        return $this;
    }

    public function insert($insertData)
    {
        $insertData['updated_at'] = date('Y-m-d H:i:s');
        $dbObject = DB::table($this->table);
        $count = $dbObject->where('person_id' , $insertData['person_id'])->count();
        if($count) {
            return false;
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