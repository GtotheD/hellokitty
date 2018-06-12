<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class CheckHimoTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CheckHimoTables {--test} {--dir=}';

    private $testData = [];
    /**
     * /**
     * Create a new command instance.
     **/
    public function __construct()
    {
        parent::__construct();
        for ($i=1;$i <= 100000; $i++) {
            $this->testData[] = $i;
        }

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
        $loopPerOnece = 1000;  // 一度のループで処理する件数
        $offset = 0;
        foreach ($himoTables as $table) {
            $this->info($i++.':'.$table);
            $method = camel_case($table);
            $loopPerOnece = 1000;  // 一度のループで処理する件数
            $offset = 0;
            while (true) {
                $this->info('Limit: '.$loopPerOnece);
                $this->info('Offset: '.$offset);
                $result = $this->$method($loopPerOnece, $offset);
                // 更新があれば処理を実行
                // テーブルによって関連テーブルを紐づけwork_idを抽出しテーブルに格納していく。
                // 取得できなくなるまで実行
                if (empty($result)) {
                    break;
                }
                $offset += $loopPerOnece;
            }
        }
        // 全てのテーブルをチェックしてテーブルを確認後、対象のwork_idに関連するテーブルからデータを削除する。


        return true;
    }


    function himoAreas($limit, $offset)
    {
        $workIds = array_slice($this->testData, $offset, $limit);
        return $workIds;
    }

    function himoCountries($limit, $offset)
    {
        $workIds = [];
        return $workIds;
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
