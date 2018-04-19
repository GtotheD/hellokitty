<?php
/**
 * Created by PhpStorm.
 * User: inoue
 * Date: 2018/03/27
 * Time: 18:06
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportBk2Recommend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importBk2 {--test} {--dir=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'import from bk2 selling recommend file.';

    private $bk2Dir;
    private $storageDir;

    const ACTIVE_REFERENCE_TABLE = 'ts_active_reference';
    const REFERENCE_COLUMN = 'ts_bk2_recommends';

    /**
     * himo keyword file name
     */
    const BK2_FILE_NAME = 'IF_BK_BundleSellingRecommend_';
    const BK2_RECOMMENDS_TBL = array('ts_bk2_recommends1', 'ts_bk2_recommends2');

    /**
     * Create a new command instance.
     **/
    public function __construct()
    {
        parent::__construct();
        $this->bk2Dir = env('BK2_RECOMMEND_FOLDER_PATH') . DIRECTORY_SEPARATOR;
        $this->storageDir = storage_path('app/');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Start');

        // [事前準備]
        //  insert into ts_active_reference values('ts_bk2_recommends', 1, now(), now());
        //
        // 事前にNFS(/export/home/share/bk2)をマウントして該当日の下記tar.gzファイルをコピー
        //  /export/home/share/bk2/IF_BK_BundleSellingRecommend_yyyymmdd_yyyymmddhhmmss.tar.gz

        // VIEWが参照しているテーブルを取得(ts_active_reference)
        $nonActiveTable = 1;

        $activeReference = DB::table(self::ACTIVE_REFERENCE_TABLE)->where('view', self::REFERENCE_COLUMN)->first();
        if ($activeReference->active_table === 1) {
            $nonActiveTable = 2;
        } else {
            $nonActiveTable = 1;
        }

        $isError = FALSE;
        $isImport = FALSE;

        try {
            $isFileExists = FALSE;

            // 17時から20時で毎時実行し、当日分のファイル（.fin）を確認
            $file = self::BK2_FILE_NAME . date('Ymd');
            foreach( glob($this->bk2Dir . $file . "_*.tar.gz.fin") as $val) {
                $isFileExists = TRUE;
            }

            if ($isFileExists === FALSE) return true;

            $gzFile = "";
            foreach( glob($this->bk2Dir . $file . "_*.tar.gz") as $val) {
                $gzFile = $val;
            }
            $this->info($gzFile);

            $tarDir = basename($gzFile, '.tar.gz');
            if (!file_exists($gzFile)) {
                $this->warn('file not found');
            } else {
                // storage配下にコピー
                if (!File::copy($gzFile, $this->storageDir . basename($gzFile))) {
                    $this->warn('file copy error');
                }

                // tar.gz解凍後のファイル格納ディレクトリを作成
                if (!file_exists($this->storageDir . $tarDir)) {
                    mkdir($this->storageDir . $tarDir);
                }

                // tar.gz解凍処理
                system(sprintf("tar xvfz %s -C %s", $this->storageDir . basename($gzFile), $this->storageDir . $tarDir), $ret);
                $this->info("tar xvfz ret = " . $ret);
                if ($ret !== 0) {
                    $isError = FALSE;
                    return false;
                }
            }

            if (file_exists($this->storageDir . $tarDir)) {

                // truncate 実行
                DB::table(self::BK2_RECOMMENDS_TBL[$nonActiveTable-1])->truncate();
                $this->info("truncate table " . self::BK2_RECOMMENDS_TBL[$nonActiveTable-1]);

                foreach( glob($this->storageDir . $tarDir . '/*') as $val) {
                    // 取得したファイルから MySQL(LOAD DATA INFILE)用のファイルを作成
                    // ※VIEWが参照していないテーブルに対する登録クエリを作成
                    $query = sprintf("LOAD DATA LOCAL INFILE '%s' INTO TABLE `%s` FIELDS TERMINATED BY '\t' (@work_id, @list_work_id) SET work_id=@work_id, list_work_id=@list_work_id, created_at=now(), updated_at=now()",
                        $val,
                        self::BK2_RECOMMENDS_TBL[$nonActiveTable-1]);

                    // LOAD 実行（6秒弱）・・・truncate(indexがdropされていない)場合はもう少し時間がかかる
                    $this->info($query);
                    $count = DB::connection()->getPdo()->exec($query);
                    $this->info("count[".$count."] ". $query);

                    // 一件以上インポートされたらフラグを書き換える
                    if ($count > 0) $isImport = TRUE;
                }
                // index（truncateの場合はINDEXは残ったまま）
            }
        } catch(\Exception $e) {
            $this->warn($e->getTraceAsString());
            $isError = TRUE;
        } finally {
            if ($isError === FALSE && $isImport === TRUE) {
                $replaceViewQuery = sprintf("CREATE OR REPLACE VIEW ts_bk2_recommends AS SELECT * FROM %s",
                    self::BK2_RECOMMENDS_TBL[$nonActiveTable-1]);
                DB::connection()->getPdo()->exec($replaceViewQuery);
                $this->info($replaceViewQuery);

                // ts_active_referenceを更新
                DB::table(self::ACTIVE_REFERENCE_TABLE)
                    ->where('view', self::REFERENCE_COLUMN)
                    ->update(['active_table' => $nonActiveTable, 'updated_at' => DB::raw('NOW()')]);
            }
        }

        // optimize・・・truncate後に実行が必要？

        $this->info('Finish');
        return true;
    }

}
