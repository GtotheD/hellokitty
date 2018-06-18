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
            ->where('keyword', 'like', "{$keyword}%")
            ->orWhere('roman_alphabet', 'like', "{$keyword}%")
            ->orWhere('hiragana', 'like', "{$keyword}%")
            ->orWhere('katakana', 'like', "{$keyword}%")
            ->orderBy('weight', 'desc')
            ->orderBy('keyword');
        return $this;
    }

}
