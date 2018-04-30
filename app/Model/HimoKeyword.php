<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HimoKeyword extends Model
{
    const TABLE = 'ts_himo_keywords';

    function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function setConditionByKeyword($keyword)
    {
        $this->dbObject = DB::table($this->table)
            ->where('keyword', 'like', "命%")
            ->orderBy('weight')
            ->orderBy('keyword');
        return $this;
    }

}
