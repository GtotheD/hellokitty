<?php
/**
 * Created by PhpStorm.
 * User: inoue
 * Date: 2018/03/27
 * Time: 18:06
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\Section;
use App\Model\Structure;
use App\Exceptions\NoContentsException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class Touch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'touch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'touch json file.';

    /*
     *name of  folder Section
     */
    const SECTION_DIR_NAME = 'section';

    /*
     * category
     */
    const CATEGORY_DIR = 'category';

    /*
     * section table name
     */
    const BIG_CATEGORY_LIST = ['dvd', 'cd', 'book', 'game'];
    const SUB_CATEGORY_LIST = ['rental', 'sell'];

    /**
     *  structureRepository
     * @var StructureRepository
     */
    private $root;
    private $baseDir;

    /**
     * Create a new command instance.
     **/
    public function __construct()
    {
        parent::__construct();
        $this->root = env('STRUCTURE_DATA_FOLDER_PATH') . DIRECTORY_SEPARATOR . self::CATEGORY_DIR;
        $this->baseDir = env('STRUCTURE_DATA_FOLDER_PATH') . DIRECTORY_SEPARATOR;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('更新処理開始 ['.date('Y/m/d H:i:s').']');

        $nowTime = time();

        // DBからnoimageに該当する「ファイル名」「カテゴリ」「サブカテゴリ」を取得
        $sections = DB::select(DB::raw('select ts_structure_id from ts_sections where image_url LIKE "%noimage%" group by ts_structure_id'));
        foreach ($sections as $se) {
            $structures = DB::select(DB::raw('select goods_type, sale_type, section_file_name from ts_structures where id = '. $se->ts_structure_id .' order by goods_type, sale_type'));
            // ファイルパスを生成
            foreach ($structures as $st) {
                $file = $this->searchSectionFile($st->goods_type, $st->sale_type, $st->section_file_name);
                if ($file) {
                    // touchコマンド実行
                    if (!touch($file['absolute'], $nowTime)) {
                        $this->info("更新に失敗[" . $file['absolute'] . "]");
                    } else {
                        $this->info("更新に成功[" . $file['absolute'] . "]");
                    }
                }
            }
        }

        $this->info("更新処理終了");

        return true;
    }

// ファイルをカテゴリ、サブカテゴリ毎から探す。
// 見つかればファイルパスを返却。
    private
    function searchSectionFile($goodType, $saleType, $fileName)
    {
        $relativePath = self::CATEGORY_DIR .
            DIRECTORY_SEPARATOR .
            self::BIG_CATEGORY_LIST[$goodType-1] .
            DIRECTORY_SEPARATOR .
            self::SUB_CATEGORY_LIST[$saleType-1] .
            DIRECTORY_SEPARATOR .
            self::SECTION_DIR_NAME .
            DIRECTORY_SEPARATOR .
            $fileName . '.json';
        $absolutePath = $this->baseDir . $relativePath;

        if (!is_file($absolutePath)) {
            return false;
        }
        $timestamp = File::lastModified($absolutePath);

        return [
            'relative' => $relativePath,
            'absolute' => $absolutePath,
            'timestamp' => $timestamp
        ];
    }
}
