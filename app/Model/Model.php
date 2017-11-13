<?php
/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/11/13
 * Time: 18:34
 */

namespace App\Model;

use Illuminate\Support\Facades\DB;

class Model
{
    protected $table;
    protected $dbObject;
    protected $limit;
    protected $offset;

    function __construct($table)
    {
        $this->table = $table;
    }

    public function update($id, $values)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->update($values);
    }
    public function count()
    {
        return $this->dbObject->count();
    }
    public function get($limit = 100, $offset = 0)
    {
        return $this->dbObject
            ->skip($offset)->take($limit)
            ->get();
    }
    public function conditionAll()
    {
        $this->dbObject = DB::table($this->table);
        return $this;
    }
    public function getOne()
    {
        return $this->dbObject->first();
    }
}