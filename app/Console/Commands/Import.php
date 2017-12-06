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
    const BANNER_TYPE = 'banner';

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

    /**
     *  structureRepository
     * @var StructureRepository
     */
    private $structureRepository;

    private $structureTable;
    private $sectionTable;
    private $bannerTable;

    private $root;

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
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Start Import Command.');
        $this->info('Execute Import Structure Data from Json Files.');
        $this->structureTable->truncate();
        $this->sectionTable->truncate();
        $this->bannerTable->truncate();
        $this->importStructureData();
        $bannerFolder = $this->root . DIRECTORY_SEPARATOR . self::BANNER_TYPE;
        $this->importBannerFolder($bannerFolder);

        $this->info('Import Fixed Banner');
        $this->importFiexedBanner(env('STRUCTURE_DATA_FOLDER_PATH') . DIRECTORY_SEPARATOR . 'banner');

        $this->info('Update Srctions Table Data.');
        $this->updateSerctionsData();
        $this->info('Finish!');

    }

    private function importStructureData()
    {

        //section folder
        $rootFolder = scandir($this->root);

        if (count($rootFolder) < 1) {
            return false;
        }
        foreach ($rootFolder as $goodType) {
            if (preg_match('/^\./', $goodType, $matches) > 0) {
                continue;
            }
            $goodType = strtolower($goodType);
            //scan folder
            $subDirectory = glob($this->root . DIRECTORY_SEPARATOR . $goodType . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
            if (count($subDirectory) > 0) {
                //check has sub directory rental or sale
                //case good type
                $this->importByGoodType($goodType, $this->root);
            }
        }
    }

    private function updateSerctionsData()
    {
        $sectionRepository = new SectionRepository;
        $section = new Section;
        $sections = $section->conditionNoUrlCode()->get(10000);
        $tws = new TWSRepository;
        foreach ($sections as $sectionRow) {
            $this->info($sectionRow->id . ' : ' . $sectionRow->code);
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
                }
            }
        }
    }


    /**
     * read data from file in folder By Good Type name
     * @param $goodType
     * @param $directory
     * @return bool
     */
    private function importByGoodType($goodType, $directory)
    {
        if (!$this->structureRepository->convertGoodsTypeToId($goodType)) {
            return false;
        }

        $goodTypePath = $directory . DIRECTORY_SEPARATOR . $goodType;
        $goodTypeFolder = $this->createFileList($goodTypePath);
        foreach ($goodTypeFolder as $saleType) {
            $baseFile = null;
            $sectionFolder = null;
            $saleType = strtolower($saleType);
            if (
                !$this->structureRepository->convertSaleTypeToId($saleType)
                || !is_dir($goodTypePath . DIRECTORY_SEPARATOR . $saleType)
            ) {
                continue;
            }

            $baseFile = $goodTypePath . DIRECTORY_SEPARATOR . $saleType . DIRECTORY_SEPARATOR . self::BASE_FILE_NAME;
            $sectionFolder = $goodTypePath . DIRECTORY_SEPARATOR . $saleType . DIRECTORY_SEPARATOR . self::SECTION_FOLDER_NAME;

            $goodTypeCode = $this->structureRepository->convertGoodsTypeToId($goodType);
            $saleTypeCode = $this->structureRepository->convertSaleTypeToId($saleType);
            if (empty($goodType) || empty($saleType)) {
                $this->info('unmatch Good Type or Sale Type;');
                $this->info(' Goods type:' . $goodType);
                $this->info(' Sale type:' . $saleType);
                $this->info(' Base File Name:' . $baseFile);
                return false;
            }
            $this->importBaseJSON($goodTypeCode, $saleTypeCode, $baseFile);
            $this->importSectionFolder($goodTypeCode, $saleTypeCode, $sectionFolder);
        }
    }

    /**
     *  import base.json to table structure
     * @param $goodType
     * @param $saleType
     * @param $filePath
     * @return bool
     */
    private function importBaseJSON($goodType, $saleType, $filePath)
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

    /**
     * import all file data in section folder to table section
     * @param $folderPath
     * @return bool
     */
    private function importSectionFolder($goodType, $saleType, $folderPath)
    {
        if (!is_dir($folderPath)) {
            return false;
        }
        //section folder
        $sectionFiles = $this->createFileList($folderPath);
        if (count($sectionFiles) < 1) {
            return false;
        }
        foreach ($sectionFiles as $sectionFile) {
            $sectionFileBaseName = pathinfo($sectionFile)['filename'];
            $sectionFileRealPath = $folderPath . DIRECTORY_SEPARATOR . $sectionFile;
            if (!is_file($sectionFileRealPath)) {
                return false;
            }
            $dataSection = json_decode($this->fileGetContentsUtf8($sectionFileRealPath), true);
            $sectionArray = [];
            foreach ($dataSection['rows'] as $row) {
                $sectionData = array();
                foreach ($row as $field => $value) {
                    $structure = new Structure;
                    $structureObj = $structure->condtionFindFilename($goodType, $saleType, $sectionFileBaseName)->getOne();
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
    }

    private function importBannerFolder($folderPath)
    {
        if (is_dir($folderPath)) {

            //section folder
            $bannerFiles = $this->createFileList($folderPath);

            if (count($bannerFiles) > 0) {
                foreach ($bannerFiles as $bannerFile) {
                    $bannerFileBaseName = pathinfo($bannerFile)['filename'];
                    $bannerFileRealpath = $folderPath . DIRECTORY_SEPARATOR . $bannerFile;
                    if (is_file($bannerFileRealpath)) {
                        $dataBanner = json_decode($this->fileGetContentsUtf8($bannerFileRealpath), true);
                        $bannerArray = [];
                        $structure = new Structure;
                        $structureObj = $structure->conditionFindBannerWithSectionFileName($bannerFileBaseName)->getOne();
                        if (is_object($structureObj)) {
                            if (property_exists($structureObj, 'id')) {
                                $tsStructureId = $structureObj->id;
                            } else {
                                continue; // idが存在しない場合紐付けができないのでスキップ
                            }
                        } else {
                            continue; // idが存在しない場合紐付けができないのでスキップ
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
                }
            }
        }
    }

    private function importFiexedBanner($folderPath)
    {
        if (!is_dir($folderPath)) {
            return false;
        }
        //section folder
        $files = $this->createFileList($folderPath);

        foreach ($files as $file) {
            $banner = json_decode($this->fileGetContentsUtf8($folderPath . DIRECTORY_SEPARATOR . $file), true);
            $fileBaseName = $this->getBaseName($file);
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
                    continue; // idが存在しない場合紐付けができないのでスキップ
                }
            } else {
                continue; // idが存在しない場合紐付けができないのでスキップ
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

    /*
     * 隠しファイル以外を取得する
     */
    private function createFileList($folderPath)
    {
        $response = null;
        $files = scandir($folderPath);
        foreach ($files as $file) {
            if (preg_match('/^\./', $file, $matches) > 0) {
                continue;
            }
            $response[] = $file;
        }
        return $response;
    }

    private function getBaseName($file)
    {
        return pathinfo($file)['filename'];

    }
}
