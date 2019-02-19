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
use App\Repositories\WorkRepository;
use App\Repositories\TWSRepository;
use App\Repositories\HimoRepository;
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
    protected $signature = 'import {--test} {--update-only} {--dir=}';

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

    /*
     * category
     */
    const PREMIUM_DIR = 'premium';

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
    const BIG_CATEGORY_LIST = ['dvd', 'book', 'cd', 'game', 'banner', 'premium'];
    const SUB_CATEGORY_LIST = ['rental', 'sell'];

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
        $updateOnly = $this->option('update-only');
        $isTest = $this->option('test');
        $dir = $this->option('dir');
        if (isset($dir)) {
            $this->root = $dir . DIRECTORY_SEPARATOR . self::CATEGORY_DIR;
            $this->baseDir = $dir . DIRECTORY_SEPARATOR;
        }

        // updateのみ実行の場合
        if ($updateOnly === true) {
            $this->updateSectionsDataFromHimo();
            return true;
        }
        $this->getImportControlInfo();

        $this->infoH1('Start Json Data Import Command. [' . date('Y/m/d H:i:s') . ']');

        $this->info('Check import control file.');
        if (!file_exists($this->importControlFIle)) {
            $this->info('Create import control file.');
            File::put($this->importControlFIle, null);
        }
        $this->info('Search Target Directory....');
        $fileList = $this->createList();
        DB::transaction(function () use ($fileList, $isTest) {
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
                    $this->infoMessage('No import');
                    continue;
                }
                $structure = $this->searchTsStructureId($file['goodTypeCode'], $file['saleTypeCode'], $file['filename']);
                $tsStructureIds = $structure['structureIds'];
                if (!$tsStructureIds) {
                    $this->infoMessage('No import');
                    continue;
                }
                if ($file['goodType'] == self::BANNER_DIR) {
                    $this->infoMessage('Import Banner....');
                    // 一度関連のIDのものを全て削除
                    $bannerTable = DB::table(self::BANNER_TABLE);
                    $bannerTable->whereIn('ts_structure_id', $tsStructureIds)->delete();
                    foreach ($tsStructureIds as $tsStructureId) {
                        $result = $this->importBanner($file['absolute'], $tsStructureId);
                        if (!$result) {
                            $this->infoMessage('No import');
                            continue;
                        }
                    }
                } else {
                    // 一度関連のIDのものを全て削除
                    $sectionTable = DB::table(self::SECTION_TABLE);
                    $sectionTable->whereIn('ts_structure_id', $tsStructureIds)->delete();
                    foreach ($tsStructureIds as $tsStructureId) {
                        $result = $this->importSection($file['absolute'], $tsStructureId, false, $structure['sectionType']);
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
            if ($isTest === false) {
                $this->updateSectionsDataFromHimo();
            }
            $this->commitImportControlInfo();
        });

        $this->info('Finish!');
        return true;
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
                if ($explodeFilePath[1] == self::FIXED_BANNER_FILE_NAME) {
                    $fileList['banner'][] = [
                        'relative' => $file->getRelativePathname(),
                        'absolute' => $file->getPathname(),
                        'filename' => $file->getFilename(),
                        'timestamp' => $timestamp
                    ];
                }
            } else if ($explodeFilePath[0] === self::CATEGORY_DIR &&
                (array_key_exists(3, $explodeFilePath) &&
                    $explodeFilePath[3] === self::BASE_FILE_NAME)
            ) {
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
            } else if ($explodeFilePath[0] === self::CATEGORY_DIR &&
                $explodeFilePath[1] === self::PREMIUM_DIR &&
                (array_key_exists(4, $explodeFilePath) &&
                    $explodeFilePath[4] === self::BASE_FILE_NAME)
            ) {
                $goodTypeCode = $this->structureRepository->convertGoodsTypeToId($explodeFilePath[2]);
                $saleTypeCode = $this->structureRepository->convertSaleTypeToId($explodeFilePath[3]);
                $fileList['category']['base'][] = [
                    'relative' => $file->getRelativePathname(),
                    'absolute' => $file->getPathname(),
                    'filename' => $file->getFilename(),
                    'goodType' => $explodeFilePath[2],
                    'saleType' => $explodeFilePath[3],
                    'goodTypeCode' => 5,
                    'saleTypeCode' => $saleTypeCode,
                    'timestamp' => $timestamp
                ];
            } else if ($explodeFilePath[0] === self::CATEGORY_DIR) {

                if (
                    array_key_exists(1, $explodeFilePath) &&
                    array_search($explodeFilePath[1], self::BIG_CATEGORY_LIST) === false
                ) {
                    continue;
                }
                if (
                    $explodeFilePath[1] === self::BANNER_DIR &&
                    preg_match('/.*\.json$/', $explodeFilePath[2], $match) === 1
                ) {
                    $fileList['category']['section'][] = [
                        'relative' => $file->getRelativePathname(),
                        'absolute' => $file->getPathname(),
                        'filename' => $file->getFilename(),
                        'goodType' => $explodeFilePath[1],
                        'saleType' => $explodeFilePath[2],
                        'goodTypeCode' => 'banner',
                        'saleTypeCode' => null,
                        'timestamp' => $timestamp
                    ];
                } else {
                    if (
                        array_key_exists(2, $explodeFilePath) &&
                        array_search($explodeFilePath[2], self::SUB_CATEGORY_LIST) === false &&
                        $explodeFilePath[1] !== self::PREMIUM_DIR
                    ) {
                        continue;
                    }
                }
                if (array_key_exists(3, $explodeFilePath) &&
                    $explodeFilePath[3] === self::SECTION_DIR_NAME
                    && preg_match('/.*\.json$/', $explodeFilePath[4], $match) === 1
                ) {
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
                } elseif (
                    $explodeFilePath[1] === self::PREMIUM_DIR &&
                    array_key_exists(4, $explodeFilePath) &&
                    $explodeFilePath[4] === self::SECTION_DIR_NAME &&
                    preg_match('/.*\.json$/', $explodeFilePath[5], $match) === 1
                ) {
                    $goodTypeCode = $this->structureRepository->convertGoodsTypeToId($explodeFilePath[2]);
                    $saleTypeCode = $this->structureRepository->convertSaleTypeToId($explodeFilePath[3]);
                    $fileList['category']['section'][] = [
                        'relative' => $file->getRelativePathname(),
                        'absolute' => $file->getPathname(),
                        'filename' => $file->getFilename(),
                        'goodType' => $explodeFilePath[2],
                        'saleType' => $explodeFilePath[3],
                        'goodTypeCode' => 5,
                        'saleTypeCode' => $saleTypeCode,
                        'timestamp' => $timestamp
                    ];
                }
            }
        }
        return $fileList;
    }

    private
    function searchTsStructureId($goodType, $saleType, $fileBaseName)
    {
        $tsStructureId = [];
        $fileBaseName = str_replace('.json', '', $fileBaseName);
        $structure = new Structure;
        if ($goodType === false || $goodType === self::BANNER_DIR) {
            $structureObj = $structure->conditionFindBannerWithSectionFileName($fileBaseName)->getOne();
            if (empty($structureObj)) {
                $this->infoMessage('Not found structure id.');
                $this->infoMessage('GoodType: ' . $goodType);
                $this->infoMessage('SaleType: ' . $saleType);
                $this->infoMessage('FileName: ' . $fileBaseName);
                return false;
            }
            $tsStructureId[] = $structureObj->id;
            $sectionType = $structureObj->section_type;
        } else {
            $structureObj = $structure->conditionFindFilename($goodType, $saleType, $fileBaseName)->get();
            if (empty($structureObj)) {
                $this->infoMessage('Not found structure id.');
                $this->infoMessage('GoodType: ' . $goodType);
                $this->infoMessage('SaleType: ' . $saleType);
                $this->infoMessage('FileName: ' . $fileBaseName);
                return false;
            }
            $section = current($structureObj->all());
            $sectionType = $section->section_type;
            foreach ($structureObj as $structure) {
                if (property_exists($structure, 'id')) {
                    $tsStructureId[] = $structure->id;
                } else {
                    $this->infoMessage('Not found structure id.');
                    return false;
                }
            }
        }
        return [
            'sectionType' => $sectionType,
            'structureIds' => $tsStructureId
        ];
    }

    private
    function importCheck($file)
    {
        // なかった場合はfalse
        if (!file_exists($file['absolute'])) {
            $this->infoMessage('file not exists.');
            return false;
        }

        // 実行有無の確認
        if (!$this->checkImportDate($file['absolute'])) {
            $this->infoMessage('not the execution time.');
            return false;
        }
        if (!$this->checkTimestamp($file['relative'], $file['timestamp'])) {
            return false;
        }
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

    private function updateSectionsDataFromHimo()
    {
        $workRepository = new WorkRepository;
        $section = new Section;
        $structureRepository = new StructureRepository();
        // 全件を対象
        $sections = $section->conditionNoWorkIdActiveRow()->select(
            [
                't1.id',
                't1.code',
                't1.image_url',
                't2.sale_type'
            ])->getAll();
        foreach ($sections as $sectionRow) {
            $codeType = null;
            $this->infoH2($sectionRow->id . ' : ' . $sectionRow->code);
            if (!empty($sectionRow->code)) {
                try {
                    $length = strlen($sectionRow->code);
                    // Item
                    if (preg_match('/^PTA/', $sectionRow->code)) {
                        $codeType = '0102';
                        // rental_product_cd
                    } elseif ($length === 9) {
                        $codeType = '0206';
                    } elseif ($length === 13) {
                        $codeType = '0205';
                    }

                    $this->infoMessage('Id Type: ' . $codeType);
                    if (!empty($codeType)) {
                        // 作成する場合、
                        $workRepository->setSaleType($structureRepository->convertSaleTypeToString($sectionRow->sale_type));
                        $res = $workRepository->get($sectionRow->code, [], $codeType);
                        // 取得できなかった場合は無視する。
                        if (empty($res)) {
                            continue;
                        }
                        if ($codeType == '0102') {
                            $res['saleType'] = WorkRepository::SALE_TYPE_THEATER;
                        }
                        $updateValues = [
                            'work_id' => $res['workId'],
                            'title' => $res['workTitle'],
                            'url_code' => $res['urlCd'],
                            'sale_type' => $res['saleType'],
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                        // 既存イメージがある場合は更新対象としない
                        if (empty($sectionRow->image_url)) {
                            $updateValues['image_url'] = $res['jacketL'];
                        }
                        $updateValues['sale_start_date'] = $res['saleStartDate'];
                        $updateValues['supplement'] = $res['supplement'];
                        // work_idできたのに映画情報じゃなかったら入れない。
                        $section->update($sectionRow->id, $updateValues);
                    } else {
                        $this->infoMessage('The code type of this ID does not exist.');
                    }
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
        $structureTable = DB::table(self::STRUCTURE_TALBE);

        $filePath = $file['absolute'];
        if (!is_file($filePath)) {
            return false;
        }
        $dataBase = null;
        //base file
        $dataBase = json_decode($this->fileGetContentsUtf8($filePath), true);

        $relativeFilename = str_replace($this->baseDir, '', $filePath);
        // 関連するセクションの検索
        $structureList = $structureTable->where([
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
        $structureTable->where([
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
            $insertId = $structureTable->insertGetId($structureArray);

            // 特集セクションの取り込み
            // プレミアムも含む
            if (($row['sectionType'] === 2 || $row['sectionType'] === 7)
            && !empty($row['sectionFileName'])) {
                $this->infoMessage('Begin Import section by base.json [file Name]: ' . $row['sectionFileName']);
                $filePath = $this->searchSectionFile($file['goodType'], $file['saleType'], $row['sectionFileName']);
                $checkResult = $this->importCheck($filePath, $filePath['timestamp']);
                if (!$checkResult) {
                    if (count($oldId) != 0 && array_key_exists($row['sectionFileName'], $oldId)) {
                        $this->infoMessage('Update #section.ts_structure_id from : ' . $oldId[$row['sectionFileName']] . ' to: ' . $insertId);
                        $sectionTable = DB::table(self::SECTION_TABLE);
                        $updateCount = $sectionTable->where('ts_structure_id', $oldId[$row['sectionFileName']])
                            ->update(['ts_structure_id' => $insertId]);
                        $this->infoMessage('Update count : ' . $updateCount);
                        if ($updateCount < 1) {
                            $this->infoMessage('Error!! Can not update.');
                            $this->importSection($filePath['absolute'], $insertId, false, $row['sectionType']);
                        } else {
                            // リリース日表示フラグだけの更新を行う
                            $this->importSection($filePath['absolute'], $insertId, true, $row['sectionType']);
                        }
                    } else {
                        $this->importSection($filePath['absolute'], $insertId, false, $row['sectionType']);
                    }
                    continue;
                }
                $this->importSection($filePath['absolute'], $insertId, false, $row['sectionType']);
            } else if ($row['sectionType'] == 1 && !empty($row['sectionFileName'])) {
                $this->infoMessage('Import banner: ' . $row['sectionFileName']);
                $filePath = $this->searchBannerFile($row['sectionFileName']);
                $checkResult = $this->importCheck($filePath, $filePath['timestamp']);
                if (!$checkResult) {
                    if (count($oldId) != 0 && array_key_exists($row['sectionFileName'], $oldId)) {
                        $this->infoMessage('Update #banner.ts_structure_id from : ' . $oldId[$row['sectionFileName']] . ' to: ' . $insertId);
                        $bannerTable = DB::table(self::BANNER_TABLE);
                        $updateCount = $bannerTable->where('ts_structure_id', $oldId[$row['sectionFileName']])
                            ->update(['ts_structure_id' => $insertId]);
                        $this->infoMessage('Update count : ' . $updateCount);
                        if ($updateCount < 1) {
                            $this->infoMessage('Error!! Can not update.');
                            $this->importBanner($filePath['absolute'], $insertId);
                        }
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
    function importSection($filePath, $tsStructureId, $onlyUpdateIsReleaseDate = false, $sectionType = null)
    {
        $sectionTable = DB::table(self::SECTION_TABLE);
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
        if ($onlyUpdateIsReleaseDate === true) {
            $this->infoMessage(' Only Update IsReleaseDate Flag. tsStructureId: ' . $tsStructureId);
            return true;
        }
        foreach ($dataSection['rows'] as $row) {
            $sectionArrayTemp = [
                'code' => $row['jan'],
                'image_url' => (array_key_exists('imageUrl', $row) ? $row['imageUrl'] : ""),
                'display_start_date' => $row['displayStartDate'],
                'display_end_date' => $row['displayEndDate'],
                'ts_structure_id' => $tsStructureId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            // プレミアム用で増設
            if ($sectionType === Structure::SECTION_TYPE_PREMIUM_PICKLE) {
                // section typeが7のときだけプsubtitleを含める
                $sectionArrayTemp['data'] = json_encode([
                    'subtitle' => $row['subtitle'],
                    'text' => $row['text'],
                    'link_url' => $row['linkUrl'],
                    'is_tap_on' => $row['isTapOn'],
                ]);
            } else {
                $sectionArrayTemp['data'] = json_encode([
                    'text' => $row['text'],
                    'link_url' => $row['linkUrl'],
                    'is_tap_on' => $row['isTapOn'],
                ]);
            }
            $sectionArray[] = $sectionArrayTemp;
        }
        $this->infoMessage('Execute Import section. tsStructureId: ' . $tsStructureId);
        return $sectionTable->insert($sectionArray);
    }

    private
    function importBanner($filePath, $tsStructureId)
    {
        $bannerTable = DB::table(self::BANNER_TABLE);
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
        return $bannerTable->insert($bannerArray);
    }

    private
    function importFixedBanner($filePath)
    {
        $structureTable = DB::table(self::STRUCTURE_TALBE);
        $bannerTable = DB::table(self::BANNER_TABLE);

        if (!file_exists($filePath)) {
            return false;
        }
        $banner = json_decode($this->fileGetContentsUtf8($filePath), true);
        $fileBaseName = $this->getBaseName($filePath);
        // 初回だけ作るため存在した場合は再作成しない。IDは変更されない。
        $fixedBannerStructureCount = $structureTable->where([
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
            $this->infoMessage('Insert Fiexd banner.');
            $structureTable->insert($structureArray);
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
        $bannerTable->where(['ts_structure_id' => $tsStructureId])->delete();

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
        $bannerTable->insert($bannerArray);

    }

    /*
     * convert file json encode SJIS to utf-8
     */
    function fileGetContentsUtf8($fn)
    {
        if (!isset($fn)) {
            return false;
        }
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
