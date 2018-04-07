<?php
/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/11/13
 * Time: 18:34
 */

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

    public function select($column)
    {
        $this->dbObject->select($column);
        return $this;
    }

    public function update($id, $values)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->update($values);
    }

    public function limit($limit)
    {
        return $this->dbObject->limit($limit);
    }

    public function offset($offset)
    {
        return $this->dbObject->offset($offset);
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

    public function toCamel($ignoreColumn = [])
    {
        $this->dbObject->select(DB::raw($this->camelCaseColumn($ignoreColumn)));
        return $this;
    }

    private function camelCaseColumn($ignoreColumn = [])
    {
        $columns = Schema::getColumnListing($this->table);
        return $this->convertCamelCase($columns, $ignoreColumn);
    }

    public function selectCamel($columns)
    {
        $this->dbObject->select(DB::raw($this->convertCamelCase($columns)));
        return $this;
    }

    private function convertCamelCase($columns, $ignoreColumn = [])
    {
        foreach ($columns as $column) {
            if(!in_array($column, $ignoreColumn)) {
                $aliasName[] = $column. ' AS '. camel_case($column);
            }
        }
        return implode($aliasName, ',');
    }
}