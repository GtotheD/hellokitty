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

    const SECTION_TYPE_BANNER = 1;
    const SECTION_TYPE_SPECIAL = 2;
    const SECTION_TYPE_FAVORITE = 3;
    const SECTION_TYPE_HISTORY = 4;
    const SECTION_TYPE_PDMP = 5;
    const SECTION_TYPE_PREMIUM_RECOMMEND = 6;
    const SECTION_TYPE_PREMIUM_PICKLE = 7;


    function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function setConditionTypes($goodsType, $saleType, $fileName = null)
    {
        $this->dbObject = DB::table($this->table)
            ->where([
                'goods_type' => $goodsType,
                'sale_type' => $saleType
            ])
            ->whereRaw("(ts_structures.display_start_date <= ". DB::raw('now()') .
                " or ts_structures.display_start_date = '0000-00-00 00:00:00')" )
            ->whereRaw("(ts_structures.display_end_date > ". DB::raw('now()') .
                " or ts_structures.display_end_date = '0000-00-00 00:00:00')" );
        if ($fileName) {
            $this->dbObject->where('section_file_name', $fileName);
        }
        $this->dbObject->orderBy('sort', 'asc');
        return $this;
    }

    public function conditionFindFilename($goodsType, $saleType, $fileName)
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

    public function conditionFindFilenameWithDispTime($goodsType, $saleType, $fileName, $sectionType = null)
    {
        $this->dbObject = DB::table($this->table)
            ->where([
                'goods_type' => $goodsType,
                'sale_type' => $saleType,
            ])
            ->whereRaw("(ts_structures.display_start_date <= ". DB::raw('now()') .
                " or ts_structures.display_start_date = '0000-00-00 00:00:00')" )
            ->whereRaw("(ts_structures.display_end_date > ". DB::raw('now()') .
                " or ts_structures.display_end_date = '0000-00-00 00:00:00')" );
        if ($fileName) {
            $this->dbObject->where('section_file_name', $fileName);
        }
        if ($sectionType) {
            $this->dbObject->where('section_type', $sectionType);
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
            ->whereRaw("(ts_structures.display_start_date <= ". DB::raw('now()') .
                " or ts_structures.display_start_date = '0000-00-00 00:00:00')" )
            ->whereRaw("(ts_structures.display_end_date > ". DB::raw('now()') .
                " or ts_structures.display_end_date = '0000-00-00 00:00:00')" )
            ->groupBy(['id','banner_width','banner_height']);

        return $this;
    }
}