<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Promotion extends Model
{
    const TABLE = 'ts_mst_promotion';

    function __construct($connection = null)
    {
        parent::__construct(self::TABLE);
        $this->setConnection($connection);
    }

    public function setConditionByPromotionId($prom_id = null)
    {
        $this->dbObject = $this->connection->table($this->table)
            ->where('id', $prom_id);
        return $this;
    }

    public function setConditionByPromotionIds($prom_ids = null)
    {
        $this->dbObject = $this->connection->table($this->table)
            ->whereIn('id', $prom_ids);
        return $this;
    }
}
