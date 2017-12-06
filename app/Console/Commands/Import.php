<?php
/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/11/09
 * Time: 16:32
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\SectionRepository;
use App\Repositories\StructureRepository;
use App\Repositories\TWSRepository;
use App\Model\Section;
use App\Model\Structure;
use App\Exceptions\NoContentsException;
use Illuminate\Support\Facades\File;
use League\Csv\Reader;
use League\Csv\Writer;

class Import extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'import from json data.';

    /*
     *name of file base.json
     */
    const BASE_FILE_NAME = 'base.json';

    /*
     *name of  folder Section
     */
    const SECTION_FOLDER_NAME = 'section';

    /*
     * banner type name
     */
    const BANNER_DIR = 'banner';

    /*
     * category
     */
    const CATEGORY_DIR = 'category';

    /**
     * structure table name
     */
    const STRUCTURE_TALBE = 'ts_structures';

    /*
     * section table name
     */
    const SECTION_TABLE = 'ts_sections';

    /*
     * section table name
     */
    const BANNER_TABLE = 'ts_banners';

    const CONTROL_FILE = 'import_control';

    /**
     *  structureRepository
     * @var StructureRepository
     */
    private $structureRepository;

    private $structureTable;
    private $sectionTable;
    private $bannerTable;

    private $root;

    private $baseDir;
    private $storageDir;

    /**
     * Create a new command instance.
     **/
    public function __construct()
    {
        parent::__construct();
        $this->structureRepository = new StructureRepository;
        $this->structureTable = app('db')->table(self::STRUCTURE_TALBE);
        $this->sectionTable = app('db')->table(self::SECTION_TABLE);
        $this->bannerTable = app('db')->table(self::BANNER_TABLE);
        $this->root = env('STRUCTURE_DATA_FOLDER_PATH') . DIRECTORY_SEPARATOR . self::CATEGORY_DIR;
        $this->baseDir = env('STRUCTURE_DATA_FOLDER_PATH') . DIRECTORY_SEPARATOR;
        $this->storageDir = storage_path('app/') . DIRECTORY_SEPARATOR . self::CONTROL_FILE;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('------------------------------');
        $this->info('Start Json Data Import Command.');
        $this->info('------------------------------');

        // 一覧の作成
        $this->info('Search Target Directory....');
        $files = File::allFiles($this->baseDir);
        foreach ($files as $file) {
            $timestamp = File::lastModified($file->getPathname());
            if ($timestamp === false) {
                $this->info('Can not get timestamp.');
            }
            $explodeFilePath = explode('/', $file->getRelativePathname());
            if ($explodeFilePath[0] === self::BANNER_DIR) {
                $fileList['banner'][] = [
                    'relative' => $file->getRelativePathname(),
                    'absolute' => $file->getPathname(),
                    'timestamp' => $timestamp
                ];
            } else if ($explodeFilePath[0] === self::CATEGORY_DIR) {
                $fileList['category'][] = [
                    'relative' => $file->getRelativePathname(),
                    'absolute' => $file->getPathname(),
                    'goodTypeCode' => $this->structureRepository->convertGoodsTypeToId($explodeFilePath[1]),
                    'saleTypeCode' => $this->structureRepository->convertSaleTypeToId($explodeFilePath[2]),
                    'timestamp' => $timestamp
                ];
            }
        }
        $this->structureTable->truncate();
        $this->sectionTable->truncate();
        $this->bannerTable->truncate();

        // 先にbase.jsonのインポートを行う
        $this->info('Import All Sections base.json');
        foreach ($fileList['category'] as $filePath) {
            $explodeFilePath = explode('/', $filePath['relative']);
            if (count($explodeFilePath) === 4) {
                $this->info('  => ' . $filePath['absolute']);
                $this->importBaseJson($filePath['goodTypeCode'], $filePath['saleTypeCode'], $filePath['absolute']);
            }
        }
        // section, bannerをインポートする
        $this->info('Import Category Directory');
        foreach ($fileList['category'] as $filePath) {
            $explodeFilePath = explode('/', $filePath['relative']);
            // baseの処理
            if (count($explodeFilePath) === 5) {
                $this->info('  => ' . $filePath['absolute']);
                $return = $this->importSection($filePath['goodTypeCode'], $filePath['saleTypeCode'], $filePath['absolute']);
                if ($return) {
                    $this->info('Can not Imported This Section.');
                }
            } else if ($explodeFilePath[1] == self::BANNER_DIR) {
                $this->info('  => ' . $filePath['absolute']);
                $this->importBanner($filePath['absolute']);
            }
        }

        $this->info('Import Fixed Banner');
        // baseインポート後にsection, bannerをインポートする
        foreach ($fileList['banner'] as $filePath) {
            $this->info('  => ' . $filePath['absolute']);
            $this->importFixedBanner($filePath['absolute']);
        }
        $this->info('Update Structure Table Data.');
        $this->updateSectionsData();
        $this->info('Finish!');

    }

    private function updateSectionsData()
    {
        $sectionRepository = new SectionRepository;
        $section = new Section;
        $sections = $section->conditionNoUrlCode()->get(10000);
        $tws = new TWSRepository;
        foreach ($sections as $sectionRow) {
            $this->info('  => ' . $sectionRow->id . ' : ' . $sectionRow->code);
            if (!empty($sectionRow->code)) {
                try {
                    $res = $tws->detail($sectionRow->code)->get()['entry'];
                    $updateValues = [
                        'title' => $res['productName'],
                        'url_code' => $res['urlCd'],
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    if (array_key_exists('image', $res) && array_key_exists('large', $res['image'])) {
                        if (!empty($res['image']['large'])) {
                            $updateValues['image_url'] = $res['image']['large'];
                        }
                    }

                    if ($res['isRental'] == 1) {
                        if (array_key_exists('rentalStartDate', $res)) {
                            $updateValues['rental_start_date'] = $res['rentalStartDate'];
                        }
                    }
                    if ($res['isSell'] == 1) {
                        if (array_key_exists('saleDate', $res)) {
                            $updateValues['sale_start_date'] = $res['saleDate'];
                        }
                    }

                    if (array_key_exists('artistInfo', $res)) {
                        $updateValues['supplement'] = $sectionRepository->getOneArtist($res['artistInfo'])['artistName'];
                    } else if (array_key_exists('modelName', $res)) {
                        $updateValues['supplement'] = $res['modelName'];
                    }
                    $section->update($sectionRow->id, $updateValues);
                } catch (NoContentsException $e) {
                    $this->info('Skip up date: No Contents');
                }
            }
        }
    }

    /**
     *  import base.json to table structure
     * @param $goodType
     * @param $saleType
     * @param $filePath
     * @return bool
     */
    private function importBaseJson($goodType, $saleType, $filePath)
    {
        if (!is_file($filePath)) {
            return false;
        }
        //base file
        $dataBase = json_decode($this->fileGetContentsUtf8($filePath), true);
        $structureData = [];
        foreach ($dataBase['rows'] as $row) {

            if ($row['disp'] == 0) continue;

            $structureArray = [
                'goods_type' => $goodType,
                'sale_type' => $saleType,
                'sort' => $row['sort'],
                'section_type' => $row['sectionType'],
                'display_start_date' => $row['displayStartDate'],
                'display_end_date' => $row['displayEndDate'],
                'title' => $row['title'],
                'link_url' => $row['linkUrl'],
                'is_tap_on' => $row['isTapOn'],
                'api_url' => $row['apiUrl'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            if (array_key_exists('isRanking', $row)) {
                $structureArray['is_ranking'] = $row['isRanking'];
            }
            if (array_key_exists('sectionFileName', $row)) {
                $structureArray['section_file_name'] = $row['sectionFileName'];
            }
            if (array_key_exists('bannerWidth', $row)) {
                $structureArray['banner_width'] = $row['bannerWidth'];
            }
            if (array_key_exists('bannerHeight', $row)) {
                $structureArray['banner_height'] = $row['bannerHeight'];
            }

            $this->structureTable->insert($structureArray);
        }
    }

    private function importSection($goodType, $saleType, $filePath)
    {
        if (!file_exists($filePath)) {
            return false;
        }
        $dataSection = json_decode($this->fileGetContentsUtf8($filePath), true);
        $fileBaseName = $this->getBaseName($filePath);
        $sectionArray = [];
        foreach ($dataSection['rows'] as $row) {
            $sectionData = [];
            foreach ($row as $field => $value) {
                $structure = new Structure;

                $structureObj = $structure->condtionFindFilename($goodType, $saleType, $fileBaseName)->getOne();
                if (!is_object($structureObj)) {
                    return false;
                }
                if (property_exists($structureObj, 'id')) {
                    $tsStructureId = $structureObj->id;
                } else {
                    return false;
                }
                $sectionData = [
                    'code' => $row['jan'],
                    'image_url' => (array_key_exists('imageUrl', $row) ? $row['imageUrl'] : ""),
                    'display_start_date' => $row['displayStartDate'],
                    'display_end_date' => $row['displayEndDate'],
                    'ts_structure_id' => $tsStructureId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }
            $sectionArray[] = $sectionData;
        }
        $this->sectionTable->insert($sectionArray);
    }

    private function importBanner($filePath)
    {
        if (!file_exists($filePath)) {
            return false;
        }
        $fileBaseName = $this->getBaseName($filePath);
        $dataBanner = json_decode($this->fileGetContentsUtf8($filePath), true);
        $bannerArray = [];
        $structure = new Structure;
        $structureObj = $structure->conditionFindBannerWithSectionFileName($fileBaseName)->getOne();
        if (is_object($structureObj)) {
            if (property_exists($structureObj, 'id')) {
                $tsStructureId = $structureObj->id;
            } else {
                return false; // idが存在しない場合紐付けができないのでスキップ
            }
        } else {
            return false; // idが存在しない場合紐付けができないのでスキップ
        }
        foreach ($dataBanner['rows'] as $row) {
            $bannerArray[] = [
                'link_url' => $row['linkUrl'],
                'is_tap_on' => $row['isTapOn'],
                'image_url' => $row['imageUrl'],
                'login_type' => $row['loginType'],
                'display_start_date' => $row['displayStartDate'],
                'display_end_date' => $row['displayEndDate'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'ts_structure_id' => $tsStructureId
            ];
        }
        app('db')->table(self::BANNER_TABLE)->insert($bannerArray);
    }

    private function importFixedBanner($filePath)
    {
        if (!file_exists($filePath)) {
            return false;
        }
        $banner = json_decode($this->fileGetContentsUtf8($filePath), true);
        $fileBaseName = $this->getBaseName($filePath);
        $structureArray = [
            'goods_type' => 0,
            'sale_type' => 0,
            'sort' => 0,
            'section_type' => 99,
            'title' => $banner['bannerTitle'],
            'section_file_name' => $fileBaseName,
            'display_start_date' => $banner['displayStartDate'],
            'display_end_date' => $banner['displayEndDate'],
            'banner_width' => $banner['bannerWidth'],
            'banner_height' => $banner['bannerHeight'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->structureTable->insert($structureArray);

        $bannerArray = [];
        $structure = new Structure;
        $structureObj = $structure->conditionFindBannerWithSectionFileName($fileBaseName)->getOne();
        if (is_object($structureObj)) {
            if (property_exists($structureObj, 'id')) {
                $tsStructureId = $structureObj->id;
            } else {
                return false; // idが存在しない場合紐付けができないのでスキップ
            }
        } else {
            return false; // idが存在しない場合紐付けができないのでスキップ
        }
        foreach ($banner['rows'] as $row) {
            $bannerArray[] = [
                'link_url' => $row['linkUrl'],
                'is_tap_on' => $row['isTapOn'],
                'image_url' => $row['imageUrl'],
                'login_type' => $row['loginType'],
                'display_start_date' => $row['displayStartDate'],
                'display_end_date' => $row['displayEndDate'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'ts_structure_id' => $tsStructureId
            ];
        }
        app('db')->table(self::BANNER_TABLE)->insert($bannerArray);

    }

    /*
     * convert file json encode SJIS to utf-8
     */
    function fileGetContentsUtf8($fn)
    {
        $content = file_get_contents($fn);
        return mb_convert_encoding($content, 'UTF-8',
            mb_detect_encoding($content, 'UTF-8, SJIS', true));
    }

    private function getBaseName($file)
    {
        return pathinfo($file)['filename'];
    }
}
