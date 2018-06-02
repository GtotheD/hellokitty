<?php
/**
 * Created by PhpStorm.
 * User: inoue
 * Date: 2018/03/27
 * Time: 18:06
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Repositories\ReleaseCalenderRepository;
use App\Model\HimoReleaseOrder;

class CreateReleaseCalendar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ReleaseCalendar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Release Calendar Data.';


    const HIMO_RELEASE_ORDER_TABLE = 'ts_himo_release_orders';
    const HIMO_RELEASE_ORDER_TMP_TABLE = 'ts_himo_release_orders_tmp';

    const PARAM_MONTH = ['this', 'last', 'next'];
    /**
     * Create a new command instance.
     **/
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('リリース情報テーブル更新処理開始 ['.date('Y/m/d H:i:s').']');

        $releaseCalenderRepository = new ReleaseCalenderRepository;
        // モデルに一時テーブルをセット
        $himoReleaseOrderTmp = new HimoReleaseOrder(self::HIMO_RELEASE_ORDER_TMP_TABLE);

        // ジャンル一覧取得
        $releaseGenreMap = config('release_genre_map');

        // 更新対象のモデルを一時テーブルに変更
        $releaseCalenderRepository->setHimoReleaseOrder($himoReleaseOrderTmp);

        $this->info('一時テーブルデータ削除 ['.date('Y/m/d H:i:s').']');
        DB::table(self::HIMO_RELEASE_ORDER_TMP_TABLE)->truncate();

        foreach (self::PARAM_MONTH AS $month) {
            // 一時テーブルの作成
            foreach ($releaseGenreMap AS $releaseGenreMapKey => $releaseGenreMapItem) {
                $this->info('Create Month: '.$month.'   Genre Id: '.$releaseGenreMapKey);
                $releaseCalenderRepository->setMonth($month);
                $releaseCalenderRepository->setGenreId($releaseGenreMapKey);
                $releaseCalenderRepository->get(true);
            }
        }
        $this->info('一時テーブル作成完了 ['.date('Y/m/d H:i:s').']');

        $this->info('テーブルの再作成 ['.date('Y/m/d H:i:s').']');
        // テーブルを削除
        DB::table(self::HIMO_RELEASE_ORDER_TABLE)->truncate();

        $this->info('再作成したテーブルへインサート実行 ['.date('Y/m/d H:i:s').']');
        // tmpテーブルからインサートかける
        $replaceViewQuery = sprintf("INSERT INTO %s SELECT * FROM %s",
            self::HIMO_RELEASE_ORDER_TABLE,
            self::HIMO_RELEASE_ORDER_TMP_TABLE);
        DB::connection()->getPdo()->exec($replaceViewQuery);
        $this->info('作成完了 ['.date('Y/m/d H:i:s').']');

        return true;
    }
}
