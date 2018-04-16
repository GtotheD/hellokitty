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

class ImportHimoKeyword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importKeyword {--test} {--dir=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'import from himo keyword file.';

    private $himoDir;
    private $storageDir;

    const ACTIVE_REFERENCE_TABLE = 'ts_active_reference';
    const REFERENCE_COLUMN = 'ts_himo_keywords';

    /**
     * himo keyword file name
     */
    const HIMO_FILE_NAME = array('_all_all_Keyword', '_all_all_Keyword_adult');
    const HIMO_KEYWORDS_TBL = array('ts_himo_keywords1', 'ts_himo_keywords2');

    /**
     * Create a new command instance.
     **/
    public function __construct()
    {
        parent::__construct();
        $this->himoDir = env('HIMO_KEYWORD_FOLDER_PATH') . DIRECTORY_SEPARATOR;
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
        //  insert into ts_active_reference values('ts_himo_keywords', 1, now(), now());
        //
        // HiMOからキーワードファイルを取得（通常＆アダルト）
        //  endpoint:/search/keywords
        //  endpoint:/adult/keywords
        // 事前にNFS(/export/home/share)をマウントして該当日の下記zipファイルをコピー（上記APIをコールしたくないため）
        //  /export/home/share/himo/aggregate_files/yyyymmdd_all_all_Keyword.zip
        //  /export/home/share/himo/aggregate_files/yyyymmdd_all_all_Keyword_adult.zip

        // VIEWが参照しているテーブルを取得(ts_active_reference)
        $nonActiveTable = 1;

        $activeReference = DB::table(self::ACTIVE_REFERENCE_TABLE)->where('view', self::REFERENCE_COLUMN)->first();
        if ($activeReference->active_table === 1) {
            $nonActiveTable = 2;
        } else {
            $nonActiveTable = 1;
        }

        $isTruncate = FALSE;
        $isError = FALSE;

        try {
            foreach (self::HIMO_FILE_NAME as $fn) {

                // 前日分のファイルを参照
                $file = date('Ymd', strtotime('-1 day')) . $fn;

                $zipFile = $file . '.zip';
                if (!file_exists($this->himoDir . $zipFile)) {
                    $this->warn('file not found');
                    continue;
                } else {
                    // storage配下にコピー
                    if (!File::copy($this->himoDir . $zipFile, $this->storageDir . $zipFile)) {
                        $this->warn('file copy error');
                        continue;
                    }

                    // zipを開く
                    $zip = new \ZipArchive();
                    if ($zip->open($this->storageDir . $zipFile) === TRUE) {
                        // zipを解凍
                        if ($zip->extractTo($this->storageDir) !== TRUE) {
                            $this->warn('file extract error');
                        }
                    } else {
                        $this->warn('file open error');
                    }
                    $zip->close();
                }

                $tsvFile = $file . '.tsv';
                if (file_exists($this->storageDir . $tsvFile)) {

                    // truncate 実行（初回のみ実施）
                    if ($isTruncate === FALSE) {
                        DB::table(self::HIMO_KEYWORDS_TBL[$nonActiveTable-1])->truncate();
                        $isTruncate = TRUE;
                        $this->info("truncate table " . self::HIMO_KEYWORDS_TBL[$nonActiveTable-1]);
                    }

                    // 取得したファイルから MySQL(LOAD DATA INFILE)用のファイルを作成（通常＆アダルト）
                    // ※VIEWが参照していないテーブルに対する登録クエリを作成
                    $query = sprintf("LOAD DATA LOCAL INFILE '%s' INTO TABLE `%s` FIELDS TERMINATED BY '\t' (@keyword, @weight, @roman_alphabet, @hiragana, @katakana) SET keyword=@keyword, weight=@weight, roman_alphabet=@roman_alphabet, hiragana=@hiragana, katakana=@katakana,created_at=now(),updated_at=now()",
                        $this->storageDir . $tsvFile,
                        self::HIMO_KEYWORDS_TBL[$nonActiveTable-1]);

                    // LOAD 実行（6秒弱）・・・truncate(indexがdropされていない)場合はもう少し時間がかかる
                    $this->info($query);
                    DB::connection()->getPdo()->exec($query);

                    // index（truncateの場合はINDEXは残ったまま）
                }
            }
        } catch(\Exception $e) {
            $this->warn($e->getTraceAsString());
            $isError = TRUE;
        } finally {
            if ($isError === FALSE) {
                $replaceViewQuery = sprintf("CREATE OR REPLACE VIEW ts_himo_keywords AS SELECT * FROM %s",
                    self::HIMO_KEYWORDS_TBL[$nonActiveTable-1]);
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
