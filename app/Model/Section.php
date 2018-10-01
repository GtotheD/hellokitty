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
            ->select([
                'ts_sections.code',
                'ts_sections.url_code',
                'ts_sections.rental_start_date',
                'ts_sections.rental_end_date',
                'ts_sections.sale_start_date',
                'ts_sections.sale_end_date',
                'ts_sections.image_url',
                'ts_sections.title',
                'ts_sections.supplement'
                    ])
            ->join('ts_structures', function ($join) use ($goodsType, $saleType, $sectionFileName) {
                $join->on('ts_structure_id', '=', 'ts_structures.id')
                    ->where([
                        'goods_type' => $goodsType,
                        'ts_structures.sale_type' => $saleType,
                        'section_file_name' => $sectionFileName
                    ]);
            })
            // add inoue
            ->whereRaw("(ts_structures.display_start_date <= ". DB::raw('now()') .
                " or ts_structures.display_start_date = '0000-00-00 00:00:00')" )
            ->whereRaw("(ts_structures.display_end_date > ". DB::raw('now()') .
                " or ts_structures.display_end_date = '0000-00-00 00:00:00')" )
            ->whereRaw("(ts_sections.display_start_date <= ". DB::raw('now()') .
                " or ts_sections.display_start_date = '0000-00-00 00:00:00')" )
            ->whereRaw("(ts_sections.display_end_date > ". DB::raw('now()') .
                " or ts_sections.display_end_date = '0000-00-00 00:00:00')" );
        return $this;
    }

    public function setConditionByTsStructureId($tsStructureId)
    {
        $this->dbObject = DB::table($this->table)
            ->select(['*'])
            ->where('ts_structure_id', '=' , $tsStructureId)
            ->where('title', '<>', '')
            // add inoue
            ->whereRaw("(display_start_date <= ". DB::raw('now()') .
                " or display_start_date = '0000-00-00 00:00:00')" )
            ->whereRaw("(display_end_date > ". DB::raw('now()') .
                " or display_end_date = '0000-00-00 00:00:00')" );
        return $this;
    }

    public function conditionNoUrlCode()
    {
        $this->dbObject = DB::table($this->table)
            ->where('code', '<>', '')
            ->where('title', '=', '');
        return $this;
    }

    public function conditionNoWorkIdActiveRow()
    {
        $this->dbObject = DB::table($this->table . ' AS t1')
            ->join('ts_structures AS t2', function ($join) {
                $join->on('t1.ts_structure_id', '=', 't2.id');
            })
            ->where('code', '<>', '')
            ->where('work_id', '=', '');
        return $this;
    }

}