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
use App\Model\ImportControl;
use App\Exceptions\NoContentsException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

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

    /**
     * fixed banner
     */
    const FIXED_BANNER_FILE_NAME = 'static.json';

    /*
     *name of  folder Section
     */
    const SECTION_DIR_NAME = 'section';

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
    private $importControl = [];

    private $structureTable;
    private $sectionTable;
    private $bannerTable;

    private $root;

    private $baseDir;
    private $importControlFIle;

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
        $this->importControlFIle = storage_path('app/') . DIRECTORY_SEPARATOR . self::CONTROL_FILE;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->getImportControlInfo();

        $this->infoH1('Start Json Data Import Command.');

        $this->info('Check import control file.');
        if (!file_exists($this->importControlFIle)) {
            $this->info('Create import control file.');
            File::put($this->importControlFIle, null);
        }

        $this->info('Search Target Directory....');
        $fileList = $this->createList();

        DB::transaction(function () use ($fileList) {
//         先にbase.jsonのインポートを行う
            $this->infoH1('Import base.json');
            foreach ($fileList['category']['base'] as $file) {
                $this->infoH2($file['relative']);
                if (!$this->importCheck($file)) {
                    continue;
                }
                $result = $this->importBaseJson($file);
                if (!$result) {
                    $this->infoMessage('No import');
                    continue;
                }
            }
            $this->infoH1('Import sections');
            foreach ($fileList['category']['section'] as $file) {
                $this->infoH2($file['relative']);
                if (!$this->importCheck($file)) {
                    continue;
                }
                $tsStructureIds = $this->searchTsStructureId($file['goodTypeCode'], $file['saleTypeCode'], $file['filename']);
                if (!$tsStructureIds) {
                    $this->infoMessage('No import');
                    continue;
                }
                if ($file['goodType'] == self::BANNER_DIR) {
                    // 一度関連のIDのものを全て削除
                    $this->bannerTable->whereIn('ts_structure_id', $tsStructureIds)->delete();
                    foreach ($tsStructureIds as $tsStructureId) {
                        $result = $this->importBanner($file['absolute'], $tsStructureId);
                        if (!$result) {
                            $this->infoMessage('No import');
                            continue;
                        }
                    }
                } else {
                    // 一度関連のIDのものを全て削除
                    $this->sectionTable->whereIn('ts_structure_id', $tsStructureIds)->delete();
                    foreach ($tsStructureIds as $tsStructureId) {
                        $result = $this->importSection($file['absolute'], $tsStructureId);
                        if (!$result) {
                            $this->infoMessage('No import');
                            continue;
                        }
                    }
                }
            }
            $this->infoH1('Import Fixed Banner');
            // baseインポート後にsection, bannerをインポートする
            foreach ($fileList['banner'] as $file) {
                $this->infoH2($file['absolute']);
                if (!$this->importCheck($file)) {
                    continue;
                }
                $this->importFixedBanner($file['absolute']);
            }

            $this->infoH1('Update Structure Table Data.');

            $this->updateSectionsData();
        });

        $this->commitImportControlInfo();

        $this->info('Finish!');

    }

    private
    function createList()
    {
        // 一覧の作成
        $files = File::allFiles($this->baseDir);
        $fileList['banner'] = [];
        foreach ($files as $file) {
            $timestamp = null;
            $timestamp = File::lastModified($file->getPathname());
            if ($timestamp === false) {
                $this->infoMessage('Can not get timestamp.');
            }
            $explodeFilePath = explode('/', $file->getRelativePathname());
            if ($explodeFilePath[0] === self::BANNER_DIR) {
                if ($file->getFilename() == self::FIXED_BANNER_FILE_NAME) {
                    $fileList['banner'][] = [
                        'relative' => $file->getRelativePathname(),
                        'absolute' => $file->getPathname(),
                        'filename' => $file->getFilename(),
                        'timestamp' => $timestamp
                    ];
                }
            } else if ($explodeFilePath[0] === self::CATEGORY_DIR && $file->getFilename() === self::BASE_FILE_NAME) {
                $goodTypeCode = $this->structureRepository->convertGoodsTypeToId($explodeFilePath[1]);
                $saleTypeCode = $this->structureRepository->convertSaleTypeToId($explodeFilePath[2]);
                $fileList['category']['base'][] = [
                    'relative' => $file->getRelativePathname(),
                    'absolute' => $file->getPathname(),
                    'filename' => $file->getFilename(),
                    'goodType' => $explodeFilePath[1],
                    'saleType' => $explodeFilePath[2],
                    'goodTypeCode' => $goodTypeCode,
                    'saleTypeCode' => $saleTypeCode,
                    'timestamp' => $timestamp
                ];
            } else if ($explodeFilePath[0] === self::CATEGORY_DIR && $explodeFilePath[2] !== self::SECTION_DIR_NAME) {
                $goodTypeCode = $this->structureRepository->convertGoodsTypeToId($explodeFilePath[1]);
                $saleTypeCode = $this->structureRepository->convertSaleTypeToId($explodeFilePath[2]);
                $fileList['category']['section'][] = [
                    'relative' => $file->getRelativePathname(),
                    'absolute' => $file->getPathname(),
                    'filename' => $file->getFilename(),
                    'goodType' => $explodeFilePath[1],
                    'saleType' => $explodeFilePath[2],
                    'goodTypeCode' => $goodTypeCode,
                    'saleTypeCode' => $saleTypeCode,
                    'timestamp' => $timestamp
                ];
            }
        }
        return $fileList;
    }

    private
    function searchTsStructureId($goodType, $saleType, $fileBaseName)
    {
        $fileBaseName = str_replace('.json', '', $fileBaseName);
        $structure = new Structure;
        if ($goodType === false) {
            $structureObj = $structure->conditionFindBannerWithSectionFileName($fileBaseName)->getOne();
            if (count($structureObj) == 0) {
                $this->infoMessage('Not found structure id.');
                $this->infoMessage('GoodType: ' . $goodType);
                $this->infoMessage('SaleType: ' . $saleType);
                $this->infoMessage('FileName: ' . $fileBaseName);
                return false;
            }
            return [$structureObj->id];
        } else {
            $structureObj = $structure->condtionFindFilename($goodType, $saleType, $fileBaseName)->get();
            if (count($structureObj) == 0) {
                $this->infoMessage('Not found structure id.');
                $this->infoMessage('GoodType: ' . $goodType);
                $this->infoMessage('SaleType: ' . $saleType);
                $this->infoMessage('FileName: ' . $fileBaseName);
                return false;
            }
            foreach ($structureObj as $structure) {
                if (property_exists($structure, 'id')) {
                    $tsStructureId[] = $structure->id;
                } else {
                    $this->infoMessage('Not found structure id.');
                    return false;
                }
            }
        }
        return $tsStructureId;
    }

    private
    function importCheck($file)
    {
        // 実行有無の確認
        if (!$this->checkImportDate($file['absolute'])) {
            return false;
        }
        if (!$this->checkTimestamp($file['relative'], $file['timestamp'])) {
            return false;
        }
        //　実行後はDBのタイムスタンプを更新
//        $this->importControl->upInsertByCondition($file['relative'], $file['timestamp']);
        $this->importControl[$file['relative']] = $file['timestamp'];


        $this->infoMessage('check ok.');
        return true;
    }

    private
    function checkImportDate($filePath)
    {
        //base file
        $json = json_decode($this->fileGetContentsUtf8($filePath), true);
        // 日付指定があるか確認
        if (array_key_exists('importDateTime', $json)) {
            if (date('Y/m/d H:i:s', strtotime($json['importDateTime'])) < date('Y/m/d H:i:s')) {
                return true;
            } else {
                return false;
            }
        }
        // 指定がない場合は取り込む
        return true;
    }

    private
    function checkTimestamp($fileName, $timeStamp)
    {
        $this->infoMessage("File Name:\t\t" . $fileName . "  [Timestamp:" . $timeStamp . ']');
        if (count($this->importControl) == 0 || !array_key_exists($fileName, $this->importControl)) {
            $this->infoMessage('First execution. Add to Import control table.');
            return true;
        }
        $this->infoMessage("Database filename:\t" . $fileName . "  [Timestamp:" . $this->importControl[$fileName] . ']');
        if ($timeStamp <= $this->importControl[$fileName]) {
            $this->infoMessage('File not changed.');
            return false;
        }
        return true;
    }

    private
    function getImportControlInfo()
    {
        $importControlModel = new ImportControl;
        $importControl = $importControlModel->conditionAll()->get();
        foreach ($importControl as $value) {
            $this->importControl[$value->file_name] = $value->unix_timestamp;
        }
        return true;
    }

    private
    function commitImportControlInfo()
    {
        $importControlModel = new ImportControl;
        foreach ($this->importControl as $fileName => $timestamp) {
            $importControlModel->upInsertByCondition($fileName, $timestamp);
        }
        return true;
    }


// ファイルをカテゴリ、サブカテゴリ毎から探す。
// 見つかればファイルパスを返却。
    private
    function searchSectionFile($goodType, $saleType, $fileName)
    {
        $relativePath = self::CATEGORY_DIR .
            DIRECTORY_SEPARATOR .
            $goodType .
            DIRECTORY_SEPARATOR .
            $saleType .
            DIRECTORY_SEPARATOR .
            self::SECTION_DIR_NAME .
            DIRECTORY_SEPARATOR .
            $fileName . '.json';
        $absolutePath = $this->baseDir . DIRECTORY_SEPARATOR . $relativePath;

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

    private
    function searchBannerFile($fileName)
    {
        $relativePath = self::CATEGORY_DIR .
            DIRECTORY_SEPARATOR .
            self::BANNER_DIR .
            DIRECTORY_SEPARATOR .
            $fileName . '.json';
        $absolutePath = $this->baseDir . DIRECTORY_SEPARATOR . $relativePath;
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

    private
    function updateSectionsData()
    {
        $sectionRepository = new SectionRepository;
        $section = new Section;
        $sections = $section->conditionNoUrlCode()->get(10000);
        $tws = new TWSRepository;
        foreach ($sections as $sectionRow) {
            $this->infoH2($sectionRow->id . ' : ' . $sectionRow->code);
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
                    $this->infoMessage('Skip up date: No Contents');
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
    private
    function importBaseJson($file)
    {
        $this->structureTable = app('db')->table(self::STRUCTURE_TALBE);

        $filePath = $file['absolute'];
        if (!is_file($filePath)) {
            return false;
        }
        $dataBase = null;
        //base file
        $dataBase = json_decode($this->fileGetContentsUtf8($filePath), true);

        $relativeFilename = str_replace($this->baseDir, '', $filePath);
        // 関連するセクションの検索
        $structureList = $this->structureTable->where([
            'goods_type' => $file['goodTypeCode'],
            'sale_type' => $file['saleTypeCode']
        ])->get();

        $oldId = [];
        if (count($structureList) > 0) {
            foreach ($structureList as $structure) {
                $deleteIds[] = $structure->id;
                if (($structure->section_type == 2 ||
                        $structure->section_type == 1) &&
                    !empty($structure->section_file_name)) {
                    if (array_key_exists($structure->section_file_name, $oldId)) {
                        continue;
                    }
                    $oldId[$structure->section_file_name] = $structure->id;
                }
            }
        }
        $this->infoMessage('Delete structure table data > goods_type:' . $file['goodTypeCode'] . ' sale_type:' . $file['saleTypeCode']);
        // 削除の実行
        $this->structureTable->where([
            'goods_type' => $file['goodTypeCode'],
            'sale_type' => $file['saleTypeCode']
        ])->delete();

        foreach ($dataBase['rows'] as $row) {
            if ($row['disp'] == 0) continue;
            $structureArray = [
                'goods_type' => $file['goodTypeCode'],
                'sale_type' => $file['saleTypeCode'],
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
            $this->infoMessage('Insert structure table data > goods_type:' . $file['goodTypeCode'] .
                ' sale_type:' . $file['saleTypeCode'] .
                ' title:' . $row['title']
            );
            $insertId = $this->structureTable->insertGetId($structureArray);

            // 特集セクションの取り込み
            if ($row['sectionType'] == 2 && !empty($row['sectionFileName'])) {
                $this->infoMessage('Import section: ' . $row['sectionFileName']);
                $filePath = $this->searchSectionFile($file['goodType'], $file['saleType'], $row['sectionFileName']);
                $checkResult = $this->importCheck($filePath, $filePath['timestamp']);
                if (!$checkResult) {
                    if (count($oldId) != 0 && array_key_exists($row['sectionFileName'], $oldId)) {
                        $this->infoMessage('Update section id from : ' . $oldId[$row['sectionFileName']]);
                        $this->infoMessage('Update section id to   : ' . $insertId);
                        $updateCount = $this->sectionTable->where('ts_structure_id', $oldId[$row['sectionFileName']])
                            ->update(['ts_structure_id' => $insertId]);
                        $this->infoMessage('Update count : ' . $updateCount);
                    } else {
                        $this->importSection($filePath['absolute'], $insertId);
                    }
                    continue;
                }
                $this->importSection($filePath['absolute'], $insertId);
            } else if ($row['sectionType'] == 1 && !empty($row['sectionFileName'])) {
                $this->infoMessage('Import banner: ' . $row['sectionFileName']);
                $filePath = $this->searchBannerFile($row['sectionFileName']);
                $checkResult = $this->importCheck($filePath, $filePath['timestamp']);
                if (!$checkResult) {
                    if (count($oldId) != 0 && array_key_exists($row['sectionFileName'], $oldId)) {
                        $this->ininfoMessagefo('Update section id from : ' . $oldId[$row['sectionFileName']]);
                        $this->infoMessage('Update section id to   : ' . $insertId);
                        $updateCount = $this->bannerTable->where('ts_structure_id', $oldId[$row['sectionFileName']])
                            ->update(['ts_structure_id' => $insertId]);
                        $this->infoMessage('Update count : ' . $updateCount);
                    } else {
                        $this->importBanner($filePath['absolute'], $insertId);
                    }
                    continue;
                }
                $this->importBanner($filePath['absolute'], $insertId);
            }
        }
        return true;
    }

    private
    function importSection($filePath, $tsStructureId)
    {
        if (!file_exists($filePath)) {
            return false;
        }
        $dataSection = json_decode($this->fileGetContentsUtf8($filePath), true);
        $sectionArray = [];
        // 日付表示フラグを更新する。
        if (array_key_exists('isReleaseDate', $dataSection)) {
            $structure = new Structure();
            $structure->update($tsStructureId, ['is_release_date' => $dataSection['isReleaseDate']]);
        }
        foreach ($dataSection['rows'] as $row) {
            $sectionArray[] = [
                'code' => $row['jan'],
                'image_url' => (array_key_exists('imageUrl', $row) ? $row['imageUrl'] : ""),
                'display_start_date' => $row['displayStartDate'],
                'display_end_date' => $row['displayEndDate'],
                'ts_structure_id' => $tsStructureId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
        $this->infoMessage(' Import section. tsStructureId: ' . $tsStructureId);
        return $this->sectionTable->insert($sectionArray);
    }

    private
    function importBanner($filePath, $tsStructureId)
    {
        if (!file_exists($filePath)) {
            return false;
        }
        $dataBanner = json_decode($this->fileGetContentsUtf8($filePath), true);
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
        $this->infoMessage('Import banner. tsStructureId: ' . $tsStructureId);
        return $this->bannerTable->insert($bannerArray);
    }

    private
    function importFixedBanner($filePath)
    {
        if (!file_exists($filePath)) {
            return false;
        }
        $banner = json_decode($this->fileGetContentsUtf8($filePath), true);
        $fileBaseName = $this->getBaseName($filePath);

        // 初回だけ作るため存在した場合は再作成しない。IDは変更されない。
        $fixedBannerStructureCount = $this->structureTable->where([
            'goods_type' => 0,
            'sale_type' => 0,
            'section_type' => 99,
        ])->count();
        if ($fixedBannerStructureCount == 0) {
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
        }

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
        // 削除の実行
        $this->bannerTable->where(['ts_structure_id' => $tsStructureId])->delete();

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

    private
    function getBaseName($file)
    {
        return pathinfo($file)['filename'];
    }

    private
    function infoH1($string)
    {
        $this->info('------------------------------');
        $this->info($string);
        $this->info('------------------------------');
    }

    private
    function infoH2($string)
    {
        $indent = '==> ';
        $this->info($indent . $string);
    }

    private
    function infoMessage($string)
    {
        $indent = '     > ';
        $this->info($indent . $string);
    }
}
