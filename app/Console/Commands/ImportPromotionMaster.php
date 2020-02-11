<?php

namespace App\Console\Commands;

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
                $this->info('Create subfolder promotionMaster in storage/app.');
            } else {
                throw new Exception('failed mkdir : ' . $storage_path);
            }
        }
        
        $files = glob($absolute_path . DIRECTORY_SEPARATOR . '*.*');
        if (empty($files)) {
            $this->error(sprintf('There is no file in %s', $absolute_path));
            return;
        }
        $this->info(sprintf('Get file from %s to storage.', $absolute_path));
        // backup if old files exist
        $old_files = glob($storage_path . DIRECTORY_SEPARATOR . '*.*');
        if (!empty($old_files)) {
            // create backup folder
            $bk_dir = $storage_path . DIRECTORY_SEPARATOR . 'back_up' . DIRECTORY_SEPARATOR . date('Ymd') . '_' . date('His');
            if (@mkdir($bk_dir, '0777', true)) {
                $this->info(sprintf('Create backup folder %s', $bk_dir));
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
        $this->info('Transaction start!');
        DB::beginTransaction();
        try {
            foreach ($import_files as $import_file) {
                $this->upsertPromotionMaster($import_file);
            }

            DB::commit();
            $this->info('Transaction end! Upsert successfully!');
        } catch (Exception $e) {
            $this->error('Error while inserting promotion master data. Error message:' . $e->getMessage() .'.Line: ' . $e->getLine());
            DB::rollback();
            $this->info('Transaction end! Rollback!');
        }

        return true;
    }

    /**
     * Insert data from file.
     */
    public function upsertPromotionMaster($import_file)
    {
        $data = $this->formatPromotionMaster($import_file);
        $now = Carbon::now();
        foreach ($data as $db => $value) {
            if ($db == self::PROMOTION_TABLE) {
                $record = DB::table($db)->where('id', $value['id'])->first();
                if (!empty($record)) {
                    $this->info(sprintf('Delete existing record in %s table.', $db));
                    DB::table($db)->where('id', $value['id'])->delete();
                }
                $value['created_at'] = $value['updated_at'] = $now;
                DB::table($db)->insert($value);
            } elseif ($db == self::PROMOTION_WORKS_TABLE || $db == self::PROMOTION_PRIZE_TABLE) {
                $existed_ids = DB::table($db)->where('promotion_id', $value[0]['promotion_id'])->get()->pluck('id')->toArray();

                if (!empty($existed_ids)) {
                    $this->info(sprintf('Delete existing records in %s table.', $db));
                    DB::table($db)->whereIn('id', $existed_ids)->delete();
                }
                foreach ($value as &$v) {
                    $v['created_at'] = $v['updated_at'] = $now;
                }
                DB::table($db)->insert($value);
            } else {
                $this->importQesAns($value);
            }
        }
    }

    /**
     * Format data from file.
     */
    public function formatPromotionMaster($import_file)
    {
        $content = file_get_contents($import_file);
        $content = mb_convert_encoding($content, 'UTF-8', 'SHIFT-JIS');
        $content = json_decode($content, true);
        $result = [];

        // PROMOTION_TABLE
        $promotion = [];
        $promotion['id'] = $content['id'];
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
        foreach ($content['work'] as $work) {
            $promotion_work = [];
            $promotion_work['promotion_id'] = $content['id'];
            $promotion_work['jan'] = $work['jan'];
            $promotion_work['work_title'] = isset($work['workTitle']) ? $work['workTitle'] : '';
            $promotion_works[] = $promotion_work;
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
        $promotion_qna = [];
        foreach ($content['questionnaire'] as $qna) {
            $promotion_qes = [];
            $promotion_qes['promotion_id'] = $content['id'];
            $promotion_qes['sort'] = $qna['sort'];
            $promotion_qes['format'] = $qna['format'];
            $promotion_qes['text'] = $qna['text'];
            $promotion_anses = [];
            foreach ($qna['answer'] as $ans) {
                $promotion_ans = [];
                $promotion_ans['sort'] = $ans['sort'];
                $promotion_ans['text'] = $ans['text'];
                $promotion_anses[] = $promotion_ans;
            }
            $promotion_qes['answer'] = $promotion_anses;
            $promotion_qna[] = $promotion_qes;
        }
        $result['qes_ans'] = $promotion_qna;

        return $result;
    }

    /**
     * import to db for qes and ans table.
     */
    public function importQesAns($data)
    {
        $now = Carbon::now();
        $existed_ids = DB::table(self::PROMOTION_QES_TABLE)->where('promotion_id', $data[0]['promotion_id'])->get()->pluck('id')->toArray();
        if (!empty($existed_ids)) {
            $this->info(sprintf('Delete existing records in %s table.', self::PROMOTION_QES_TABLE));
            DB::table(self::PROMOTION_QES_TABLE)->whereIn('id', $existed_ids)->delete();
            $this->info(sprintf('Delete existing records in %s table.', self::PROMOTION_ANS_TABLE));
            DB::table(self::PROMOTION_ANS_TABLE)->whereIn('qes_id', $existed_ids)->delete();
        }
        foreach ($data as $value) {
            $qes = [];
            $qes['promotion_id'] = $value['promotion_id'];
            $qes['sort'] = $value['sort'];
            $qes['format'] = $value['format'];
            $qes['text'] = $value['text'];
            $qes['created_at'] = $qes['updated_at'] = $now;
            $qes_id = DB::table(self::PROMOTION_QES_TABLE)->insertGetId($qes);

            $anses = [];
            foreach ($value['answer'] as $answer) {
                $ans = [];
                $ans['qes_id'] = $qes_id;
                $ans['sort'] = $answer['sort'];
                $ans['text'] = $answer['text'];
                $ans['created_at'] = $ans['updated_at'] = $now;
                $anses[] = $ans;
            }
            DB::table(self::PROMOTION_ANS_TABLE)->insert($anses);
        }
    }
}
