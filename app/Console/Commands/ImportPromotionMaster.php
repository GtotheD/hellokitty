<?php

namespace App\Console\Commands;

use App\Repositories\WorkRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use Exception;

class ImportPromotionMaster extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ImportPromotionMaster';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert Promotion Master data to table ts_mst_promotion, ts_mst_promotion_works, ts_mst_promotion_prize, ts_mst_promotion_qes, ts_mst_promotion_ans.';

    private $storageDir;

    const PROMOTION_MASTER_DIR = 'promotionMaster';
    const FILE_IMPORT_ABSOLUTE_DIR = '/export/home/tol/tp/data/json/promotion';
    const PROMOTION_TABLE = 'ts_mst_promotion';
    const PROMOTION_WORKS_TABLE = 'ts_mst_promotion_works';
    const PROMOTION_PRIZE_TABLE = 'ts_mst_promotion_prize';
    const PROMOTION_QES_TABLE = 'ts_mst_promotion_qes';
    const PROMOTION_ANS_TABLE = 'ts_mst_promotion_ans';

    /**
     * Create a new command instance.
     **/
    public function __construct()
    {
        parent::__construct();
        $this->storageDir = storage_path('app/' . self::PROMOTION_MASTER_DIR);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $absolute_path = self::FILE_IMPORT_ABSOLUTE_DIR;
        $storage_path = $this->storageDir;

        if (!file_exists($storage_path)) {
            if (@mkdir($storage_path, '0777', true)) {
                $this->info(date("Y/m/d H:i:s") . ':Create subfolder promotionMaster in storage/app.');
            } else {
                throw new Exception('failed mkdir : ' . $storage_path);
            }
        }
        
        $files = glob($absolute_path . DIRECTORY_SEPARATOR . '*.*');
        if (empty($files)) {
            $this->error(date("Y/m/d H:i:s") . ':' .sprintf('There is no file in %s', $absolute_path));
            return;
        }
        $this->info(date("Y/m/d H:i:s") . ':' . sprintf('Get file from %s to storage.', $absolute_path));
        // backup if old files exist
        $old_files = glob($storage_path . DIRECTORY_SEPARATOR . '*.*');
        if (!empty($old_files)) {
            // create backup folder
            $bk_dir = $storage_path . DIRECTORY_SEPARATOR . 'back_up' . DIRECTORY_SEPARATOR . date('Ymd') . '_' . date('His');
            if (@mkdir($bk_dir, '0777', true)) {
                $this->info(date("Y/m/d H:i:s") . ':' . sprintf('Create backup folder %s', $bk_dir));
            } else {
                throw new Exception('failed mkdir : ' . $bk_dir);
            }
            // move old files to backup folder
            foreach ($old_files as $old_file) {
                rename($old_file, $bk_dir . DIRECTORY_SEPARATOR . basename($old_file));
            }
        }
        // move new files to storage
        foreach ($files as $file) {
            rename($file, $storage_path . DIRECTORY_SEPARATOR . basename($file));
        }

        $import_files = glob($storage_path . DIRECTORY_SEPARATOR . '*.*');
        if (empty($import_files)) {
            $this->error(sprintf('There is no file to import in %s', $storage_path));
            return;
        }
        
        $this->info(date("Y/m/d H:i:s") . ':' . 'Transaction start!');


        $datas = [];
        foreach ($import_files as $import_file) {
            $datas[] = $this->formatPromotionMaster($import_file);
        }

	if (count($datas) == 0) {
            retuen;
        }

        DB::beginTransaction();
        try {
            foreach ($datas as $data) {
                $this->upsertPromotionMaster($data);
            }

            DB::commit();
            $this->info(date("Y/m/d H:i:s") . ':' . 'Transaction end! Upsert successfully!');
        } catch (Exception $e) {
            $this->error('Error while inserting promotion master data. Error message:' . $e->getMessage() .'.Line: ' . $e->getLine());
            DB::rollback();
            $this->info(date("Y/m/d H:i:s") . ':' .'Transaction end! Rollback!');
        }

        return true;
    }

    /**
     * Insert data from file.
     */
    public function upsertPromotionMaster($data)
    {
        //$data = $this->formatPromotionMaster($import_file);
        $now = Carbon::now();
        foreach ($data as $db => $value) {
            if ($db == self::PROMOTION_TABLE) {
                DB::table($db)->where('promotion_id', $value['promotion_id'])->delete();
                $value['created_at'] = $value['updated_at'] = $now;
                DB::table($db)->insert($value);
            } else {
                DB::table($db)->where('promotion_id', $value[0]['promotion_id'])->delete();
                foreach ($value as &$v) {
                    $v['created_at'] = $v['updated_at'] = $now;
                }
                DB::table($db)->insert($value);
            }
        }
    }

    /**
     * Format data from file.
     */
    public function formatPromotionMaster($import_file)
    {
        $content = file_get_contents($import_file);
        //$content = mb_convert_encoding($content, 'UTF-8', 'SHIFT-JIS');
        $content = json_decode($content, true);
        $result = [];

        // PROMOTION_TABLE
        $promotion = [];
        $promotion['promotion_id'] = $content['id'];
        $promotion['title'] = $content['title'];
        $promotion['main_image'] = isset($content['mainImage']) ? $content['mainImage'] : '';
        $promotion['thumb_image'] = isset($content['thumbImage']) ? $content['thumbImage'] : '';
        $promotion['outline'] = isset($content['outline']) ? $content['outline'] : '';
        $promotion['target'] = isset($content['target']) ? $content['target'] : '';
        if (isset($content['promotionDates'])) {
            $promotion['promotion_start_date'] = $content['promotionDates']['startDate'];
            $promotion['promotion_end_date'] = $content['promotionDates']['endDate'];
        }
        $promotion['caution'] = isset($content['caution']) ? $content['caution'] : '';
        $promotion['supplement'] = isset($content['supplements']) ? json_encode($content['supplements']) : '';
        $promotion['image'] = isset($content['prizeImage']) ? $content['prizeImage'] : '';
        $result[self::PROMOTION_TABLE] = $promotion;

        // PROMOTION_WORKS_TABLE
        $promotion_works = [];
        $i = 1;
        $workRepository = new WorkRepository;
        foreach ($content['work'] as $work) {
            $promotion_work = [];
            $promotion_work['promotion_id'] = $content['id'];
            $promotion_work['sort'] = $i;

            $length = strlen($work['jan']);
            // Item
            if ($length === 9) {
                $codeType = '0206';
                $sale_type = 'rental';
            } elseif ($length === 13) {
                $codeType = '0205';
                $sale_type = 'sell';
            }

            if (!empty($codeType)) {
                $workRepository->setSaleType($sale_type);
                $res = $workRepository->get($work['jan'], [], $codeType);
                if (empty($res)) {
                    continue;
                }
                $workid = $res['workId'];
                $title = $res['workTitle'];
                $sale_type = $res['saleType'];
            }
            if($work['workTitle'] != '') {
                $title = $work['workTitle'];
            }
            $promotion_work['work_id'] = $workid;
            $promotion_work['work_title'] = $title;
            $promotion_work['jan'] = $work['jan'];
            $promotion_works[] = $promotion_work;
            $i++;
        }
        $result[self::PROMOTION_WORKS_TABLE] = $promotion_works;

        // PROMOTION_PRIZE_TABLE
        $promotion_prizes = [];
        foreach ($content['prize'] as $prize) {
            $promotion_prize = [];
            $promotion_prize['promotion_id'] = $content['id'];
            $promotion_prize['sort'] = $prize['sort'];
            $promotion_prize['text'] = $prize['text'];
            $promotion_prizes[] = $promotion_prize;
        }
        $result[self::PROMOTION_PRIZE_TABLE] = $promotion_prizes;

        // PROMOTION_QES_TABLE and PROMOTION_ANS_TABLE
        $promotion_qeses = $promotion_anses = [];
        foreach ($content['questionnaire'] as $qna) {
            $promotion_qes = [];
            $promotion_qes['promotion_id'] = $content['id'];
            $promotion_qes['sort'] = $qna['sort'];
            $promotion_qes['format'] = $qna['format'];
            $promotion_qes['text'] = $qna['text'];
            $promotion_qeses[] = $promotion_qes;

            foreach ($qna['answer'] as $ans) {
                $promotion_ans = [];
                $promotion_ans['promotion_id'] = $content['id'];
                $promotion_ans['sort_qes'] = $qna['sort'];
                $promotion_ans['sort'] = $ans['sort'];
                $promotion_ans['text'] = $ans['text'];
                $promotion_anses[] = $promotion_ans;
            }
        }
        $result[self::PROMOTION_QES_TABLE] = $promotion_qeses;
        $result[self::PROMOTION_ANS_TABLE] = $promotion_anses;

        return $result;
    }
}
