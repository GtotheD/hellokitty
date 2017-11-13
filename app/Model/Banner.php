<?php
/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/11/13
 * Time: 18:27
 */

namespace App\Model;

use Illuminate\Support\Facades\DB;
use App\Model\Model;

class Banner extends Model
{
    const TABLE = 'ts_banners';

    protected $dbObject;
    protected $limit;
    protected $offset;

    public function conditionSectionBanner($bannerName)
    {
        $this->dbObject = DB::table(self::TABLE)
            ->select(['ts_banners.*','banner_width','banner_height'])
            ->join('ts_structures', function ($join) use ($bannerName) {
                $join->on('ts_structure_id', '=', 'ts_structures.id')
                    ->where([
                        'section_type' => 1,
                        'section_file_name' => $bannerName
                    ])
                    ->where('ts_structures.display_start_date', '<', DB::raw('now()'))
                    ->where('ts_structures.display_end_date', '>', DB::raw('now()'));
            });
        return $this;
    }
}