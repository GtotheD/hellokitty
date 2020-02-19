<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PromotionWork extends Model
{
    const TABLE = 'ts_mst_promotion_works';
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

    public function setConditionByJan($jan)
    {
        $this->dbObject = $this->connection->table($this->table)
            ->where([
                'jan' => $jan,
            ]);
        return $this;
    }

    public function setConditionByWorkId($workId, $saleType)
    {
        if ($saleType === 'rental') {
            $len = 9;
        } else {
            $len = 13;
        }
        $this->dbObject = $this->connection->table($this->table)
            ->where('work_Id', $workId)
            ->whereRaw('char_length(jan) = '. $len);
        return $this;
    }
}
