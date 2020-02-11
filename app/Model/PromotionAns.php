<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PromotionAns extends Model
{
    const TABLE = 'ts_mst_promotion_ans';

    function __construct($connection = null)
    {
        parent::__construct(self::TABLE);
        $this->setConnection($connection);
    }

    public function setConditionQesIds($qes_id_arr)
    {
        $this->dbObject = $this->connection->table($this->table)
            ->whereIn('qes_id', $qes_id_arr);
        return $this;
    }
}
