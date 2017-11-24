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

    /**
     * Create a new command instance.
     **/
    public function __construct()
    {
        parent::__construct();
        $this->structureRepository = new StructureRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->importStructureData();

        $this->updateSerctionsData();

    }

    private function importStructureData()
    {
        $root = env('STRUCTURE_DATA_FOLDER_PATH');

        //section folder
        $rootFolder = scandir($root);
        //remove empty file in folder
        unset($rootFolder[array_search('.', $rootFolder, true)]);
        unset($rootFolder[array_search('..', $rootFolder, true)]);


        if (count($rootFolder) > 0) {
            foreach ($rootFolder as $goodType) {
                $goodType = strtolower($goodType);

                //scan folder
                $subDirectory = glob($root . DIRECTORY_SEPARATOR . $goodType . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
                if (count($subDirectory) > 0) {
                    //check has sub directory rental or sale
                    //case good type
                    $this->importByGoodType($goodType, $root);
                } else if($goodType == self::BANNER_TYPE) {
                    // case banner
                    $bannerFolder = $root . DIRECTORY_SEPARATOR . $goodType;
                    $this->importBannerFolder($bannerFolder);
                }

            }
        }

    }

    private function updateSerctionsData() {
        $sectionRepository = new SectionRepository;
        $section = new Section;
        $sections = $section->conditionNoUrlCode()->get(10000);
        $tws = new TWSRepository;
        foreach ($sections as $sectionRow) {
            $this->info($sectionRow->id);
            if (!empty($sectionRow->code)) {
                $res = $tws->detail($sectionRow->code)->get()['entry'];
                $updateValues = [
                    'title' => $res['productName'],
                    'image_url' => $res['image']['large'],
                    'url_code' => $res['urlCd'],
                    'sale_start_date' => $res['saleDate'],
                ];
                if (array_key_exists('artistInfo', $res)) {
                    $updateValues['supplement'] = $sectionRepository->getOneArtist($res['artistInfo'])['artistName'];
                } else if (array_key_exists('modelName', $res)) {
                    $updateValues['supplement'] = $res['modelName'];

                }
                $section->update($sectionRow->id, $updateValues);
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

        $goodTypeFolder = scandir($goodTypePath);
        //remove dump file . and .. in folder
        unset($goodTypeFolder[array_search('.', $goodTypeFolder, true)]);
        unset($goodTypeFolder[array_search('..', $goodTypeFolder, true)]);

        foreach ($goodTypeFolder as $saleType) {
            $saleType = strtolower($saleType);

            if (
                !$this->structureRepository->convertSaleTypeToId($saleType)
                || !is_dir($goodTypePath . DIRECTORY_SEPARATOR . $saleType)
            ) {
                continue;
            }

            $baseFile = $goodTypePath . DIRECTORY_SEPARATOR . $saleType . DIRECTORY_SEPARATOR . self::BASE_FILE_NAME;
            $sectionFolder = $goodTypePath . DIRECTORY_SEPARATOR . $saleType . DIRECTORY_SEPARATOR . self::SECTION_FOLDER_NAME;

            $this->importBaseJSON($goodType, $saleType, $baseFile);

            $this->importSectionFolder($sectionFolder);
        }
    }

    /**
     *  import base.json to table structure
     * @param $goodType
     * @param $saleType
     * @param $filePath
     */
    private function importBaseJSON($goodType, $saleType, $filePath)
    {

        if (is_file($filePath)) {

            //base file
            $dataBase = json_decode($this->file_get_contents_utf8($filePath), true);
            $structureArray = [];
            foreach ($dataBase['rows'] as $row) {
                $structureData = array();
                $structureData['goods_type'] = $this->structureRepository->convertGoodsTypeToId($goodType);
                $structureData['sale_type'] = $this->structureRepository->convertSaleTypeToId($saleType);

                foreach ($row as $field => $value) {
                    $fieldName = snake_case($field);
                    $structureData[$fieldName] = $value;
                }
                $structureArray[] = $structureData;
            }

            app('db')->table(self::STRUCTURE_TALBE)->insert($structureArray);
        }
    }

    /**
     * import all file data in section folder to table section
     * @param $folderPath
     */
    private function importSectionFolder($folderPath)
    {
        if (is_dir($folderPath)) {

            //section folder
            $sectionFiles = scandir($folderPath);
            //remove empty file in folder
            unset($sectionFiles[array_search('.', $sectionFiles, true)]);
            unset($sectionFiles[array_search('..', $sectionFiles, true)]);

            if (count($sectionFiles) > 0) {

                foreach ($sectionFiles as $sectionFile) {
                    $sectionFileRealPath = $folderPath . DIRECTORY_SEPARATOR . $sectionFile;
                    if (is_file($sectionFileRealPath)) {
                        $dataSection = json_decode($this->file_get_contents_utf8($sectionFileRealPath), true);
                        $sectionArray = [];
                        foreach ($dataSection as $row) {
                            $sectionData = array();
                            foreach ($row as $field => $value) {
                                $fieldName = snake_case($field);

                                $sectionData[$fieldName] = $value;
                            }
                            $sectionArray[] = $sectionData;
                        }
                        app('db')->table(self::SECTION_TABLE)->insert($sectionArray);
                    }
                }
            }
        }
    }

    private function importBannerFolder($folderPath)
    {
        if (is_dir($folderPath)) {

            //section folder
            $bannerFiles = scandir($folderPath);
            //remove empty file in folder
            unset($bannerFiles[array_search('.', $bannerFiles, true)]);
            unset($bannerFiles[array_search('..', $bannerFiles, true)]);

            if (count($bannerFiles) > 0) {

                foreach ($bannerFiles as $bannerFile) {

                    $bannerFileRealpath = $folderPath . DIRECTORY_SEPARATOR . $bannerFile;
                    if (is_file($bannerFileRealpath)) {
                        $dataBanner = json_decode($this->file_get_contents_utf8($bannerFileRealpath), true);
                        $bannerArray = [];
                        foreach ($dataBanner['rows'] as $row) {
                            $bannerArray[] = [
                                'link_url' => $row['linkUrl'],
                                'is_tap_on' => $row['isTapOn'],
                                'image_url' => $row['imageUrl'],
                                'login_type' => $row['loginType'],
                            ];
                        }
                        app('db')->table(self::BANNER_TABLE)->insert($bannerArray);
                    }
                }
            }
        }
    }
    /*
     * convert file json encode SJIS to utf-8
     */
    function file_get_contents_utf8($fn)
    {
        $content = file_get_contents($fn);
        return mb_convert_encoding($content, 'UTF-8',
            mb_detect_encoding($content, 'UTF-8, SJIS', true));
    }


}
