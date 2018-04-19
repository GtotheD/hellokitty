<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RelatedPeople extends Model
{
    const TABLE = 'ts_related_people';

    function __construct()
    {
        parent::__construct(self::TABLE);
    }

    /*
     * Get Newest Product
     */
    public function setConditionByPeople($peopleId)
    {
        $this->dbObject = DB::table($this->table)
            ->where([
                'people_id' => $peopleId,
            ])

            ->orderBy('updated_at', 'desc');

        return $this;
    }

    public function insert($insertData)
    {
        $insertData['updated_at'] = date('Y-m-d H:i:s');

        $dbObject = DB::table($this->table);

        $count = $dbObject->where('people_id' , $insertData['people_id'])
            ->where('person_id' , $insertData['person_id'])
            ->count();
        if($count) {
            return $dbObject->where('people_id' , $insertData['people_id'])
                ->where('person_id' , $insertData['person_id'])
                ->update($insertData);
        } else {
            $insertData['created_at'] = date('Y-m-d H:i:s');
            return DB::table($this->table)->insertGetId($insertData);
        }
    }

    public function insertBulk($relatedPeople)
    {
        $insertData = [];
        $ignoreColumn = ['id', 'created_at', 'updated_at'];
        $columns = Schema::getColumnListing(self::TABLE);

        foreach ($relatedPeople as $key =>  $row) {
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