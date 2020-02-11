<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PromotionWork extends Model
{
    const TABLE = 'ts_mst_promotion_works';

    function __construct($connection = null)
    {
        parent::__construct(self::TABLE);
        $this->setConnection($connection);
    }

    public function setConditionPromotionId($promotion_id)
    {
        $this->dbObject = $this->connection->table($this->table)
            ->where([
                'promotion_id' => $promotion_id,
            ]);
        return $this;
    }

    public function setConditionByJan($jan)
    {
        $this->dbObject = $this->connection->table($this->table)
            ->where([
                'jan' => $jan,
            ]);
        return $this;
    }
}
