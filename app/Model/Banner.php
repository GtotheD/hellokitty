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

    const NORMAL_BANNER = 1;
    const FIXED_BANNER = 99;

    const LOGIN_TYPE_LOGGED_IN = 0;
    const LOGIN_TYPE_NO_LOGGED_IN = 1;
    const LOGIN_TYPE_ALL= 2;

    private $loginType ;

    function __construct()
    {
        parent::__construct(self::TABLE);
        $this->loginType = self::LOGIN_TYPE_ALL;
    }

    /**
     * @param mixed $loginType
     */
    public function setLoginType($loginType)
    {
        $this->loginType = $loginType;
    }

    public function conditionSectionBanner($bannerName)
    {
        $this->dbObject = DB::table(self::TABLE)
            ->select(['ts_banners.*','banner_width','banner_height'])
            ->join('ts_structures', function ($join) use ($bannerName) {
                $join->on('ts_structure_id', '=', 'ts_structures.id')
                    ->where([
                        'section_type' => self::NORMAL_BANNER,
                        'section_file_name' => $bannerName,
                    ])
                    ->where(function ($query) {
                        $query->where('login_type', self::LOGIN_TYPE_ALL)
                            ->orWhere('login_type', $this->loginType);
                    });
            })
            ->where('ts_structures.display_start_date', '<', DB::raw('now()'))
            ->orWhere('ts_structures.display_start_date', '=', '0000-00-00 00:00:00')
            ->where('ts_structures.display_end_date', '>', DB::raw('now()'))
            ->orWhere('ts_structures.display_end_date', '=', '0000-00-00 00:00:00')
            ->where('ts_banners.display_start_date', '<', DB::raw('now()'))
            ->orWhere('ts_banners.display_start_date', '=', '0000-00-00 00:00:00')
            ->where('ts_banners.display_end_date', '>', DB::raw('now()'))
            ->orWhere('ts_banners.display_end_date', '=', '0000-00-00 00:00:00');
        return $this;
    }

    public function conditionSectionFixedBanner($bannerName)
    {
        $this->dbObject = DB::table(self::TABLE)
            ->select(['ts_banners.*','banner_width','banner_height'])
            ->join('ts_structures', function ($join) use ($bannerName) {
                $join->on('ts_structure_id', '=', 'ts_structures.id')
                    ->where([
                        'section_type' => self::FIXED_BANNER,
                        'section_file_name' => $bannerName,
                    ])
                    ->where(function ($query) {
                        $query->where('login_type', self::LOGIN_TYPE_ALL)
                            ->orWhere('login_type', $this->loginType);
                    });
            })
            ->where('ts_structures.display_start_date', '<', DB::raw('now()'))
            ->orWhere('ts_structures.display_start_date', '=', '0000-00-00 00:00:00')
            ->where('ts_structures.display_end_date', '>', DB::raw('now()'))
            ->orWhere('ts_structures.display_end_date', '=', '0000-00-00 00:00:00')
            ->where('ts_banners.display_start_date', '<', DB::raw('now()'))
            ->orWhere('ts_banners.display_start_date', '=', '0000-00-00 00:00:00')
            ->where('ts_banners.display_end_date', '>', DB::raw('now()'))
            ->orWhere('ts_banners.display_end_date', '=', '0000-00-00 00:00:00');
        return $this;
    }
}