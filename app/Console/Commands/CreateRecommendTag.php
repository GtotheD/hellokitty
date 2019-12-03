<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class CreateRecommendTag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CreateRecommendTag';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert RecommendTag Data to table ts_recommend_tag';

    private $storageDir;

    const RECOMMEND_TAG_DIR = 'recommendTag';
    const FILE_IMPORT_ABSOLUTE_DIR = '/export/home/tol/tp/data/xml/osusume1000';
    const RECOMMEND_TAG_TABLE = 'ts_recommend_tag';
    const RECOMMEND_TAG_FILE_NAME = 'work_tags2.csv';

    /**
     * Create a new command instance.
     **/
    public function __construct()
    {
        parent::__construct();
        $this->storageDir = storage_path('app/' . self::RECOMMEND_TAG_DIR);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!file_exists($this->storageDir)) {
            if (@mkdir($this->storageDir, '0777', true)) {
                $this->info('Create subfolder recommendTag in storage/app.');
            } else {
                throw new Exception('failed mkdir : ' . $this->storageDir);
            }
        }

        $absolute_path = self::FILE_IMPORT_ABSOLUTE_DIR . DIRECTORY_SEPARATOR . self::RECOMMEND_TAG_FILE_NAME;
        $storage_path = $this->storageDir . DIRECTORY_SEPARATOR . self::RECOMMEND_TAG_FILE_NAME;
        if (!file_exists($absolute_path)) {
            $this->error(sprintf('There is no file in %s', self::FILE_IMPORT_ABSOLUTE_DIR));
        } else {
            $this->info(sprintf('Get file from %s to storage.', self::FILE_IMPORT_ABSOLUTE_DIR));
            if (file_exists($storage_path)) {
                copy($storage_path, $storage_path . '.' . date('Ymd'));
            }
            rename($absolute_path, $storage_path);

            $this->info('Transaction start!');
            DB::beginTransaction();
            try {
                $file = $this->storageDir . DIRECTORY_SEPARATOR . self::RECOMMEND_TAG_FILE_NAME;
                if (file_exists($file)) {
                    setlocale(LC_ALL, 'ja_JP.sjis');
                    $fp = fopen($file, 'r');
                    if (fgetcsv($fp)) {
                        $this->deleteAndInsert($fp);
                    } else {
                        $this->error('Cannot get content of file.');
                    }
                    fclose($fp);
                }

                DB::commit();
                $this->info('Transaction end! Insert successfully!');
            } catch (Exception $e) {
                $this->error('Error while inserting recommend tag. Error message:' . $e->getMessage() .'.Line: ' . $e->getLine());
                DB::rollback();
                $this->info('Transaction end! Rollback!');
            }
        }

        return true;
    }

    /**
     * Delete and insert data from file.
     */
    public function deleteAndInsert($fp)
    {
        $tagArray = [];
        $itemArray = [];
        while ($line = fgetcsv($fp)) {
            mb_convert_variables('utf-8', 'sjis-win', $line);
            $tagArray[] = $line[0];

            $item = [];
            $item['tag'] = $line[0];
            $item['tag_title'] = $line[1];
            $item['tag_message'] = $line[2];
            $now = Carbon::now();
            $item['updated_at'] = $now;
            $item['created_at'] = $now;
            $itemArray[] = $item;
        }
        $this->info('Delete existed record!');
        DB::table(self::RECOMMEND_TAG_TABLE)->whereIn('tag', $tagArray)->delete();

        $this->info('Inserting..');
        DB::table(self::RECOMMEND_TAG_TABLE)->insert($itemArray);
    }
}
