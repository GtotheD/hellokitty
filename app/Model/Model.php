<?php
/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/11/13
 * Time: 18:34
 */

namespace App\Model;


class Model
{
    protected $dbObject;
    protected $limit;
    protected $offset;

    public function update($id, $values)
    {
        return DB::table(self::TABLE)
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
        $this->dbObject = DB::table(self::TABLE);
        return $this;
    }
}