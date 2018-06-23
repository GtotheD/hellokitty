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
        $methods = get_class_methods($this);
        // テーブル一つづつ更新を確認
        $i = 1;
        $workModel = new Work;
        DB::table($this->himoUpdateWork::TABLE)->truncate();
        // foreach ($himoTables as $table) {
        foreach ($methods as $method) {
            if (substr($method, 0, 4) !== 'himo') {
                continue;
            }
            $this->info($i++ . ':' . $method);
            //$method = camel_case($table);
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
                $targetWorksArray = [];
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
        $this->deleteFromTables();
        return true;
    }

    function deleteFromTables()
    {
        DB::beginTransaction();
        try {
            $this->info('Delete Start');
            $personSubQuery = DB::table('ts_works AS tw')
                ->select(DB::raw('distinct(tpe.person_id)'))
                ->join('ts_products as tp', function ($join) {
                    $join->on('tp.work_id', '=', 'tw.work_id');
                })
                ->join('ts_people as tpe', function ($join) {
                    $join->on('tpe.product_unique_id', '=', 'tp.product_unique_id');
                })
                ->join('ts_himo_update_works as thuw', function ($join) {
                    $join->on('tw.work_id', '=', 'thuw.work_id');
                });
            DB::table('ts_related_people')
                ->whereRaw('people_id IN ('.$personSubQuery->toSql().')')
                ->delete();
            DB::table('ts_people_related_works')
                ->whereRaw('person_id IN ('.$personSubQuery->toSql().')')
                ->delete();

            $productsSubQuery = DB::table('ts_works AS tw')
                ->select(DB::raw('distinct(tp.product_unique_id)'))
                ->join('ts_products as tp', function ($join) {
                    $join->on('tp.work_id', '=', 'tw.work_id');
                })
                ->join('ts_himo_update_works as thuw', function ($join) {
                    $join->on('tw.work_id', '=', 'thuw.work_id');
                });

            DB::table('ts_people')
                ->whereRaw('product_unique_id IN ('.$productsSubQuery->toSql().')')
                ->delete();

            DB::table('ts_musico_url as tmu')
                ->join('ts_himo_update_works as thuw', function ($join) {
                    $join->on('tmu.work_id', '=', 'thuw.work_id');
                })->delete();

            DB::table('ts_discas_products as tdp')
                ->join('ts_himo_update_works as thuw', function ($join) {
                    $join->on('tdp.work_id', '=', 'thuw.work_id');
                })->delete();

            DB::table('ts_series as ts')
                ->join('ts_himo_update_works as thuw', function ($join) {
                    $join->on('ts.work_id', '=', 'thuw.work_id');
                })->delete();

            DB::table('ts_related_works as trw')
                ->join('ts_himo_update_works as thuw', function ($join) {
                    $join->on('trw.work_id', '=', 'thuw.work_id');
                })->delete();

            DB::table('ts_products as tp')
                ->join('ts_himo_update_works as thuw', function ($join) {
                    $join->on('tp.work_id', '=', 'thuw.work_id');
                })->delete();

            DB::table('ts_works as tw')
                ->join('ts_himo_update_works as thuw', function ($join) {
                    $join->on('tw.work_id', '=', 'thuw.work_id');
                })->delete();
            DB::commit();
            $this->info('Delete Success!');
        } catch (\Exception $exception) {
            $this->info('Delete Error...');
            DB::rollback();
        }

    }

    /*
     * 対象テーブル：himo_works
     */
    function himoWorks($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->whereBetween('hw.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    /*
     * 対象テーブル：himo_work_countries
     */
    function himoWorkCountries($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_work_countries AS hwc', function ($join) {
                $join->on('hw.himo_work_pk', '=', 'hwc.himo_work_pk');
            })
            ->whereBetween('hwc.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    /*
     * 対象テーブル：himo_work_docs
     */
    function himoWorkDocs($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_work_docs AS hwd', function ($join) {
                $join->on('hw.himo_work_pk', '=', 'hwd.himo_work_pk');
            })
            ->whereBetween('hwd.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    /*
     * 対象テーブル：himo_work_products
     */
    function himoWorkProducts($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hw.himo_work_pk', '=', 'hwp.himo_work_pk');
            })
            ->whereBetween('hwp.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    /*
     * 対象テーブル：himo_work_relations
     */
    function himoWorkRelations($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_work_relations AS hwr', function ($join) {
                $join->on('hw.himo_work_pk', '=', 'hwr.himo_work_pk');
            })
            ->whereBetween('hwr.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    /*
     * 対象テーブル：himo_work_scenes
     */
    function himoWorkScenes($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_work_scenes AS hws', function ($join) {
                $join->on('hw.himo_work_pk', '=', 'hws.himo_work_pk');
            })
            ->whereBetween('hws.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    /*
     * 対象テーブル：himo_work_types
     */
    function himoWorkTypes($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_work_types AS hwt', function ($join) {
                $join->on('hw.work_type_id', '=', 'hwt.id');
            })
            ->whereBetween('hwt.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    /*
     * 対象テーブル：himo_work_works
     */
    function himoWorkWorks($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_work_works AS hww', function ($join) {
                $join->on('hw.himo_work_id', '=', 'hww.himo_work1_pk');
            })
            ->whereBetween('hww.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    /*
     * 対象テーブル：himo_products
     */
    function himoProducts($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->whereBetween('hp.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    /*
     * 対象テーブル：himo_product_countries
     */
    function himoProductCountries($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_product_countries AS hpc', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpc.himo_product_pk');
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->whereBetween('hpc.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    /*
     * 対象テーブル：himo_product_devices
     */
    function himoProductDevices($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_product_devices AS hpd', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpd.himo_product_pk');
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->whereBetween('hpd.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    /*
     * 対象テーブル：himo_product_docs
     */
    function himoProductDocs($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_product_docs AS hpd', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpd.himo_product_pk');
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->whereBetween('hpd.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    /*
     * 対象テーブル：himo_product_genres
     */
    function himoProductGenres($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_product_genres AS hpg', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpg.himo_product_pk');
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->whereBetween('hpg.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    /*
     * 対象テーブル：himo_product_people
     */
    function himoProductPeople($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_product_people AS hpp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpp.himo_product_pk');
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->whereBetween('hpp.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    /*
     * 対象テーブル：himo_product_scenes
     */
    function himoProductScenes($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_product_scenes AS hps', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hps.himo_product_pk');
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->whereBetween('hps.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    /*
     * 対象テーブル：himo_product_tracks
     */
    function himoProductTracks($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_product_tracks AS hpt', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpt.himo_product_pk');
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->whereBetween('hpt.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }
    /*
     * 対象テーブル：himo_product_types
     */
    function himoProductTypes($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_product_types AS hpt', function ($join) {
                $join->on('hp.product_type', '=', 'hpt.product_type_name');
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->whereBetween('hpt.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoCountriesToWork($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_work_countries AS hwc', function ($join) {
                $join->on('hw.himo_work_pk', '=', 'hwc.himo_work_pk');
            })
            ->join('himo_countries AS hc', function ($join) {
                $join->on('hwc.country_id', '=', 'hc.id');
            })
            ->whereBetween('hc.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoCountriesToProduct($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_product_countries AS hpc', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpc.himo_product_pk');
            })
            ->join('himo_countries AS hc', function ($join) {
                $join->on('hpc.country_id', '=', 'hc.id');
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
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
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_product_devices AS hpd', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpd.himo_product_pk');
            })
            ->join('himo_devices AS hd', function ($join) {
                $join->on('hpd.device_id', '=', 'hd.id');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
            })
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
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_product_docs AS hpd', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpd.himo_product_pk');
            })
            ->join('himo_docs AS hd', function ($join) {
                $join->on('hd.id', '=', 'hpd.himo_doc_pk');
            })
            ->join('himo_doc_sources AS hds', function ($join) {
                $join->on('hds.id', '=', 'hd.doc_source_id');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
            })
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
                $join->on('hw.himo_work_pk', '=', 'hwd.himo_work_pk');
            })
            ->join('himo_docs AS hd', function ($join) {
                $join->on('hd.id', '=', 'hwd.himo_doc_pk');
            })
            ->join('himo_doc_sources AS hds', function ($join) {
                $join->on('hds.id', '=', 'hd.doc_source_id');
            })
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
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_product_docs AS hpd', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpd.himo_product_pk');
            })
            ->join('himo_docs AS hd', function ($join) {
                $join->on('hd.id', '=', 'hpd.himo_doc_pk');
            })
            ->join('himo_doc_types AS hds', function ($join) {
                $join->on('hds.id', '=', 'hd.doc_type_id');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->whereBetween('hds.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoDocTypesToWork($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_work_docs AS hwd', function ($join) {
                $join->on('hw.himo_work_pk', '=', 'hwd.himo_work_pk');
            })
            ->join('himo_docs AS hd', function ($join) {
                $join->on('hd.id', '=', 'hwd.himo_doc_pk');
            })
            ->join('himo_doc_types AS hdt', function ($join) {
                $join->on('hdt.id', '=', 'hd.doc_type_id');
            })
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
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_product_docs AS hpd', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpd.himo_product_pk');
            })
            ->join('himo_docs AS hd', function ($join) {
                $join->on('hd.id', '=', 'hpd.himo_doc_pk');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
            })
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
                $join->on('hw.himo_work_pk', '=', 'hwd.himo_work_pk');
            })
            ->join('himo_docs AS hd', function ($join) {
                $join->on('hd.id', '=', 'hwd.himo_doc_pk');
            })
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
                $join->on('hp.game_model_id', '=', 'hgm.id');
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->whereBetween('hgm.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoGenres($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_product_genres AS hpg', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpg.himo_product_pk');
            })
            ->join('himo_genres AS hg', function ($join) {
                $join->on('hg.id', '=', 'hpg.genre_id');
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
            })
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
                $join->on('hp.item_cd', '=', 'hi.item_cd');
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->whereBetween('hi.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
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
                $join->on('hp.maker_cd', '=', 'hm.maker_cd');
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
            })
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
                $join->on('hp.media_category_cd', '=', 'hmc.id');
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
            })
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
                $join->on('hp.media_format_id', '=', 'hmf.id');
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
            })
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
            ->join('himo_product_people AS hpp', function ($join) {
                $join->on('hpp.himo_product_pk', '=', 'hp.himo_product_pk');
            })
            ->join('himo_people AS hpe', function ($join) {
                $join->on('hpe.himo_person_pk', '=', 'hpp.himo_person_pk');
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
            })
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
                $join->on('hw.rating_id', '=', 'hr.id');
            })
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
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_ratings AS hr', function ($join) {
                $join->on('hp.rating_id', '=', 'hr.id');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->whereBetween('hr.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoRoles($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_tracks AS ht', function ($join) {
                $join->on('ht.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->join('himo_track_people AS htp', function ($join) {
                $join->on('htp.himo_track_pk', '=', 'ht.himo_track_pk');
            })
            ->join('himo_roles AS hr', function ($join) {
                $join->on('hr.id', '=', 'htp.role_id');
            })
            ->whereBetween('hr.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoRoles2($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_product_people AS hpp', function ($join) {
                $join->on('hpp.himo_product_pk', '=', 'hp.himo_product_pk');
            })
            ->join('himo_roles AS hr', function ($join) {
                $join->on('hr.id', '=', 'hpp.role_id');
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hwp.himo_product_pk', '=', 'hp.himo_product_pk');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hw.himo_work_pk', '=', 'hwp.himo_work_pk');
            })
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
                $join->on('ht.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->join('himo_track_people AS htp', function ($join) {
                $join->on('htp.himo_track_pk', '=', 'ht.himo_track_pk');
            })
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
                $join->on('ht.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->whereBetween('ht.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoTracksToProduct($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_product_tracks AS hpt', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpt.himo_product_pk');
            })
            ->join('himo_tracks AS ht', function ($join) {
                $join->on('ht.himo_track_pk', '=', 'hpt.himo_track_pk');
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->whereBetween('hpt.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoBigSeries($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_small_series_works AS hssw', function ($join) {
                $join->on('hssw.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->join('himo_small_series AS hss', function ($join) {
                $join->on('hss.id', '=', 'hssw.small_series_id');
            })
            ->join('himo_big_series_small_series AS hbsss', function ($join) {
                $join->on('hbsss.small_series_id', '=', 'hss.id');
            })
            ->join('himo_big_series AS hbs', function ($join) {
                $join->on('hbs.id', '=', 'hbsss.big_series_id');
            })
            ->whereBetween('hbs.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoBigSeriesSmallSeries($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_small_series_works AS hssw', function ($join) {
                $join->on('hssw.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->join('himo_small_series AS hss', function ($join) {
                $join->on('hss.id', '=', 'hssw.small_series_id');
            })
            ->join('himo_big_series_small_series AS hbsss', function ($join) {
                $join->on('hbsss.small_series_id', '=', 'hss.id');
            })
            ->whereBetween('hbsss.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    // himo_small_series ↓
    // himo_small_series_works ↓
    // himo_works
    function himoSmallSeries($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_small_series_works AS hssw', function ($join) {
                $join->on('hssw.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->join('himo_small_series AS hss', function ($join) {
                $join->on('hss.id', '=', 'hssw.small_series_id');
            })
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
                $join->on('hssw.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->whereBetween('hssw.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoXmediaBigSeries($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_small_series_works AS hssw', function ($join) {
                $join->on('hssw.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->join('himo_small_series AS hss', function ($join) {
                $join->on('hss.id', '=', 'hssw.small_series_id');
            })
            ->join('himo_big_series_small_series AS hbsss', function ($join) {
                $join->on('hbsss.small_series_id', '=', 'hss.id');
            })
            ->join('himo_big_series AS hbs', function ($join) {
                $join->on('hbs.id', '=', 'hbsss.big_series_id');
            })
            ->join('himo_xmedia_big_series AS hxbs', function ($join) {
                $join->on('hxbs.big_series_id', '=', 'hbs.id');
            })
            ->whereBetween('hxbs.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoXmedias($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_works AS hw')
            ->select('hw.himo_work_id')
            ->join('himo_small_series_works AS hssw', function ($join) {
                $join->on('hssw.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->join('himo_small_series AS hss', function ($join) {
                $join->on('hss.id', '=', 'hssw.small_series_id');
            })
            ->join('himo_big_series_small_series AS hbsss', function ($join) {
                $join->on('hbsss.small_series_id', '=', 'hss.id');
            })
            ->join('himo_big_series AS hbs', function ($join) {
                $join->on('hbs.id', '=', 'hbsss.big_series_id');
            })
            ->join('himo_xmedia_big_series AS hxbs', function ($join) {
                $join->on('hxbs.big_series_id', '=', 'hbs.id');
            })
            ->join('himo_xmedias AS hx', function ($join) {
                $join->on('hx.id', '=', 'hxbs.xmedia_id');
            })
            ->whereBetween('hx.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

    function himoPersonRelations($targetWorksArray)
    {
        return DB::connection('mysql_himo')->table('himo_products AS hp')
            ->select('hw.himo_work_id')
            ->join('himo_person_relations AS hpr', function ($join) {
                $join->on('hp.himo_person_pk', '=', 'hpr.himo_person_pk_from');
            })
            ->join('himo_people AS hp', function ($join) {
                $join->on('hp.himo_person_pk', '=', 'hpp.himo_person_pk');
            })
            ->join('himo_product_people AS hpp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hpp.himo_product_pk');
            })
            ->join('himo_work_products AS hwp', function ($join) {
                $join->on('hp.himo_product_pk', '=', 'hwp.himo_product_pk');
            })
            ->join('himo_works AS hw', function ($join) {
                $join->on('hwp.himo_work_pk', '=', 'hw.himo_work_pk');
            })
            ->whereBetween('hpr.modified', [$this->lastUpdateDateStart, $this->lastUpdateDateEnd])
            ->whereIn('hw.himo_work_id', $targetWorksArray)
            ->groupBy('hw.himo_work_id')
            ->get();
    }

}
