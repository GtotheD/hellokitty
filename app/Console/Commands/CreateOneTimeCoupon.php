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
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;
use Illuminate\Support\Facades\Validator;
use App\Model\OneTimeCoupon;

class CreateOneTimeCoupon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
//    protected $signature = 'OneTimeCoupon {periodType}';
    protected $signature = 'OneTimeCoupon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create One Time Coupon Master Data.';

    private $couponDir;
    private $storageDir;

    /**
     * himo keyword file name
     */
    const COUPON_FILE_NAME = 'coupon.csv';

    /**
     * Create a new command instance.
     **/
    public function __construct()
    {
        parent::__construct();
        $this->couponDir = env('COUPON_FOLDER_PATH') . DIRECTORY_SEPARATOR;
        $this->storageDir = storage_path('app/');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('クーポン情報取り込み処理開始 ['.date('Y/m/d H:i:s').']');

        //（前提）
        //  ・CSVファイルが所定のフォルダに配置されている
        //
        // CSV情報取り込み
        //   1. CSVファイルからデータを取得
        //   2. 1行ずつ下記の処理を行う
        //   3.   「店番」と「トクばん」で一意なデータとし、DBに合致するものがあるか判定
        //   4.  (あり)
        //         施策開始日、施策終了日を更新（UPDATE）
        //           ⇨ 企業コード、配信管理IDの変更は許容しない
        //   4.  (なし)
        //         新規登録（INSERT）
        //   5. 取り込み処理が完了したCSVファイルを移動（バックアップ）
        //（条件）
        //    ・CSVファイルが存在する
        //    ・CSVデータのバリデーション
        //
        $isError = FALSE;
        $file = $this->couponDir . self::COUPON_FILE_NAME;
        try {
            if (!file_exists($file)) {
                $this->info('処理対象ファイルなし');
            } else {
                // 全データバリデーションチェック

                $lexer = new Lexer($this->getCsvConfig());
                $interpreter = new Interpreter();

                $rows = array();
                $interpreter->addObserver(function(array $row) use (&$rows) {
                    $rows[] = $row;
                });

                $lexer->parse($file, $interpreter);

                $data = array();
                foreach($rows as $key => $cols) {

                    $rowData = array();

                    // 企業コードは固定
                    $rowData['company_id'] = '0000';

                    foreach ($cols as $k => $val) {
                        switch ($k) {
                            case 0:
                                $rowData['store_cd'] = $val;
                                break;
                            case 1:
                                $rowData['delivery_id'] = $val;
                                break;
                            case 2:
                                $rowData['tokuban'] = $val;
                                break;
                            case 3:
                                $rowData['delivery_start_date'] = date('Y-m-d H:i', strtotime($val));
                                break;
                            case 4:
                                $rowData['delivery_end_date'] = date('Y-m-d H:i', strtotime($val));
                                break;
                        }
                    }

                    // バリデーション
                    $validator = Validator::make($rowData, [
                        'company_id' => 'required|string|size:4',
                        'store_cd' => 'required|string|size:4',
                        'delivery_id' => 'size:10',
                        'tokuban' => 'required|numeric',
                        'delivery_start_date' => 'required|date',
                        'delivery_end_date' => 'required|date|after:delivery_start_date',
                    ]);

                    if ($validator->fails()) {
                        $validator->errors()->add('line', $key);
                        throw new \Exception;
                    }

                    $data[] = $rowData;
                }

                // DB登録
                $model = new OneTimeCoupon();
                DB::beginTransaction();
                $model->insertBulk($data);
                DB::commit();

                // CSVファイルのバックアップ
                // 格納ディレクトリを作成
                if (!file_exists($this->storageDir . 'coupons')) {
                    mkdir($this->storageDir . 'coupons');
                }
                // storage配下に移動
                if (!rename($file, $this->storageDir . 'coupons/' . basename($file) . '.' . date('Ymd'))) {
                    $this->warn('file move error');
                }
            }
        } catch(\Exception $e) {
            DB::rollback();
            $this->warn($e->getTraceAsString());
            $isError = TRUE;
        } finally {
            if ($isError === TRUE) {
                $this->warn('クーポン情報取り込み処理異常終了 ['.date('Y/m/d H:i:s').']');
                return false;
            }
        }

        $this->info('クーポン情報取り込み処理終了 ['.date('Y/m/d H:i:s').']');
        return true;
    }

    private function getCsvConfig()
    {
        $config = new LexerConfig();
        $config
            ->setDelimiter(",")
            ->setToCharset("UTF-8")
            ->setFromCharset("sjis-win");
        return $config;
    }
}
