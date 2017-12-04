<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/27
 * Time: 19:47
 */
class Section extends Model
{
    const TABLE = 'ts_sections';

    const TYPE_BANNER = 1; // 1 バナー
    const TYPE_PRODUCT = 2; // 2 商品一覧
    const TYPE_FAVORITE = 3; // 3 お気に入り
    const TYPE_HISTORY = 4; // 4 閲覧履歴（チェックした作品)
    const TYPE_RECOMMEND = 5; // 5 PDMPレコメンド

    function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function conditionSectionFromStructure($goodsType, $saleType, $sectionFileName)
    {
        $this->dbObject = DB::table($this->table)
            ->select('ts_sections.*')
            ->join('ts_structures', function ($join) use ($goodsType, $saleType, $sectionFileName) {
                $join->on('ts_structure_id', '=', 'ts_structures.id')
                    ->where([
                        'goods_type' => $goodsType,
                        'sale_type' => $saleType,
                        'section_file_name' => $sectionFileName
                    ]);

            })

            // add inoue
            ->where('ts_structures.display_start_date', '<', DB::raw('now()'))
            ->orWhere('ts_structures.display_start_date', '=', '0000-00-00 00:00:00')
            ->where('ts_structures.display_end_date', '>', DB::raw('now()'))
            ->orWhere('ts_structures.display_end_date', '=', '0000-00-00 00:00:00')

            ->where('ts_sections.display_start_date', '<', DB::raw('now()'))
            ->orWhere('ts_sections.display_start_date', '=', '0000-00-00 00:00:00')
            ->where('ts_sections.display_end_date', '>', DB::raw('now()'))
            ->orWhere('ts_sections.display_end_date', '=', '0000-00-00 00:00:00');
        return $this;
    }

    public function conditionNoUrlCode()
    {
        $this->dbObject = DB::table($this->table)
            ->where('code', '<>', '')
            ->where('title', '=', '');
        return $this;
    }

}