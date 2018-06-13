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
                    $work[] = ['work_id' => $work->himo_work_pk];
                }
                // テーブルによって関連テーブルを紐づけwork_idを抽出しテーブルに格納していく。
                $this->himoUpdateWork->bulkInsertOnDuplicateKey($result);
                $offset += $loopPerOnce;
            }
        }
        // 全てのテーブルをチェックしてテーブルを確認後、対象のwork_idに関連するテーブルからデータを削除する。


        return true;
    }

    // 更新日以降で取得する。
    function himoCountries($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hwp.himo_work_pk')
            ->join('himo_product_countries AS hpc', 'hp.himo_product_pk', '=', 'hpc.himo_product_pk')
            ->join('himo_countries AS hc', 'hpc.country_id', '=', 'hc.id')
            ->join('himo_work_products AS hwp', 'hp.himo_product_pk', '=', 'hwp.himo_product_pk')
            ->whereBetween('hc.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hwp.himo_work_pk', $targetWorksArray)
            ->groupBy('hwp.himo_work_pk')
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

    function himoProducts($targetWorksArray)
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

    function himoWorkCountries($targetWorksArray)
    {
        $workIds = [];
        return $workIds;
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

    function himoWorks($targetWorksArray)
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
