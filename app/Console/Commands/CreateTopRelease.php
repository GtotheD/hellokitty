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
use App\Model\TopReleaseNewest;
use App\Model\TopReleaseLastest;

class CreateTopRelease extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'TopRelease {periodType}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Top Release Calendar Data.';


    const HIMO_RELEASE_ORDER_TABLE = 'ts_himo_release_orders';

    const TOP_RELEASE_NEWEST_TABLE = 'ts_top_release_newest';
    const TOP_RELEASE_NEWEST_TMP_TABLE = 'ts_top_release_newest_tmp';
    const TOP_RELEASE_LASTEST_TABLE = 'ts_top_release_lastest';
    const TOP_RELEASE_LASTEST_TMP_TABLE = 'ts_top_release_lastest_tmp';
    const HIMO_PRODUCT_TABLE = 'ts_products';

    const PARAM_PERIOD_TYPE_NEWEST = 'newest';
    const PARAM_PERIOD_TYPE_LASTEST = 'lastest';

    /**
     * TSUTAYA イチオシ
     */
    const PARAM_GENRE_NEWEST = [1,9,17,22,28,39,51];

    /**
     * 販売DVD（邦画、洋画、アニメ・キッズ）
     * 販売CD（洋楽、アニメ）
     */
    const PARAM_GENRE_LASTEST = [11,12,13,24,25];

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
        $periodType = $this->argument('periodType');

        $this->info('トップリリース情報テーブル更新処理開始 ['.date('Y/m/d H:i:s').']');

        $releaseCalenderRepository = new ReleaseCalenderRepository;

        // newest / lastest で処理を振り分ける
        if ($periodType === self::PARAM_PERIOD_TYPE_NEWEST) {

            // tmp テーブルでデータ生成
            $topReleaseNewestTmp = new TopReleaseNewest(self::TOP_RELEASE_NEWEST_TMP_TABLE);

            $this->info('一時テーブルデータ削除 ['.date('Y/m/d H:i:s').']');
            DB::table(self::TOP_RELEASE_NEWEST_TMP_TABLE)->truncate();

            //（前提）
            //    ・ts_himo_release_orderが生成済み
            //    ※2018/6 時点ではAM4:00に生成している
            //
            // tmpテーブルにデータを生成
            //   1. ts_himoe_release_orderから対象データを取得
            //      ・TSUTAYAイチオシ
            //   2. work_id に紐付くts_productsを確認
            //
            //（条件）
            //    ・生成日以降にリリースされるタイトル
            //    ・販売種別が一致（販売種別の取得方法は「ReleaseCalenderRepository.genreMappingにtap_genre_idを渡して販売種別を特定」）
            //    ・アイテムコード（VHSを対象外とする）
            //
            $targetMonth = Carbon::today()->format('Y-m-01');
            foreach (self::PARAM_GENRE_NEWEST as $tapGenreId) {
                $hrotObj = DB::table(self::HIMO_RELEASE_ORDER_TABLE)
                    ->where('month', '>=', $targetMonth)
                    ->where('tap_genre_id', $tapGenreId)
                    ->orderBy('id')
                    ->get();

                $orderNum = 1;
                $releaseTopNewestData = [];
                foreach($hrotObj as $columns) {
                    $mappingData = $releaseCalenderRepository->genreMapping($tapGenreId);
                    $saleType = $mappingData['productSellRentalFlg'];
                    $workId = $columns->work_id;
                    $month = $columns->month;

                    if ($month == Carbon::today()->format('Y-m-01')) {
                        $monthFrom = Carbon::today();
                        $monthTo = Carbon::today()->endOfMonth();
                    } else {
                        $monthFrom = Carbon::parse('next month')->startOfMonth();
                        $monthTo = Carbon::parse('next month')->endOfMonth();
                    }
    
                    $count = DB::table(self::HIMO_PRODUCT_TABLE)
                        ->where('product_type_id', $saleType)
                        ->where('work_id', $workId)
                        ->where('sale_start_date', '>=', $monthFrom)
                        ->where('sale_start_date', '<=', $monthTo)
                        ->whereRaw(DB::raw(' item_cd not like \'__20\' '))
                        ->count();

                    if ($count > 0) {
                        $releaseTopNewestData[] = [
                            'work_id' => $workId,
                            'month' => $month,
                            'tap_genre_id' => $tapGenreId,
                            'sort' => $orderNum,
                        ];
                        $orderNum++;
                    }
                }
                $topReleaseNewestTmp->insertBulk($releaseTopNewestData);
            }
            $this->info('一時テーブル作成完了 ['.date('Y/m/d H:i:s').']');

            // テーブルを削除
            DB::table(self::TOP_RELEASE_NEWEST_TABLE)->truncate();
            $this->info('テーブルのデータ削除 ['.date('Y/m/d H:i:s').']');

            // tmpテーブルからインサートかける
            $replaceViewQuery = sprintf("INSERT INTO %s SELECT * FROM %s",
                self::TOP_RELEASE_NEWEST_TABLE,
                self::TOP_RELEASE_NEWEST_TMP_TABLE);
            DB::connection()->getPdo()->exec($replaceViewQuery);
            $this->info('再作成したテーブルへインサート実行 ['.date('Y/m/d H:i:s').']');

        } else if ($periodType === self::PARAM_PERIOD_TYPE_LASTEST) {

            // tmp テーブルでデータ生成
            $topReleaseLastestTmp = new TopReleaseLastest(self::TOP_RELEASE_LASTEST_TMP_TABLE);

            $this->info('一時テーブルデータ削除 ['.date('Y/m/d H:i:s').']');
            DB::table(self::TOP_RELEASE_LASTEST_TMP_TABLE)->truncate();

            //（前提）
            //    ・ts_himo_release_orderが生成済み
            //    ※2018/6 時点ではAM4:00に生成している
            //
            // tmpテーブルにデータを生成
            //   1. ts_himoe_release_orderから対象データを取得
            //   2. work_id に紐付くts_productsを確認
            //
            //（条件）
            //    ・今日までにリリースされているタイトル
            //    ・販売種別が一致（販売種別の取得方法は「ReleaseCalenderRepository.genreMappingにtap_genre_idを渡して販売種別を特定」）
            //    ・アイテムコード（VHSを対象外とする）
            //
            $targetMonth = Carbon::today()->format('Y-m-01');
            foreach (self::PARAM_GENRE_LASTEST as $tapGenreId) {
                $hrotObj = DB::table(self::HIMO_RELEASE_ORDER_TABLE)
                    ->where('month', '<=', $targetMonth)
                    ->where('tap_genre_id', $tapGenreId)
                    ->orderBy('id')
                    ->get();

                $orderNum = 1;
                $releaseTopLastestData = [];
                foreach($hrotObj as $columns) {
                    $mappingData = $releaseCalenderRepository->genreMapping($tapGenreId);
                    $saleType = $mappingData['productSellRentalFlg'];
                    $workId = $columns->work_id;
                    $month = $columns->month;
                    $monthFrom = Carbon::today();

                    $count = DB::table(self::HIMO_PRODUCT_TABLE)
                        ->where('product_type_id', $saleType)
                        ->where('work_id', $workId)
                        ->where('sale_start_date', '<=', $monthFrom)
                        ->whereRaw(DB::raw(' item_cd not like \'__20\' '))
                        ->count();

                    if ($count > 0) {
                        $releaseTopLastestData[] = [
                            'work_id' => $workId,
                            'month' => $month,
                            'tap_genre_id' => $tapGenreId,
                            'sort' => $orderNum,
                        ];
                        $orderNum++;
                    }
                }
                $topReleaseLastestTmp->insertBulk($releaseTopLastestData);
            }
            $this->info('一時テーブル作成完了 ['.date('Y/m/d H:i:s').']');

            // テーブルを削除
            DB::table(self::TOP_RELEASE_LASTEST_TABLE)->truncate();
            $this->info('テーブルのデータ削除 ['.date('Y/m/d H:i:s').']');

            // tmpテーブルからインサートかける
            $replaceViewQuery = sprintf("INSERT INTO %s SELECT * FROM %s",
                self::TOP_RELEASE_LASTEST_TABLE,
                self::TOP_RELEASE_LASTEST_TMP_TABLE);
            DB::connection()->getPdo()->exec($replaceViewQuery);
            $this->info('再作成したテーブルへインサート実行 ['.date('Y/m/d H:i:s').']');

        }

        $this->info('作成完了 ['.date('Y/m/d H:i:s').']');

        return true;
    }
}
