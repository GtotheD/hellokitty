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
        $i = 1;
        $workModel = new Work;
        DB::table($this->himoUpdateWork::TABLE)->truncate();
        foreach ($himoTables as $table) {
            $this->info($i++ . ':' . $table);
            $method = camel_case($table);
            $targetWorks = null;
            $loopPerOnce = 100;  // 一度のループで処理する件数
            $offset = 0;
            $workList = [];
            while (true) {
                $this->info('Offset: ' . $offset . ' Limit: ' . $loopPerOnce);
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
                if (empty($workList)) {
                    $this->info('対象なし');
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
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_work_docs AS hwd', function ($join) {
                $join->on('hw.himo_work_pk', '=', 'hwd.himo_work_pk')
                    ->where('hwd.delete_flg', '=', 0);
            })
            ->whereBetween('hwd.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoWorkProducts($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hw.himo_work_pk', '=', 'hwp.himo_work_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->whereBetween('hwp.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoWorkRelations($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_work_relations AS hwr', function ($join) {
                $join->on('hw.himo_work_pk', '=', 'hwr.himo_work_pk')
                    ->where('hwr.delete_flg', '=', 0);
            })
            ->whereBetween('hwr.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoWorkScenes($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_work_scenes AS hws', function ($join) {
                $join->on('hw.himo_work_pk', '=', 'hws.himo_work_pk')
                    ->where('hws.delete_flg', '=', 0);
            })
            ->whereBetween('hws.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoWorkTypes($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_work_types AS hwt', function ($join) {
                $join->on('hw.work_type_id', '=', 'hwt.work_type_id')
                    ->where('hwt.delete_flg', '=', 0);
            })
            ->whereBetween('hwt.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoWorkWorks($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_work_works AS hww', function ($join) {
                $join->on('hw.himo_work_d', '=', 'hww.himo_work1_pk')
                    ->where('hww.delete_flg', '=', 0);
            })
            ->whereBetween('hww.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
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
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_product_countries AS hpc', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpc.himo_product_pk')
                    ->where('hpc.delete_flg', '=', 0);
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hw.delete_flg', '=', 0);
            })
            ->where('hp.delete_flg', '=', 0)
            ->whereBetween('hpc.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoProductDevices($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_product_devices AS hpd', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpd.himo_product_pk')
                    ->where('hpc.delete_flg', '=', 0);
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hw.delete_flg', '=', 0);
            })
            ->where('hp.delete_flg', '=', 0)
            ->whereBetween('hpd.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoProductDocs($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_product_docs AS hpd', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpd.himo_product_pk')
                    ->where('hpc.delete_flg', '=', 0);
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hw.delete_flg', '=', 0);
            })
            ->where('hp.delete_flg', '=', 0)
            ->whereBetween('hpc.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoProductGenres($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_product_genress AS hpg', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpg.himo_product_pk')
                    ->where('hpg.delete_flg', '=', 0);
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hw.delete_flg', '=', 0);
            })
            ->where('hp.delete_flg', '=', 0)
            ->whereBetween('hpg.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoProductPeople($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_product_peoples AS hpp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpp.himo_product_pk')
                    ->where('hpp.delete_flg', '=', 0);
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hw.delete_flg', '=', 0);
            })
            ->where('hp.delete_flg', '=', 0)
            ->whereBetween('hpp.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoProductScenes($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_product_scenes AS hps', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hps.himo_product_pk')
                    ->where('hps.delete_flg', '=', 0);
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hw.delete_flg', '=', 0);
            })
            ->where('hp.delete_flg', '=', 0)
            ->whereBetween('hps.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoProductTracks($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_product_tracks AS hpt', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpt.himo_product_pk')
                    ->where('hpt.delete_flg', '=', 0);
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hw.delete_flg', '=', 0);
            })
            ->where('hp.delete_flg', '=', 0)
            ->whereBetween('hpt.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoProductTypes($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_product_types AS hpt', function ($join) {
                $join->on('hp.product_type_id', '=', 'hpt.id')
                    ->where('hpt.delete_flg', '=', 0);
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hw.delete_flg', '=', 0);
            })
            ->where('hp.delete_flg', '=', 0)
            ->whereBetween('hpt.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
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
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_product_devices AS hpd', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpd.himo_product_pk')
                    ->where('hpd.delete_flg', '=', 0);
            })
            ->join('himo_devices AS hd', function ($join) {
                $join->on('hpd.device_id', '=', 'hd.id')
                    ->where('hd.delete_flg', '=', 0);
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hw.delete_flg', '=', 0);
            })
            ->where('hp.delete_flg', '=', 0)
            ->whereBetween('hd.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoDocSourcesToProduct($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_product_docs AS hpd', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpd.himo_product_pk')
                    ->where('hpd.delete_flg', '=', 0);
            })
            ->join('himo_docs AS hd', function ($join) {
                $join->on('hd.id', '=', 'hpd.himo_doc_pk')
                    ->where('hd.delete_flg', '=', 0);
            })
            ->join('himo_doc_sources AS hds', function ($join) {
                $join->on('hds.id', '=', 'hd.doc_source_id')
                    ->where('hd.delete_flg', '=', 0);
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hw.delete_flg', '=', 0);
            })
            ->where('hp.delete_flg', '=', 0)
            ->whereBetween('hd.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoDocSourcesToWork($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_work_docs AS hwd', function ($join) {
                $join->on('hw.himo_work_pk', '=', 'hwd.himo_work_pk')
                    ->where('hwd.delete_flg', '=', 0);
            })
            ->join('himo_docs AS hd', function ($join) {
                $join->on('hd.id', '=', 'hwd.himo_doc_pk')
                    ->where('hd.delete_flg', '=', 0);
            })
            ->join('himo_doc_sources AS hds', function ($join) {
                $join->on('hds.id', '=', 'hd.doc_source_id')
                    ->where('hd.delete_flg', '=', 0);
            })
            ->where('hw.delete_flg', '=', 0)
            ->whereBetween('hwd.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoDocTypesToProduct($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_product_docs AS hpd', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpd.himo_product_pk')
                    ->where('hpd.delete_flg', '=', 0);
            })
            ->join('himo_docs AS hd', function ($join) {
                $join->on('hd.id', '=', 'hpd.himo_doc_pk')
                    ->where('hd.delete_flg', '=', 0);
            })
            ->join('himo_doc_types AS hds', function ($join) {
                $join->on('hds.id', '=', 'hd.doc_type_id')
                    ->where('hd.delete_flg', '=', 0);
            })
            ->join('himo_works AS hdt', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hw.delete_flg', '=', 0);
            })
            ->where('hp.delete_flg', '=', 0)
            ->whereBetween('hdt.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoDocTypesToWork($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_work_docs AS hwd', function ($join) {
                $join->on('hw.himo_work_pk', '=', 'hwd.himo_work_pk')
                    ->where('hwd.delete_flg', '=', 0);
            })
            ->join('himo_docs AS hd', function ($join) {
                $join->on('hd.id', '=', 'hwd.himo_doc_pk')
                    ->where('hd.delete_flg', '=', 0);
            })
            ->join('himo_doc_types AS hdt', function ($join) {
                $join->on('hds.id', '=', 'hd.doc_type_id')
                    ->where('hd.delete_flg', '=', 0);
            })
            ->where('hw.delete_flg', '=', 0)
            ->whereBetween('hdt.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoDocsToProduct($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_product_docs AS hpd', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpd.himo_product_pk')
                    ->where('hpd.delete_flg', '=', 0);
            })
            ->join('himo_docs AS hd', function ($join) {
                $join->on('hd.id', '=', 'hpd.himo_doc_pk')
                    ->where('hd.delete_flg', '=', 0);
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hw.delete_flg', '=', 0);
            })
            ->where('hp.delete_flg', '=', 0)
            ->whereBetween('hd.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoDocsToWork($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_work_docs AS hwd', function ($join) {
                $join->on('hw.himo_work_pk', '=', 'hwd.himo_work_pk')
                    ->where('hwd.delete_flg', '=', 0);
            })
            ->join('himo_docs AS hd', function ($join) {
                $join->on('hd.id', '=', 'hwd.himo_doc_pk')
                    ->where('hd.delete_flg', '=', 0);
            })
            ->where('hw.delete_flg', '=', 0)
            ->whereBetween('hwd.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    // productだけ
    function himoGameModels($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_game_models AS hgm', function ($join) {
                $join->on('hp.game_model_id', '=', 'hgm.id')
                    ->where('hgm.delete_flg', '=', 0);
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hw.delete_flg', '=', 0);
            })
            ->where('hp.delete_flg', '=', 0)
            ->whereBetween('hgm.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoGenres($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_genres AS hg', function ($join) {
                $join->on('hg.id', '=', 'hpg.genre_pk')
                    ->where('hpg.delete_flg', '=', 0);
            })
            ->join('himo_product_genres AS hpg', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpg.himo_product_pk')
                    ->where('hpg.delete_flg', '=', 0);
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hw.delete_flg', '=', 0);
            })
            ->where('hp.delete_flg', '=', 0)
            ->whereBetween('hg.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    // productだけ
    function himoItems($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_items AS hi', function ($join) {
                $join->on('hp.item_cd', '=', 'hi.item_cd')
                    ->where('hgm.delete_flg', '=', 0);
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hw.delete_flg', '=', 0);
            })
            ->where('hp.delete_flg', '=', 0)
            ->whereBetween('hm.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    // productだけ
    // todo:ER上はhimo_makers.idをキーにしているが間違ってる？
    function himoMakers($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_makers AS hm', function ($join) {
                $join->on('hp.maker_cd', '=', 'hm.maker_cd')
                    ->where('hgm.delete_flg', '=', 0);
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hw.delete_flg', '=', 0);
            })
            ->where('hp.delete_flg', '=', 0)
            ->whereBetween('hm.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    // productだけ
    // todo:IDの確認
    function himoMediaCategories($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_media_categories AS hmc', function ($join) {
                $join->on('hp.media_category_cd', '=', 'hmc.id')
                    ->where('hmc.delete_flg', '=', 0);
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hw.delete_flg', '=', 0);
            })
            ->where('hp.delete_flg', '=', 0)
            ->whereBetween('hmc.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoMediaFormats($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_media_formats AS hmf', function ($join) {
                $join->on('hp.media_format_cd', '=', 'hmf.id')
                    ->where('hmf.delete_flg', '=', 0);
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hw.delete_flg', '=', 0);
            })
            ->where('hp.delete_flg', '=', 0)
            ->whereBetween('hmf.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    //
    function himoPeople($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_peoples AS hpe', function ($join) {
                $join->on('hpp.himo_person_pk', '=', 'hpe.himo_person_pk')
                    ->where('hpp.delete_flg', '=', 0);
            })
            ->join('himo_product_peoples AS hpp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpp.himo_product_pk')
                    ->where('hpp.delete_flg', '=', 0);
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hw.delete_flg', '=', 0);
            })
            ->where('hp.delete_flg', '=', 0)
            ->whereBetween('hpe.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoRatingsToWork($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_ratings AS hr', function ($join) {
                $join->on('hw.rating_id', '=', 'hr.id')
                    ->where('hr.delete_flg', '=', 0);
            })
            ->where('hw.delete_flg', '=', 0)
            ->whereBetween('hr.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoRatingsToProduct($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_ratings AS hr', function ($join) {
                $join->on('hp.rating_id', '=', 'hr.id')
                    ->where('hr.delete_flg', '=', 0);
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hw.delete_flg', '=', 0);
            })
            ->where('hp.delete_flg', '=', 0)
            ->whereBetween('hd.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoRoles($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_tracks AS ht', function ($join) {
                $join->on('ht.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_track_peoples AS htp', function ($join) {
                $join->on('htp.himo_track_pk', '=', 'ht.himo_track_pk')
                    ->where('hpp.delete_flg', '=', 0);
            })
            ->join('himo_roles AS hr', function ($join) {
                $join->on('hr.id', '=', 'htp.role_id')
                    ->where('hpp.delete_flg', '=', 0);
            })
            ->where('hw.delete_flg', '=', 0)
            ->whereBetween('hr.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoRoles2($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_product_peoples AS hpp', function ($join) {
                $join->on('hpp.himo_product_pk', '=', 'ht.himo_product_pk')
                    ->where('hpp.delete_flg', '=', 0);
            })
            ->join('himo_roles AS hr', function ($join) {
                $join->on('hr.id', '=', 'hpp.role_id')
                    ->where('hpp.delete_flg', '=', 0);
            })
            ->where('hw.delete_flg', '=', 0)
            ->whereBetween('hr.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoTrackPeople($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_tracks AS ht', function ($join) {
                $join->on('ht.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_track_peoples AS htp', function ($join) {
                $join->on('htp.himo_track_pk', '=', 'ht.himo_track_pk')
                    ->where('hpp.delete_flg', '=', 0);
            })
            ->where('hw.delete_flg', '=', 0)
            ->whereBetween('htp.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoTracksToWork($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_tracks AS ht', function ($join) {
                $join->on('ht.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->where('hw.delete_flg', '=', 0)
            ->whereBetween('ht.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoTracksToProduct($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_tracks AS ht', function ($join) {
                $join->on('ht.himo_track_pk', '=', 'hpt.himo_track_pk')
                    ->where('ht.delete_flg', '=', 0);
            })
            ->join('himo_product_tracks AS hpt', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpt.himo_product_pk')
                    ->where('hpt.delete_flg', '=', 0);
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk')
                    ->where('hwp.delete_flg', '=', 0);
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hw.delete_flg', '=', 0);
            })
            ->where('hp.delete_flg', '=', 0)
            ->whereBetween('hpt.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
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

    // himo_small_series ↓
    // himo_small_series_works ↓
    // himo_works
    function himoSmallSeries1($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_small_series_works AS hssw', function ($join) {
                $join->on('hssw.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hssw.delete_flg', '=', 0);
            })
            ->join('himo_small_series AS hss', function ($join) {
                $join->on('hss.id', '=', 'hssw.small_series_id')
                    ->where('hssw.delete_flg', '=', 0);
            })
            ->where('hw.delete_flg', '=', 0)
            ->whereBetween('hss.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    // himo_small_series ↓
    // himo_small_series_works ↓
    // himo_works
    function himoSmallSeries2($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_small_series_works AS hssw', function ($join) {
                $join->on('hssw.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hssw.delete_flg', '=', 0);
            })
            ->join('himo_small_series AS hss', function ($join) {
                $join->on('hss.id', '=', 'hssw.small_series_id')
                    ->where('hssw.delete_flg', '=', 0);
            })
            ->where('hw.delete_flg', '=', 0)
            ->whereBetween('hss.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();

    }

    function himoSmallSeriesWorks($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_small_series_works AS hssw', function ($join) {
                $join->on('hssw.himo_work_pk', '=', 'hw.himo_work_pk')
                    ->where('hssw.delete_flg', '=', 0);
            })
            ->where('hw.delete_flg', '=', 0)
            ->whereBetween('hssw.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
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
