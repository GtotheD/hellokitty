<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Model\HimoUpdateWork;
use Illuminate\Support\Carbon;
use App\Model\Work;

class CheckHimoTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CheckHimoTables {--test} {--dir=}';

    const HIMO_DB_SETTING_NAME = 'mysql_himo';

    private $himoUpdateWork;
    private $lastUpdateDateStart;
    private $lastUpdateDateEnd;

    /**
     * /**
     * Create a new command instance.
     **/
    public function __construct()
    {
        parent::__construct();
        $this->himoUpdateWork = new HimoUpdateWork;
        $this->lastUpdateDateStart = Carbon::yesterday()->format('Y-m-d 05:00:00');
        $this->lastUpdateDateEnd = Carbon::today()->format('Y-m-d 05:00:00');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Start');
        // テーブルリストを取得
        $himoTables = config('himo_tables');
        // テーブル一つづつ更新を確認
        $i =1;
        $workModel = new Work;
        DB::table($this->himoUpdateWork::TABLE)->truncate();
        foreach ($himoTables as $table) {
            $this->info($i++.':'.$table);
            $method = camel_case($table);
            $targetWorks = null;
            $loopPerOnce = 100;  // 一度のループで処理する件数
            $offset = 0;
            while (true) {
                $this->info('Offset: '.$offset.' Limit: '.$loopPerOnce);
                $targetWorks = $workModel->conditionAll()->selectCamel(['work_id'])->get($loopPerOnce, $offset);
                // 取得できなくなるまで実行
                if (count($targetWorks) == 0) {
                    break;
                }
                foreach ($targetWorks as $targetWork) {
                    $targetWorksArray[] = $targetWork->workId;
                }
                $result = $this->$method($targetWorksArray);
                foreach ($result as $work) {
                    $workList[] = ['work_id' => $work->himo_work_id];
                }
                // テーブルによって関連テーブルを紐づけwork_idを抽出しテーブルに格納していく。
                $this->himoUpdateWork->bulkInsertOnDuplicateKey($workList);
                $offset += $loopPerOnce;
            }
        }
        // 全てのテーブルをチェックしてテーブルを確認後、対象のwork_idに関連するテーブルからデータを削除する。


        return true;
    }


    function himoWorks($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->where('hw.delete_flg', '=', 0)
            ->whereBetween('hw.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoWorkCountries($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_work_countries AS hwc', function ($join) {
                $join->on('hw.himo_work_pk', '=', 'hwc.himo_work_pk')
                    ->where('hwc.delete_flg', '=', 0);
            })
            ->join('himo_countries AS hc', function ($join) {
                $join->on('hwc.country_id', '=', 'hc.id')
                    ->where('hc.delete_flg', '=', 0);
            })
            ->whereBetween('hwc.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoWorkDocs($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoWorkEpisodes($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoWorkProducts($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoWorkRelations($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoWorkScenes($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoWorkTypes($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoWorkWorks($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }


    function himoProducts($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk')
                 ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk')
                 ->where('hw.delete_flg', '=', 0);
            })
            ->where('hp.delete_flg', '=', 0)
            ->whereBetween('hp.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoProductCountries($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoProductDevices($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoProductDocs($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoProductGenres($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoProductPeople($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoProductScenes($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoProductTracks($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoProductTypes($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }


    // todo:works側もやる
    function himoCountries($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_product_countries AS hpc', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpc.himo_product_pk')
                    ->where('hpc.delete_flg', '=', 0);
            })
            ->join('himo_countries AS hc', function ($join) {
                $join->on('hpc.country_id', '=', 'hc.id')
                    ->where('hc.delete_flg', '=', 0);
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk')
                 ->where('hw.delete_flg', '=', 0);
            })
            ->whereBetween('hc.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoDevices($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoDocSources($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoDocTypes($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoDocs($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoGameModels($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoGenres($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoItems($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoJobs($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoMakers($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoMediaCategories($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoMediaFormats($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoPeople($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }


    function himoRatings($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoRoles($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoServices($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoBigSeries($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoBigSeriesSmallSeries($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoSmallSeries($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoSmallSeriesWorks($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoTrackPeople($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoTracks($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoXmediaBigSeries($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoXmediaRelationTypes($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

    function himoXmedias($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
    }

}
