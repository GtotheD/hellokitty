<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PromotionPrize extends Model
{
    const TABLE = 'ts_mst_promotion_prize';
    protected $primaryKey = ['promotion_id', 'sort'];
    public $incrementing = false;

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
}
