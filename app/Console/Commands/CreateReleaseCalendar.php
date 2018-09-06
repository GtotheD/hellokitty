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
use Illuminate\Support\Carbon;
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
    const HIMO_PRODUCT_TABLE = 'ts_products';

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

        //（前提）
        //    ・対象月から未来1年を取得対象とする(ReleaseCalenderRepository)
        //
        // tmpテーブルに対して更新
        //   1. tmpからリストを持ってくる(month, tap_genre_id, work_id)
        //   2. work_id に紐付くts_productsを確認（条件は下記）
        //   3. 一致しない work_id は削除する
        //
        //（条件）
        //    ・monthの範囲内
        //    ・販売種別が一致（販売種別の取得方法は「ReleaseCalenderRepository.genreMappingにtap_genre_idを渡して販売種別を特定」）
        //    ・アイテムコード（VHSを対象外とする）
        $hrotObj = DB::table(self::HIMO_RELEASE_ORDER_TMP_TABLE)->orderBy('id')->get();
        foreach($hrotObj as $columns) {
            $mappingData = $releaseCalenderRepository->genreMapping($columns->tap_genre_id);
            $saleType = $mappingData['productSellRentalFlg'];
            $workId = $columns->work_id;
            $monthFrom = $columns->month;
            $monthTo = Carbon::parse($monthFrom)->endOfMonth();

            $count = DB::table(self::HIMO_PRODUCT_TABLE)
                        ->where('product_type_id', $saleType)
                        ->where('work_id', $workId)
                        ->where('sale_start_date', '>=', $monthFrom)
                        ->where('sale_start_date', '<=', $monthTo)
                        // VHSの除外
                        ->whereRaw(DB::raw(' item_cd not like \'__20\' '))
                        ->count();
            if ($count === 0) {
                DB::table(self::HIMO_RELEASE_ORDER_TMP_TABLE)
                    ->where('id', $columns->id)
                    ->delete();
            }
        }

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
