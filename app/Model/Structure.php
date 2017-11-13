<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/27
 * Time: 19:47
 */
class Structure
{
    const TABLE = 'ts_structures';

    protected $dbObject;
    protected $limit;
    protected $offset;

    public function set($goodsType, $saleType, $fileName = null)
    {
        $this->dbObject = DB::table(self::TABLE)
            ->where([
                'goods_type' => $goodsType,
                'sale_type' => $saleType
            ])
            ->where('display_start_date', '<', DB::raw('now()'))
            ->where('display_end_date', '>', DB::raw('now()'));
        if ($fileName) {
            $this->dbObject->where('section_file_name', $fileName);
        }
        return $this;
    }

    public function conditionFindBannerWithSectionFileName($fileName)
    {
        $this->dbObject = DB::table(self::TABLE)
            ->where([
                'section_type' => 1,
                'section_file_name' => $fileName
            ]);
        return $this;
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

    public function getOne()
    {
        return $this->dbObject->first();
    }

}