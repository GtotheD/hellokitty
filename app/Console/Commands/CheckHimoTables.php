<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Model\HimoUpdateWork;
use Illuminate\Support\Carbon;

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
        $this->lastUpdateDateStart = $dt = Carbon::yesterday()->format('Y-m-d 05:00:00');
        $this->lastUpdateDateEnd = $dt = Carbon::today()->format('Y-m-d 05:00:00');
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
        $loopPerOnce = 1000;  // 一度のループで処理する件数
        $offset = 0;
        foreach ($himoTables as $table) {
            $this->info($i++.':'.$table);
            $method = camel_case($table);
            $loopPerOnce = 1000;  // 一度のループで処理する件数
            $offset = 0;
            while (true) {
                $this->info('Offset: '.$offset.' Limit: '.$loopPerOnce);
                $result = $this->$method($loopPerOnce, $offset);
                // 取得できなくなるまで実行
                if (empty($result)) {
                    break;
                }
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
    function himoCountries($limit, $offset)
    {
        $dbObject = DB::table('himo_products AS hp')
            ->select('hwp.himo_work_pk')
            ->join('himo_product_countries AS hpc', 'hp.himo_product_pk', '=', 'hpc.himo_product_pk')
            ->join('himo_countries AS hc', 'hpc.country_id', '=', 'hc.id')
            ->join('himo_work_products AS hwp', 'hp.himo_product_pk', '=', 'hwp.himo_product_pk')
            ->whereBetween('hc.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->skip($offset)->take($limit)
            ->groupBy('work_id');
        return $dbObject->get();
    }

    function himoDevices($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoDocSources($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoDocTypes($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoDocs($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoGameModels($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoGenres($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoItems($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoJobs($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoMakers($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoMediaCategories($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoMediaFormats($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoPeople($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoProductCountries($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoProductDevices($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoProductDocs($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoProductGenres($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoProductPeople($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoProductScenes($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoProductTracks($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoProductTypes($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoProducts($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoRatings($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoRoles($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoServices($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoBigSeries($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoBigSeriesSmallSeries($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoSmallSeries($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoSmallSeriesWorks($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoTrackPeople($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoTracks($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoWorkCountries($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoWorkDocs($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoWorkEpisodes($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoWorkProducts($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoWorkRelations($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoWorkScenes($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoWorkTypes($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoWorkWorks($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoWorks($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoXmediaBigSeries($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoXmediaRelationTypes($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

    function himoXmedias($limit, $offset)
    {
        $workIds = [];
        return $workIds;
    }

}
