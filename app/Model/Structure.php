<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/27
 * Time: 19:47
 */
class Structure extends Model
{
    const TABLE = 'ts_structures';

    function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function set($goodsType, $saleType, $fileName = null)
    {
        $this->dbObject = DB::table($this->table)
            ->where([
                'goods_type' => $goodsType,
                'sale_type' => $saleType
            ])
            ->where('display_start_date', '<', DB::raw('now()'))
            ->where('display_end_date', '>', DB::raw('now()'));
        if ($fileName) {
            $this->dbObject->where('section_file_name', $fileName);
        }
        $this->dbObject->orderBy('sort', 'asc');
        return $this;
    }

    public function condtionFindFilename($goodsType, $saleType, $fileName)
    {
        $this->dbObject = DB::table($this->table)
            ->where([
                'goods_type' => $goodsType,
                'sale_type' => $saleType
            ]);
        if ($fileName) {
            $this->dbObject->where('section_file_name', $fileName);
        }
        return $this;
    }

    public function conditionFindBannerWithSectionFileName($fileName)
    {
        $this->dbObject = DB::table($this->table)
            ->where('section_file_name', $fileName)
            ->where(function ($query) {
                $query->where('section_type', 1)
                    ->orWhere('section_type', 99);
            });
        return $this;
    }

    public function setConditionFindBySectionfilenameWithDispTime($sectionFileName)
    {
        $this->dbObject = DB::table($this->table)
            ->select(['id','banner_width','banner_height'])
            ->where(['section_file_name' => $sectionFileName])
            // add inoue
            ->where('ts_structures.display_start_date', '<', DB::raw('now()'))
            ->orWhere('ts_structures.display_start_date', '=', '0000-00-00 00:00:00')
            ->where('ts_structures.display_end_date', '>', DB::raw('now()'))
            ->orWhere('ts_structures.display_end_date', '=', '0000-00-00 00:00:00')
            ->groupBy(['id']);

        return $this;
    }
}