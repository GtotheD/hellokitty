<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;
use App\Repositories\HimoRepository;

class CreateRecommendTagWorks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CreateRecommendTagWorks {date?} {--mode=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert CreateRecommendTagWorks Data to table ts_recommend_tag_works';

    const RECOMMEND_TAG_TABLE = 'ts_recommend_tag';
    const RECOMMEND_TAG_WORKS_TABLE = 'ts_recommend_tag_works';
    const FORMAT_DATE_REGEX = '/^[0-9]{4}(0[1-9]|1[0-2])(0[1-9]|[1-2][0-9]|3[0-1])$/';
    const FORMAT_DATE = '「yyyymmdd」';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Get list tag.');
        $mode = $this->option('mode');
        $date = $this->argument('date');
        $tagArray = [];
        if ($mode !== null && $mode == 'all') {
            $data = DB::table(self::RECOMMEND_TAG_TABLE)->get();
        } else {
            if (!isset($date)) {
                $date = Carbon::now()->format('Y-m-d');
            } elseif (preg_match(self::FORMAT_DATE_REGEX, $date)) {
                    $date = date("Y-m-d", strtotime($date));
            } else {
                $this->error(sprintf('Argument date is not %s format.', self::RECOMMEND_TAG_WORKS_TABLE));
                return false;
            }
            $data = DB::table(self::RECOMMEND_TAG_TABLE)->whereDate('updated_at', $date)->get();
        }
        foreach ($data as $item) {
            $tagArray[] = $item->tag;
        }

        $this->info('Get tag works data from Himo');
        $himo = new HimoRepository();
        $himoResult = [];
        foreach ($data as $item) {
            $himoData = $himo->crossworkForTagWorks($item->tag)->get();
            if (!empty($himoData) && $himoData['status'] !== 204) {
                $himoResult[$item->tag] = $himoData;
            } else {
                $this->error(sprintf('Cannot get data from Himo tag works 「%s」', $item->tag));
            }
        }
        if (empty($himoResult)) {
            $this->error('There is no data from Himo');
        } else {
            $this->info(sprintf('Insert data into table %s', self::RECOMMEND_TAG_WORKS_TABLE));
            $this->deleteAndInsert($himoResult);
        }
        return true;
    }

    /**
     * Delete and insert data from Himo.
     */
    public function deleteAndInsert($himoResult)
    {
        $tagArray = [];
        $itemArray = [];
        foreach ($himoResult as $tag => $dataHimo) {
            $tagArray[] = $tag;

            foreach ($dataHimo['results']['rows'] as $row) {
                $item = [];
                $item['tag'] = $tag;
                $item['work_id'] = $row['work_id'];
                $now = Carbon::now();
                $item['updated_at'] = $now;
                $item['created_at'] = $now;
                $itemArray[] = $item;
            }
        }

        DB::beginTransaction();
        try {
            $this->info('Delete existed record!');
            DB::table(self::RECOMMEND_TAG_WORKS_TABLE)->whereIn('tag', $tagArray)->delete();

            $this->info('Inserting..');
            DB::table(self::RECOMMEND_TAG_WORKS_TABLE)->insert($itemArray);

            $this->info(sprintf('Update %s.updated_at', self::RECOMMEND_TAG_TABLE));
            DB::table(self::RECOMMEND_TAG_TABLE)->whereIn('tag', $tagArray)->update(['updated_at' => Carbon::now()]);

            DB::commit();
            $this->info('Transaction end! Insert successfully!');
        } catch (Exception $e) {
            $this->error('Error while inserting recommend tag works. Error message:' . $e->getMessage() .'.Line: ' . $e->getLine());
            DB::rollback();
            $this->info('Transaction end! Rollback!');
        }
    }
}
