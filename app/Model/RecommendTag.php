<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RecommendTag extends Model
{
    const TABLE = 'ts_recommend_tag';

    function __construct($connection = null)
    {
        parent::__construct(self::TABLE);
        $this->setConnection($connection);
    }

    public function setConditionByTag($tag)
    {
        // コネクションを切り替えるために、メンバ変数のコネクションを利用する。
        $this->dbObject = $this->connection->table($this->table)
            ->where([
                'tag' => $tag,
            ]);
        return $this;
    }

    public function insert($data)
    {
       DB::table(self::TABLE)->insert($data);
    }
}
