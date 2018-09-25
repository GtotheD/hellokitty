<?php

namespace App\Repositories;

use App\Exceptions\AgeLimitException;
use App\Model\DiscasProduct;
use App\Model\MusicoUrl;
use App\Model\People;
use App\Model\Work;
use App\Model\Product;
use App\Exceptions\NoContentsException;
use DB;

/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/13
 * Time: 15:01
 */
class WorkRepository extends BaseRepository
{
    private $work;

    const REQUEST_ITEM_TYPE_DVD = 'dvd';
    const REQUEST_ITEM_TYPE_CD = 'cd';
    const REQUEST_ITEM_TYPE_BOOK = 'book';
    const REQUEST_ITEM_TYPE_GAME = 'game';

    // 本プログラムで使っている販売タイプの文字列
    const SALE_TYPE_SELL = 'sell';
    const SALE_TYPE_RENTAL = 'rental';
    const SALE_TYPE_THEATER = 'theater';
    const SALE_TYPE_OTHER = 'other';

    // MSDBアイテム種別
    const MSDB_ITEM_VIDEO = 'video';
    const MSDB_ITEM_MUSIC = 'music';
    const MSDB_ITEM_BOOK = 'book';
    const MSDB_ITEM_GAME = 'game';

    // 1=音楽、2=映像、3=書籍、4=ゲーム、5=グッズ、6=音楽単曲、7=映画
    const WORK_TYPE_CD = '1';
    const WORK_TYPE_DVD = '2';
    const WORK_TYPE_BOOK = '3';
    const WORK_TYPE_GAME = '4';
    const WORK_TYPE_GOODS = '5';
    const WORK_TYPE_MUSIC_UNIT = '6';
    const WORK_TYPE_THEATER = '7';

    const HIMO_REQUEAST_MAX = 2000;
    const HIMO_REQUEAST_PER_ONCE = 20;

    const HIMO_ROLE_ID_MUSIC = array(
        'EXT00000000D', 'EXT0000176TD', 'EXT0000177YD', 'EXT0000000UM',
        'EXT00000005Y', 'EXT00001EX4U', 'EXT000017B5G', 'EXT00001AMVW',
        'EXT00000001N', 'EXT0000757LE', 'EXT00000498P', 'EXT00000827U',
        'EXT0000000LM', 'EXT0000757LN', 'EXT0000757LR', 'EXT0000757LT',
        'EXT0000757LX', 'EXT00000024X', 'EXT000000225', 'EXT0000757M3',
        'EXT000000381', 'EXT00000L5JI', 'EXT0000004YL', 'EXT0000001XR',
        'EXT0000757MG', 'EXT00000L6SD', 'EXT00000GQWQ', 'EXT00005GDYM',
        'EXT0000000G5', 'EXT000018SON', 'EXT000019UL0', 'EXT00001B3GN',
        'EXT00001HJNQ', 'EXT000019M1Z', 'EXT0000002JZ', 'EXT000018SOP',
        'EXT000002WCE', 'EXT00001E707', 'EXT0000196VA', 'EXT000019U64',
        'EXT00002UZYP', 'EXT0000009L0', 'EXT00001W695', 'EXT00001BASK',
        'EXT00001GWRV', 'EXT00001GE0F', 'EXT00001SWQ8', 'EXT00001G96T',
        'EXT00001R6QL', 'EXT00001EQDQ', 'EXT00001RTP9', 'EXT00001ANKG',
        'EXT0000197JE', 'EXT000021B4J', 'EXT000023248', 'EXT0000197FX',
        'EXT0000757T4', 'EXT00001G92T', 'EXT0000249EP', 'EXT00001TSTM',
        'EXT000019PJP', 'EXT00001SQSP', 'EXT0000757SL', 'EXT0000757SO',
        'EXT0000757SQ', 'EXT0000757SX'
    );

    const HIMO_ROLE_ID_BOOK = array(
        'EXT00000BWU9', 'EXT0000757Q2', 'EXT0000757OB', 'EXT0000757OE',
        'EXT000000MM1', 'EXT00001RTP9', 'EXT0000757T9', 'EXT00000RCII',
        'EXT00004OS05', 'EXT00000QLJ6', 'EXT0000757OJ', 'EXT00002ZE4D',
        'EXT000014LC2', 'EXT0000757PQ', 'EXT0000757QL', 'EXT00001GBIY',
        'EXT00000QJOS', 'EXT0000757Q4', 'EXT0000757QC', 'EXT0000757PY',
        'EXT000070LL5', 'EXT0000757QS', 'EXT0000757QT', 'EXT0000757QW',
        'EXT0000757P5', 'EXT0000757OY', 'EXT0000757P9', 'EXT0000757PD',
        'EXT00001GBQX', 'EXT0000757P2', 'EXT0000757QE', 'EXT0000757PU',
        'EXT000019VYB', 'EXT000019PJP', 'EXT00001AMVW', 'EXT0000757QG',
        'EXT0000757PH', 'EXT00001G96T', 'EXT00000DSY2', 'EXT00002HEF0',
        'EXT0000757OW', 'EXT0000197FX', 'EXT0000757RM', 'EXT000018SON',
        'EXT000019UL0', 'EXT00001B3GN', 'EXT00001HJNQ', 'EXT000019M1Z',
        'EXT0000757RY', 'EXT000018SOP', 'EXT0000757S3', 'EXT00001E707',
        'EXT0000196VA', 'EXT000019U64', 'EXT00002UZYP', 'EXT0000176TD',
        'EXT0000757SD', 'EXT0000757SE', 'EXT00001EX4U', 'EXT000017B5G',
        'EXT0000177YD', 'EXT00001W695', 'EXT00001BASK', 'EXT00001GWRV',
        'EXT00001GE0F', 'EXT00001SWQ8', 'EXT000023248', 'EXT0000757T4',
        'EXT00001TSTM', 'EXT00002RY1U', 'EXT0000757SL', 'EXT0000757SO',
        'EXT0000757SQ', 'EXT0000757SX'
    );

    const HIMO_SEARCH_VIDEO_GENRE_ID = array(
        'EXT0000000U9:', 'EXT0000000WP:', 'EXT0000000YC:', 'EXT0000000ZQ:',
        'EXT00000014Q:', 'EXT00000016A:', 'EXT00000018Q:', 'EXT0000001CL:',
        'EXT0000001DL:', 'EXT0000001DO:', 'EXT0000001N4:', 'EXT0000001NP:',
        'EXT0000001WZ:', 'EXT0000001YK:', 'EXT00000022S:', 'EXT0000002G9:',
        'EXT0000002GE:', 'EXT0000002GF:', 'EXT0000003GW:', 'EXT0000003L8:',
        'EXT0000003TL:', 'EXT0000004DW:', 'EXT0000007QI:', 'EXT000000DAT:',
        'EXT000000ECY:', 'EXT000000EVS:', 'EXT000000Q1W:', 'EXT00001T1BJ'
    );

    //
    const HIMO_SEARCH_IGNORE_ADULT_GENRE_ID =
        '-EXT000073X16:EXT0000741BA:: ' . // アダルト文庫
        '-EXT000073X18:EXT0000741CG:: ' . // アダルトノベルス1
        '-EXT0000S12QH:EXT0000S12RK:: ' . // アダルトノベルス2
        ' -EXT000073X0V:EXT000074169::' . // ムック　アダルト1
        ' -EXT0000S12QK:EXT0000S12S4:' // ムック　アダルト2
    ;

    // 1=アルバム、2=シングル、3=音楽配信（複）、4=音楽配信（単）、5=ミュージックビデオ、6=グッズ
    const WORK_FORMAT_ID_ALBUM = '1';
    const WORK_FORMAT_ID_SINGLE = '2';
    const WORK_FORMAT_ID_DELIVERY_MULTI = '3';
    const WORK_FORMAT_ID_DELIVERY_SINGLE = '4';
    const WORK_FORMAT_ID_MUSICVIDEO = '5';
    const WORK_FORMAT_ID_GOODS = '6';

    const HIMO_MEDIA_FORMAT_ID = 'EXT0000000FY';
    const MSDB_ITEM_AUDIO_SINGLE_NAME = 'シングル';

    const PRODUCT_TYPE_ID_ALBUM = '3';
    const PRODUCT_TYPE_ID_SINGLE = '4';
    const MUSICO_LINK_ALBUM = '/album/view/%s?sc_ext=tsutaya_music_musbutton';
    const MUSICO_LINK_SINGLE = '/chakuuta/detail/%s?sc_ext=tsutaya_music_musbutton';

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        parent::__construct($sort, $offset, $limit);
        $this->work = new Work();
    }

    public function getNarrowColumns($workId)
    {
        $columns = [
            'work_id',
            'work_type_id',
            'work_format_id',
            'work_title',
            'rating_id',
            'big_genre_id',
            'medium_genre_id',
            'small_genre_id',
            'url_cd',
            'ccc_work_cd',
            'jacket_l',
            'sale_start_date',
            'adult_flg',
            'msdb_item'
        ];
        return $this->get($workId, $columns);
    }

    public function get($workId, $selectColumns = null, $idType = '0102', $addSaleTypeHas = true)
    {
        $product = new Product;
        $response = [];
        $productResult = null;
        switch ($idType) {
            case '0105':
                $productResult = (array)$product->setConditionByUrlCd($workId, $this->saleType)->getOne();
                break;
            case '0205':
                $productResult = (array)$product->setConditionByJanFamilyGroup($workId)->getOne();
                break;
            case '0206':
                $productResult = (array)$product->setConditionByRentalProductCdFamilyGroup($workId)->getOne();
                break;
        }
        if ($productResult) {
            $workId = $productResult['work_id'];
        }
        $this->work->setConditionByWorkId($workId);
        if ($this->work->count() == 0) {
            $himo = new HimoRepository();
            $himoResult = $himo->crosswork([$workId], $idType)->get();
            if (empty($himoResult['results']['rows'])) {
                return null;
            }
            // インサートしたものを取得するため条件を再設定
            $workId = $himoResult['results']['rows'][0]['work_id'];
            $this->work->setConditionByWorkId($workId);
            if ($this->work->count() == 0) {
                $this->insertWorkData($himoResult, $this->work);
            }
        }

        if (empty($selectColumns)) {
            $response = (array)$this->work->toCamel(['id'])->getOne();
        } else {
            $response = (array)$this->work->selectCamel($selectColumns)->getOne();
        }
        // プロダクトベースで撮ってきた場合は、対象プロダクトの情報で付加情報をつける
        if ($productResult) {
            // productのレスポンスがキャメルケースではなく、formatAddOtherDataではキャメルケースの処理の為、変換
            foreach ($productResult as $key => $item) {
                $productResultCamel[camel_case($key)] = $item;
            }

            $response = $this->formatAddOtherData($response, $addSaleTypeHas, $productResultCamel);
            // saleStartDateをproductのものでで書き換える。
            $response['saleStartDate'] = $productResultCamel['saleStartDate'];
        } else {
            $response = $this->formatAddOtherData($response, $addSaleTypeHas);
        }
        return $response;
    }
    /**
     * Description
     * @param type|array $idsArray 
     * @return type|array $workIdsArray
     */
    public function convertUrlCdToWorkId($idsArray = []) {
        $defineWorkId = 'PTA';
        $workIdsArray = [];
        foreach ($idsArray as $idElement) {
            if(substr($idElement, 0, strlen($defineWorkId)) !== $defineWorkId) {
                // Convert urlCd to workId
                $convertData = $this->getWorkByUrlCd($idElement,['work_id']);
                if(count($convertData) > 0 && isset($convertData['workId'])) {
                    $idElement = $convertData['workId'];
                    array_push($workIdsArray, $idElement);
                }
                continue;
            }
            array_push($workIdsArray, $idElement);   
        }
        return $workIdsArray;
    }
    /**
     * Description
     * @param type $workData 
     * @param type|int $maxElement 
     * @return type|array $workData
     */
    public function formatOutputBulk($baseArray, $workData, $maxElement = 30) {
        $workDataFormat = [];
        $count = 1;
        foreach ($baseArray as $baseItem) {
            // format output workData
            foreach ($workData['rows'] as $itemWork) {
                if ($itemWork['workId'] === $baseItem ) {
                    if($count > $maxElement) break;
                    $tempData['workId'] = $itemWork['workId'];
                    $tempData['urlCd'] = $itemWork['urlCd'];
                    $tempData['cccWorkCd'] = $itemWork['cccWorkCd'];
                    $tempData['workTitle'] = $itemWork['workTitle'];
                    $tempData['newFlg'] = $itemWork['newFlg'];
                    $tempData['jacketL'] = $itemWork['jacketL'];
                    $tempData['supplement'] = $itemWork['supplement'];
                    $tempData['saleType'] = isset($itemWork['saleType']) ? $itemWork['saleType']: '';
                    $tempData['itemType'] = $itemWork['itemType'];
                    $tempData['adultFlg'] = $itemWork['adultFlg'];
                    $tempData['priceTaxOut'] = isset($itemWork['priceTaxOut']) ? $itemWork['priceTaxOut']: '';
                    $tempData['workFormatName'] = $tempData['itemType'] == 'cd' ? $itemWork['workFormatName']: '';
                    $tempData['makerName'] = isset($itemWork['makerName']) ? $itemWork['makerName']: '';
                    $tempData['saleStartDate'] = $itemWork['saleStartDate'];
                    if($tempData['itemType'] == 'book') {
                        $tempData['bookSeriesName'] = $itemWork['bookSeriesName'];
                    }
                    array_push($workDataFormat, $tempData);
                    $count ++;
                    continue;
                }
            }
        }
        return $workDataFormat;
    }

    /**
     * Get work data by input urlcd
     * @param type $workId 
     * @param type|null $selectColumns 
     * @return response
     */
    public function getWorkByUrlCd($workId, $selectColumns = null)
    {
        $product = new Product;
        $response = [];
        $productResult = null;
        $productResult = (array)$this->work->setConditionByUrlCd($workId)->getOne();
        if ($productResult) {
            $workId = $productResult['work_id'];
        }
        $this->work->setConditionByWorkId($workId);
        // if have no data on table ts_works
        // Get data from himo
        if ($this->work->count() == 0) {
            $himo = new HimoRepository();
            $himoResult = $himo->crosswork([$workId], $idType)->get();
            if (empty($himoResult['results']['rows'])) {
                return null;
            }
            // インサートしたものを取得するため条件を再設定
            $workId = $himoResult['results']['rows'][0]['work_id'];
            $this->work->setConditionByWorkId($workId);
            if ($this->work->count() == 0) {
                $this->insertWorkData($himoResult, $this->work);
            }
        }
        // If have selected column get this column
        if (empty($selectColumns)) {
            $response = (array)$this->work->toCamel(['id'])->getOne();
        } else {
            $response = (array)$this->work->selectCamel($selectColumns)->getOne();
        }
        return $response;
    }

    /**
     *
     *
     * @param $workIds, $selectColumns
     * @return null
     *
     * @throws NoContentsException
     */
    public function getWorkList($workIds, $selectColumns = null, $idType = null, $workOnly = false, $saleType = null)
    {
        $himo = new HimoRepository();
        $workIdsExistedArray = [];
        switch ($idType) {
            case '0105':
                $workIdsExisted = $this->work->setConditionByUrlCd($workIds)->select('url_cd')->getAll();
                $targetColumn = 'url_cd';
                break;
            default:
                $workIdsExisted = $this->work->getWorkBySaleType($workIds)->select('work_id')->getAll();
                $targetColumn = 'work_id';
                break;
        }
        foreach ($workIdsExisted as $workIdsExistedItem) {
                $workIdsExistedArray[] = $workIdsExistedItem->$targetColumn;
        }

        // STEP 3: IDが取得出来なかった場合は全てHimoから新規で詳細情報を取得するためのリストを作成。
        if (!$workIdsExistedArray) {
            $workIdsNew = $workIds;
        } else {
            $workIdsNew = array_values(array_diff($workIds, $workIdsExistedArray));
        }
        // STEP 4: 既存データから取ってこれなかったものをHimoから取得し格納する。
        // Get data by list workIds and return
        if ($workIdsNew) {
            $max = self::HIMO_REQUEAST_MAX;
            $limitOnceMax = self::HIMO_REQUEAST_PER_ONCE;
            $loopCount = 0;
            $limitOnce = 0;
            $mergeWorks = [];
            $himoResult = [];
            // 10件ずつ問い合わせ。アプリ上で何件だすかで制御を変更する。
            foreach ($workIdsNew as $workId) {
                $loopCount++;
                $limitOnce++;
                $getList[] = $workId;
                if ($limitOnce >= $limitOnceMax ||
                    (count($workIdsNew) - $loopCount) === 0 ||
                    $loopCount == $max
                ) {
                    if (isset($idType)) {
                        $himoResult = $himo->crosswork($getList, $idType)->get();
                    } else {
                        $himoResult = $himo->crosswork($getList)->get();
                    }
                    // Himoから取得できなかった場合はスキップする
                    if (!empty($himoResult) && $himoResult['status'] !== 204) {
                        $insertResult = $this->insertWorkData($himoResult);
                    }
                    // リセットをかける
                    $limitOnce = 0;
                    $getList = [];
                    if ($loopCount == $max) {
                        break;
                    }
                }
            }
        }

        // STEP 5: 再検索する為に条件をセット
        switch ($idType) {
            case '0105':
                $this->work->setConditionByUrlCd($workIds, $saleType);
                break;
            default:
                $this->work->getWorkBySaleType($workIds, $saleType);
                break;
        }
        $this->totalCount = $this->work->count();
        if (!$this->totalCount) {
            return null;
        }
        if (empty($selectColumns)) {
            $workArray = $this->work->toCamel(['id'])->getAll();
        } else {
            $workArray = $this->work->selectCamel($selectColumns)->getAll();
        }
        // productsからとってくるが、仮データ
        foreach ($workArray as $workItem) {
            $row = (array)$workItem;
            if ($workOnly) {
                $response['rows'][] = $row;
            } else  {
                $response['rows'][] = $this->formatAddOtherData($row);
            }
        }
        return $response;
    }

    public function formatAddOtherData($response, $addSaleTypeHas = true, $product = null, $isList = false)
    {
        // productsからとってくるが、仮データ
        $productModel = new Product();
        $productRepository = new  ProductRepository();

        $roleId = '';
        $response['supplement'] = '';
        $isAdult = null;
        $isDocSet = false;
        // プロダクトが存在しなかった場合（workベースで取得した場合等）は、各販売種別の最新商品情報で取得する。
        // プロダクトを指定のもので取得したい場合は引数にてプロダクト情報を付与すると、その情報から生成する。
        if (empty($product)) {
            // 上映映画じゃなかった場合
            if ($response['workTypeId'] !== self::WORK_TYPE_THEATER && $this->saleType === self::SALE_TYPE_THEATER) {
                return $response;
            } else if ($response['workTypeId'] === self::WORK_TYPE_THEATER) {
                $product = (array)$productModel->setConditionByWorkId($response['workId'])->toCamel()->getOne();
            } else {
                $product = (array)$productModel->setConditionByWorkIdNewestProduct($response['workId'], $this->saleType)->toCamel()->getOne();
            }
            // 上記で何も拾えなかった場合は、映像のみか確認する。
            if ($productModel->isOnlyOtherItem($response['workId'])) {
                $product = (array)$productModel->setConditionByWorkIdNewestProduct($response['workId'])->toCamel()->getOne();
                // 映像のみの作品は固定で入れる
                $response['saleType'] = self::SALE_TYPE_OTHER;
            }
        }
        if (!empty($product)) {
            // 映像の場合は、ジャケ写を最新刊のブルーレイ優先で取得する。
            if ($response['msdbItem'] === self::MSDB_ITEM_VIDEO) {
                // saleTypeの指定がない場合は関係なく出す。
                $jacket = (array)$productModel->setConditionSelectJacket($response['workId'], $this->saleType)->getOne();
                // ジャケットがある場合のみ差し替え
                if (count($jacket) > 0) {
                    $product['jacketL'] = $jacket['jacketL'];
                }
            }
            if ((substr($product['itemCd'], -2) === '75' && !empty($product['numberOfVolume'])) ||
                (substr($product['itemCd'], -2) === '76' && !empty($product['numberOfVolume']))) {
                $response['productName'] = $product['productName'] . "（{$product['numberOfVolume']}）";
            }
            // 全ての在庫ページで表示する日付を商品の最新のものにする。
            $response['saleStartDate'] = $product['saleStartDate'];
            // Add price_tax_out
            if (array_key_exists('priceTaxOut', $product)) {
                $response['priceTaxOut'] = $product['priceTaxOut'];
            }
            // add supplement
            if ($product['msdbItem'] === 'game') {
                $response['supplement'] = $product['gameModelName'];
            } else {
                $person = $this->getPerson($product['msdbItem'], $product['productUniqueId']);
                if (!empty($person)) {
                    $response['personId'] = $person->person_id;
                    $response['supplement'] = $person->person_name;
                }
            }
            // レンタルDVDの場合はsupplementを空にする
            if ($product['msdbItem'] === 'video' && $response['bigGenreId'] !== 'EXT0000003GW') {
                $response['personId'] = '';
                $response['supplement'] = '';
            }

            $response['makerCd'] = $product['makerCd'];
            if (env('DISP_RELATION_VIDEO') === true) {
                $showFlg = true;
                if ($product['msdbItem'] === 'video') {
                    $listArray = config('hidden_video_map');
                    foreach ($listArray as $makerCd => $makerName) {
                        if ($makerCd === $product['makerCd']) {
                            $showFlg = false;
                            break;
                        }
                    }
                }
                $response['videoFlg'] = $showFlg;
            } else {
                $response['videoFlg'] = false;
            }
            if (!empty($product)) {
                $response['makerName'] = $product['makerName'];
            } else {
                $response['makerName'] = '';
            }
            if ($product['msdbItem'] === 'audio') {
                if ($product['mediaFormatId'] === self::HIMO_MEDIA_FORMAT_ID) {
                    $response['workFormatName'] = self::MSDB_ITEM_AUDIO_SINGLE_NAME;
                }
            }

            if (empty($response['saleType'])) {
                $response['saleType'] = $productRepository->convertProductTypeToStr($product['productTypeId']);
            }
            if ($response['workTypeId'] === self::WORK_TYPE_THEATER) {
                // 映画作品の場合は固定でいれる
                $response['saleType'] = self::SALE_TYPE_THEATER;
            }

            // 年齢チェック表示チェック
            $displayImage = checkAgeLimit(
                $this->ageLimitCheck,
                $response['ratingId'],
                $response['adultFlg'],
                $response['bigGenreId'],
                $response['mediumGenreId'],
                $response['smallGenreId'],
                $product['makerCd']);
            // ジャケ写の挿入
            $response['jacketL'] = ($displayImage) ? $product['jacketL'] : '';
            // アダルト判定
            $isAdult = isAdult(
                $response['ratingId'],
                $response['bigGenreId'],
                $response['mediumGenreId'],
                $response['smallGenreId'],
                $product['makerCd']
            );

            if (array_key_exists('docText', $response)) {
                $docs = json_decode($response['docText'], true);
                if (!empty($docs)) {

                    if ($product['msdbItem'] === 'video') {
                        $response['docText'] = getSummaryComment(DOC_TABLE_MOVIE['tol'], $docs);
                        $isDocSet = true;
                    } else if ($product['msdbItem'] === 'book') {
                        $response['docText'] = getSummaryComment(DOC_TABLE_BOOK['tol'], $docs);
                        $isDocSet = true;
                    } else if ($product['msdbItem'] === 'audio') {
                        $response['docText'] = getSummaryComment(DOC_TABLE_MUSIC['tol'], $docs, true);
                        $isDocSet = true;
                    } else if ($product['msdbItem'] === 'game') {
                        $response['docText'] = getSummaryComment(DOC_TABLE_GAME['tol'], $docs);
                        $isDocSet = true;
                    }
                }
            }
        }
        $response['newFlg'] = newFlg($response['saleStartDate']);

        // アダルトフラグがない場合、アダルト判定処理でアダルトと判定された場合はtrueにする。
        if ($isAdult !== null) {
            $response['adultFlg'] = ($response['adultFlg'] === '1') ? true : $isAdult;
        } else {
            $response['adultFlg'] = ($response['adultFlg'] === '1') ? true : false;
        }

        $response['itemType'] = $this->convertWorkTypeIdToStr($response['workTypeId']);

        if ($response['workFormatId'] == self::WORK_FORMAT_ID_MUSICVIDEO) {
            $response['itemType'] = 'dvd';
        }
        if ($addSaleTypeHas) {
            if($response['workTypeId'] === self::WORK_TYPE_THEATER) {
                $response['saleTypeHas'] = [
                    'sell' => false,
                    'rental' => false,
                    'theater' => true,
                ];
                $response['jacketL'] = current(json_decode($response['sceneL']));
            } else {
                $response['saleTypeHas'] = [
                    'sell' => ($productModel->setConditionByWorkIdSaleType($response['workId'], 'sell')->count() > 0) ? true : false,
                    'rental' => ($productModel->setConditionByWorkIdSaleType($response['workId'], 'rental')->count() > 0) ? true : false,
                    'theater' => false,
                ];
            }
        }
        // docがセットできなかった場合はブランクにする。
        if ($isDocSet === false) {
            $response['docText'] = '';
        }

        // 映画の場合は入れ替える
        if($response['workTypeId'] === self::WORK_TYPE_THEATER) {
            $response['jacketL'] = current(json_decode($response['sceneL']));
        }

        // musicoリンク
        $response['musicDownloadUrl'] = null;
        $musicoUrl = new MusicoUrl;
        $musicoUrlData = $musicoUrl->setConditionByWorkId($response['workId'])->toCamel()->getOne();
        if (!empty($musicoUrlData)) {
            $response['musicDownloadUrl'] = env('MUSICO_URL') . $musicoUrlData->url;
        }

        return $response;
    }

    function getPerson($msdbItem, $productUniqueId)
    {
        $people = new People;
        $roleId = null;
        $person = null;

        if ($msdbItem === 'book') {
            foreach (self::HIMO_ROLE_ID_BOOK as $id) {
                $person = $people->setConditionByRoleId($productUniqueId, $id)->getOne();
                if (!empty($person)) break;
            }
        } elseif ($msdbItem === 'audio' || $msdbItem === 'video') {
            foreach (self::HIMO_ROLE_ID_MUSIC as $id) {
                $person = $people->setConditionByRoleId($productUniqueId, $id)->getOne();
                if (!empty($person)) break;
            }
        }
        return $person;
    }


    /**
     * Insert work data and related work data: Product, People
     *
     * @param $himoResult
     * @param $work
     * @return array
     *
     * @throws NoContentsException
     */
    public function insertWorkData($himoResult)
    {
        $productRepository = new ProductRepository();
        $peopleRepository = new PeopleRepository();
        // Create transaction for insert multiple tables
        DB::beginTransaction();
        try {
            $workData = [];
            $productData = [];
            $peopleData = [];
            $musicoUrlInsertArray = [];
            $discasCCCprodctIdInsertArray = [];
            foreach ($himoResult['results']['rows'] as $row) {
                $workData[] = $this->format($row);
                $insertWorkId[] = $row['work_id'];
                //$insertResult = $work->insert($base);
                $musicoUrl = null;
                $isMusicVideo = false;
                $discasCCCprodctId = null;
                $deleteProduct = [];
                foreach ($row['products'] as $product) {
                    // ダウンロード用のデータ生成
                    // 単一想定
                    if ($product['service_id'] === 'musico') {
                        if ($product['product_type_id'] == self::PRODUCT_TYPE_ID_ALBUM) {
                            $musicoUrl = sprintf(self::MUSICO_LINK_ALBUM, $product['ccc_product_id']);
                        } else if ($product['product_type_id'] == self::PRODUCT_TYPE_ID_SINGLE) {
                            $musicoUrl = sprintf(self::MUSICO_LINK_SINGLE, $product['ccc_product_id']);
                        }
                    } else if ($product['service_id'] === 'discas') {
                        $discasCCCprodctId = $product['ccc_product_id'];
                    }

                    // ミュジックビデオの場合はaudioからvideoに変換するために判定する。
                    if ($row['work_format_id'] == self::WORK_FORMAT_ID_MUSICVIDEO) {
                        $isMusicVideo = true;
                    }
                    $productData[] = $productRepository->format($row['work_id'], $product, $isMusicVideo);
                    // 削除対象のIDを抽出
                    $deleteProduct[] = $product['id'];
                    // Insert people
                    if ($people = array_get($product, 'people')) {
                        foreach ($people as $person) {
                            $peopleData[] = $peopleRepository->format($product['id'], $person);
                        }
                    }
                }
                if (!empty($musicoUrl)) {
                    $musicoUrlInsertArray[] = [
                        'work_id' => $row['work_id'],
                        'url' => $musicoUrl
                    ];
                }
                if (!empty($discasCCCprodctId)) {
                    $discasCCCprodctIdInsertArray[] = [
                        'work_id' => $row['work_id'],
                        'ccc_product_id' => $discasCCCprodctId
                    ];
                }
            }
            $productModel = new Product();
            $peopleModel = new People();
            $musicoUrl = new MusicoUrl();
            $discasProduct = new DiscasProduct();

            $this->work->insertBulk($workData, $insertWorkId);

            // デッドロックがDELETEで起こっていた。
            // 一日一回DBをTRUNCATEしている為、削除処理はなくす。

            $productModel->insertBulk($productData);
            $peopleModel->insertBulk($peopleData);
            $musicoUrl->insertBulk($musicoUrlInsertArray);
            $discasProduct->insertBulk($discasCCCprodctIdInsertArray);

            DB::commit();
            return $insertWorkId;
        } catch (\Exception $exception) {
            \Log::error("Error while update work. Error message:{$exception->getMessage()} Line: {$exception->getLine()}");
            DB::rollback();
            throw new $exception;
        }

    }

    public function searchKeyword($keyword, $sort = null, $itemType = null, $periodType = null, $adultFlg = null)
    {
        $himoRepository = new HimoRepository('asc', $this->offset, $this->limit);

        $params = [
            'keyword' => $keyword,
            'itemType' => $itemType,
            'periodType' => $periodType,
            'adultFlg' => $adultFlg,
            'api' => 'search',//dummy data
            'id' => $keyword //dummy data
        ];

        $result = [
            'hasNext' => false,
            'totalCount' => 0,
            'counts' => [
                'dvd' => 0,
                'cd' => 0,
                'book' => 0,
                'game' => 0
            ],
            'rows' => []
        ];

        // DVDタブを指定して検索した場合はミュージックビデオ（msdb_item=audio）を含める
        if ($itemType === 'dvd') {
            $params['genreId'] = implode(' || ', self::HIMO_SEARCH_VIDEO_GENRE_ID);
        }

        if ($adultFlg !== 'true') {
            if (array_key_exists('genreId', $params)) {
                $params['genreId'] .= self::HIMO_SEARCH_IGNORE_ADULT_GENRE_ID;
            } else {
                $params['genreId'] = self::HIMO_SEARCH_IGNORE_ADULT_GENRE_ID;
            }
        }
        $dvdCount = 0;
        $data = $himoRepository->searchCrossworks($params, $sort, true)->get();
        if (!empty($data['status']) && $data['status'] == '200') {
            if (count($data['results']['rows']) + $this->offset < $data['results']['total']) {
                $this->hasNext = true;
            } else {
                $this->hasNext = false;
            }

            // DVDタブを指定して検索した場合はfacetsで取得した値のかわりにこの値を返却する
            if ($itemType === 'dvd') $dvdCount = $data['results']['total'];

            $result = [
                'hasNext' => $this->hasNext,
                'totalCount' => $data['results']['total'],
                'counts' => [
                    'dvd' => 0,
                    'cd' => 0,
                    'book' => 0,
                    'game' => 0
                ],
                'rows' => []
            ];

            foreach ($data['results']['rows'] as $row) {
                $base = $this->format($row);
                $itemTypeVal = $this->convertWorkTypeIdToStr($base['work_type_id']);
                $saleTypeHas = $this->parseFromArray($row['products'], $itemTypeVal);
                $displayImage = true;
                $displayImage = checkAgeLimit(
                    $this->ageLimitCheck,
                    $base['rating_id'],
                    $base['adult_flg'],
                    $base['big_genre_id'],
                    $base['medium_genre_id'],
                    $base['small_genre_id'],
                    $saleTypeHas['maker_cd']);
                $workFormatName = "";
                if ($itemTypeVal === 'cd') {
                    if ($saleTypeHas['media_format_id'] === self::HIMO_MEDIA_FORMAT_ID) {
                        $workFormatName = self::MSDB_ITEM_AUDIO_SINGLE_NAME;
                    } else {
                        $workFormatName = $base['work_format_name'];
                    }
                    if ($base['work_format_id'] == self::WORK_FORMAT_ID_MUSICVIDEO) {
                        $itemTypeVal = 'dvd';
                    }
                }
                // アダルト判定
                $isAdult = isAdult(
                    $base['rating_id'],
                    $base['big_genre_id'],
                    $base['medium_genre_id'],
                    $base['small_genre_id'],
                    $saleTypeHas['maker_cd']
                );

                $saleStartDateSell = "";
                if ($saleTypeHas['sell'] === true) {
                    $saleStartDateSell = ($row['sale_start_date']) ? date('Y-m-d 00:00:00', strtotime($saleTypeHas['saleStartDateSell'])) : '';
                }

                $saleStartDateRental = "";
                if ($saleTypeHas['rental'] === true) {
                    $saleStartDateRental = ($row['sale_start_date']) ? date('Y-m-d 00:00:00', strtotime($saleTypeHas['saleStartDateRental'])) : '';
                }

                $result['rows'][] = [
                    'workId' => $base['work_id'],
                    'urlCd' => $base['url_cd'],
                    'cccWorkCd' => $base['ccc_work_cd'],
                    'workTitle' => $base['work_title'],
                    'jacketL' => ($displayImage) ? $base['jacket_l'] : '',
                    'newFlg' => newFlg($base['sale_start_date']),
                    'adultFlg' => ($base['adult_flg'] === 1) ? true : $isAdult,
                    'itemType' => $itemTypeVal,
                    'saleType' => '',
                    'supplement' => $saleTypeHas['supplement'],
                    'saleStartDate' => ($row['sale_start_date']) ? date('Y-m-d 00:00:00', strtotime($row['sale_start_date'])) : '',
                    'saleStartDateSell' => $saleStartDateSell,
                    'saleStartDateRental' => $saleStartDateRental,
                    'saleTypeHas' => [
                        'sell' => $saleTypeHas['sell'],
                        'rental' => $saleTypeHas['rental'],
                        'theater' => $saleTypeHas['theater'],
                    ],
                    'workFormatName' => $workFormatName
                ];
            }
        }

        //check counts of all itemType
        $ItemTypesCheck = ['cd', 'dvd', 'book', 'game'];
        $dataCounts = $data;
        if (in_array(strtolower(array_get($params, 'itemType')), $ItemTypesCheck)) {
            $params['itemType'] = 'all';
            $params['responseLevel'] = '1';
            $himoRepository->setLimit(1);
            $himoRepository->setOffset(0);

            // DVDタブを指定して検索した場合はジャンルを指定しているが、
            // facetsを取得する際は「すべて」タブ指定時との条件が変わらないようにクリアする
            if ($itemType === 'dvd') $params['genreId'] = "";

            $dataCounts = $himoRepository->searchCrossworks($params, $sort)->get();

            $result['totalCount'] = $dataCounts['results']['total'];

        }
        if (!empty($dataCounts['results']['facets']['msdb_item'])) {
            foreach ($dataCounts['results']['facets']['msdb_item'] as $value) {
                switch ($value['key']) {
                    case 'video':
                        if ($itemType === 'dvd') {
                            // DVDタブを指定して検索した場合は最初に取得した結果件数を設定
                            $result['counts']['dvd'] = $dvdCount;
                        } else {
                            $result['counts']['dvd'] = $value['count'];
                        }
                        break;
                    case 'audio':
                        $result['counts']['cd'] = $value['count'];
                        break;
                    case 'book':
                        $result['counts']['book'] = $value['count'];
                        break;
                    case 'game':
                        $result['counts']['game'] = $value['count'];
                        break;
                }
            }
        }

        return $result;
    }

    public function parseFromArray($products, $itemType)
    {
        $sell = false;
        $rental = false;
        $thater = false;
        $supplement = '';
        $mediaFormatId = '';
        $saleStartDateSell = null;
        $saleStartDateRental = null;
        foreach ($products as $product) {
            // VHSを除外
            if ($product['service_id'] === 'tol' || $product['service_id'] === 'st') {

                if ($product['product_type_id'] === 1 ) { // VHSの条件を除外
                    // 最新の販売開始日を取得する。
                    if ($product['sale_start_date'] > $saleStartDateSell) {
                        $saleStartDateSell = $product['sale_start_date'];
                    }
                    $sell = true;
                } else if ($product['product_type_id'] === 2 ) { // VHSの条件を除外
                    // 最新の販売開始日を取得する。
                    if ($product['sale_start_date'] > $saleStartDateRental) {
                        $saleStartDateRental = $product['sale_start_date'];
                    }
                    $rental = true;
                } else if (empty($product['product_type_id']) && $product['service_id'] === 'st') { // VHSの条件を除外
                    $thater = true;
                }
                if ($itemType === 'game') {
                    $supplement = $product['game_model_name'];
                } else {
                    if ($itemType === 'book') {
                        foreach (self::HIMO_ROLE_ID_BOOK as $id) {
                            $supplement = $this->parseSupplement($product['people'], $id);
                            if (!empty($supplement)) break;
                        }
                    } elseif ($itemType === 'cd') {
                        foreach (self::HIMO_ROLE_ID_MUSIC as $id) {
                            $supplement = $this->parseSupplement($product['people'], $id);
                            if (!empty($supplement)) break;
                        }
                    }
                }
                $mediaFormatId = $product['media_format_id'];
                $makerCd = $product['maker_cd'];
            }
        }
        return [
            'sell' => $sell,
            'rental' => $rental,
            'theater' => $thater,
            'supplement' => $supplement,
            'media_format_id' => $mediaFormatId,
            'maker_cd' => $makerCd,
            'saleStartDateSell' => $saleStartDateSell,
            'saleStartDateRental' => $saleStartDateRental,
        ];

    }

    public function parseSupplement($people, $roleId)
    {
        foreach ($people as $person) {
            if ($person['role_id'] === $roleId) {
                return $person['person_name'];
            }
        }
    }

    /**
     * API GET: /people/{personId}
     *
     * @param $personId
     * @param null $sort
     * @param null $saleType
     *
     * @return array|null
     *
     * @throws NoContentsException
     */
    public function person($personId, $sort = null, $itemType = null)
    {
        $himoRepository = new HimoRepository();

        $params = [
            'personId' => $personId,
            'saleType' => $this->saleType,
            'itemType' => $itemType,
            'responseLevel' => 1,
            'id' => $personId,//dummy data
            'api' => 'crossworks',//dummy data
        ];
        $himoRepository->setLimit(100);
        $data = $himoRepository->searchCrossworks($params, $sort)->get();
        if (empty($data['status']) || $data['status'] != '200' || empty($data['results']['total'])) {
            throw new NoContentsException();
        }
        foreach ($data['results']['rows'] as $row) {
            $workList[] = $row['work_id'];
        }
        $this->getWorkList($workList);
        $this->work->getWorkWithProductIdsIn($workList, $this->saleType, null, $sort);
        $this->totalCount = $this->work->count();
        $works = $this->work->selectCamel($this->selectColumn())->get($this->limit, $this->offset);
        if (count($works) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }

        // STEP 7:フォーマットを変更して返却
        $workItems = [];
        foreach ($works as $workItem) {
            $workItem = (array)$workItem;
            $formatedItem = $this->formatAddOtherData($workItem, false, $workItem, true);
            foreach ($formatedItem as $key => $value) {
                if (in_array($key, $this->outputColumn())) {
                    $formatedItemSelectColumn[$key] = $value;
                }
            }
            $workItems[] = $formatedItemSelectColumn;
        }


        return $workItems;
    }

    /**
     * API GET: /genre/{personId}
     *
     * @param $personId
     * @param null $sort
     * @param null $saleType
     *
     * @return array|null
     *
     * @throws NoContentsException
     */

    public function genre($genreId)
    {
        $himoRepository = new HimoRepository('asc', $this->offset, $this->limit);

        $params = [
            'genreId' => $genreId,
            'saleType' => $this->saleType,
            'api' => 'genre',//dummy data
            'id' => $genreId //dummy data
        ];

        $data = $himoRepository->searchCrossworks($params, $this->sort)->get();

        if (!empty($data['status']) && $data['status'] == '200') {
            if (count($data['results']['rows']) + $this->offset < $data['results']['total']) {
                $this->hasNext = true;
            } else {
                $this->hasNext = false;
            }

            $this->totalCount = $data['results']['total'];
            $displayImage = true;
            foreach ($data['results']['rows'] as $row) {
                $base = $this->format($row);
                $itemType = $this->convertWorkTypeIdToStr($base['work_type_id']);
                if ($base['work_format_id'] == self::WORK_FORMAT_ID_MUSICVIDEO) {
                    $itemType = 'dvd';
                }
                $saleTypeHas = $this->parseFromArray($row['products'], $itemType);
                $displayImage = true;
                $displayImage = checkAgeLimit(
                    $this->ageLimitCheck,
                    $base['rating_id'],
                    $base['adult_flg'],
                    $base['big_genre_id'],
                    $base['medium_genre_id'],
                    $base['small_genre_id'],
                    $saleTypeHas['maker_cd']);
                // アダルト判定
                $isAdult = isAdult(
                    $base['rating_id'],
                    $base['big_genre_id'],
                    $base['medium_genre_id'],
                    $base['small_genre_id'],
                    $saleTypeHas['maker_cd']
                );
                $result[] = [
                    'workId' => $base['work_id'],
                    'urlCd' => $base['url_cd'],
                    'cccWorkCd' => $base['ccc_work_cd'],
                    'workTitle' => $base['work_title'],
                    'jacketL' => ($displayImage) ? $base['jacket_l'] : '',
                    'newFlg' => newFlg($base['sale_start_date']),
                    'adultFlg' => ($base['adult_flg'] === 1) ? true : $isAdult,
                    'itemType' => $itemType,
                    'saleType' => $this->saleType,
                    // DVDの場合は空にする。
                    'supplement' => ($itemType === 'dvd') ? '' : $saleTypeHas['supplement'],
                    'saleStartDate' => ($row['sale_start_date']) ? date('Y-m-d 00:00:00', strtotime($row['sale_start_date'])) : '',
                    'saleStartDateSell' => ($row['sale_start_date_sell']) ? date('Y-m-d 00:00:00', strtotime($row['sale_start_date_sell'])) : '',
                    'saleStartDateRental' => ($row['sale_start_date_rental']) ? date('Y-m-d 00:00:00', strtotime($row['sale_start_date_rental'])) : '',
                ];
            }
        }
        return $result;
    }

    public function convert($idType, $id)
    {
        $idCode = null;
        switch ($idType) {
            case 'workId':
                $idCode = '0102';
                break;
            case 'cccWorkCd':
                $idCode = '0103';
                break;
            case 'urlCd':
                $idCode = '0105';
                break;
            case 'jan':
                $idCode = '0205';
                break;
            case 'rentalProductId':
                $idCode = '0206';
                break;
        }
        $himoRepository = new HimoRepository();
        $workRepository = new WorkRepository();
        $himoResult = $himoRepository->crosswork([$id], $idCode, '1')->get();
        if (empty($himoResult['results']['rows'])) {
            return null;

        }
        $result['workId'] = $himoResult['results']['rows'][0]['work_id'];
        $result['itemType'] = $workRepository->convertWorkTypeIdToStr($himoResult['results']['rows'][0]['work_type_id']);
        if ($himoResult['results']['rows'][0]['work_format_id'] == self::WORK_FORMAT_ID_MUSICVIDEO) {
            $result['itemType'] = 'dvd';
        }
        return $result;
    }

    public function format($row, $isNarrow = false)
    {
        // Initial key value.
        $base = [
            'ccc_work_cd' => '',
            'url_cd' => '',
        ];
        foreach ($row['ids'] as $idItem) {
            // HiMO作品ID
            if ($idItem['id_type'] === '0103') {
                $base['ccc_work_cd'] = $idItem['id_value'];
                // URLコード
            } else if ($idItem['id_type'] === '0105') {
                $base['url_cd'] = $idItem['id_value'];
            }
        }

        // ベースのデータの整形
        $base['work_id'] = $row['work_id'];
        $base['msdb_item'] = $row['msdb_item'];
        $base['work_type_id'] = $row['work_type_id'];
        $base['work_format_id'] = $row['work_format_id'];
        $base['work_format_name'] = $row['work_format_name'];
        $base['work_title'] = $row['work_title'];
        $base['work_title_orig'] = $row['work_title_orig'];
        $base['copyright'] = $row['work_copyright'];
        $base['jacket_l'] = trimImageTag($row['jacket_l']);
        $base['scene_l'] = $this->sceneFormat($row['scenes']);
        $base['sale_start_date'] = $row['sale_start_date'];
        if (array_key_exists('docs', $row)) {
            $base['doc_text'] = json_encode($row['docs']);
        }
        if ($isNarrow === false) {
            if (!empty($row['genres'])) {
                $base['big_genre_id'] = $row['genres'][0]['big_genre_id'];
                $base['big_genre_name'] = $row['genres'][0]['big_genre_name'];
                $base['medium_genre_id'] = $row['genres'][0]['medium_genre_id'];
                $base['medium_genre_name'] = $row['genres'][0]['medium_genre_name'];
                $base['small_genre_id'] = $row['genres'][0]['small_genre_id'];
                $base['small_genre_name'] = $row['genres'][0]['small_genre_name'];
            }
            $base['filmarks_id'] = $this->filmarksIdFormat($row);
            $base['rating_id'] = $row['rating_id'];
            $base['rating_name'] = $row['rating_name'];
            $base['adult_flg'] = $row['adult_flg'];
            $base['created_year'] = $row['created_year'];
            $base['created_countries'] = $row['created_countries'];
            $base['book_series_name'] = $row['book_series_name'];
        }
        // アイテム種別毎に整形フォーマットを変更できるように
        switch ($row['work_type_id']) {
            case self::WORK_TYPE_CD:
                $base['itemType'] = 'cd';
                break;
            case self::WORK_TYPE_DVD:
                $base['itemType'] = 'dvd';
                break;
            case self::WORK_TYPE_BOOK:
                $base['itemType'] = 'book';
                break;
            case self::WORK_TYPE_GAME:
                $base['itemType'] = 'game';
                break;
        }
        return $base;
    }

    public function convertWorkTypeIdToStr($workTypeId)
    {
        $itemType = null;
        switch ($workTypeId) {
            case self::WORK_TYPE_CD:
                $itemType = 'cd';
                break;
            case self::WORK_TYPE_DVD:
                $itemType = 'dvd';
                break;
            case self::WORK_TYPE_BOOK:
                $itemType = 'book';
                break;
            case self::WORK_TYPE_GAME:
                $itemType = 'game';
                break;
            case self::WORK_TYPE_MUSIC_UNIT:
                $itemType = 'cd';
                break;
            case self::WORK_TYPE_THEATER:
                $itemType = 'dvd';
                break;
        }
        return $itemType;
    }

    public function convertWorkTypeIdToMsdbItem($workTypeId)
    {
        $itemType = null;
        switch ($workTypeId) {
            case self::WORK_TYPE_CD:
                $itemType = 'audio';
                break;
            case self::WORK_TYPE_DVD:
                $itemType = 'video';
                break;
            case self::WORK_TYPE_BOOK:
                $itemType = 'book';
                break;
            case self::WORK_TYPE_GAME:
                $itemType = 'game';
                break;
            case self::WORK_TYPE_MUSIC_UNIT:
                $itemType = 'audio';
                break;
        }
        return $itemType;
    }

    private function sceneFormat($data)
    {
        $result = [];
        foreach ($data as $image) {
            // 表示条件
            // disable_flg（緊急非表示用フラグ）
            //  = 0：表示可
            // provider（連携元サービス）
            //  =  0（YouTube） ：非表示
            //  =  1（Jst）     ：非表示
            //  =  2（Stinglay）：[size = 3]のもののみ表示
            //  = 99（その他）  ：全て表示
            if ($image['disable_flg'] != '0') {
                continue;
            }
            if ($image['provider'] == '2') {
                if ($image['size'] == '3') {
                    $result[] = trimImageTag($image['url'], true);
                }
            } elseif ($image['provider'] == '99') {
                $result[] = trimImageTag($image['url'], true);
            }
        }
        return json_encode($result, JSON_UNESCAPED_SLASHES);
    }

    private function filmarksIdFormat($row)
    {
        if (!empty($row['filmarks_id'][0])) {
            return $row['filmarks_id'][0];
        }
        return null;
    }

    private function outputColumn()
    {
        return [
            'workId',
            'urlCd',
            'cccWorkCd',
            'workTitle',
            'newFlg',
            'jacketL',
            'supplement',
            'saleType',
            'itemType',
            'adultFlg'
        ];
    }

    private function selectColumn()
    {
        return [
            'w1.work_id',
            'work_type_id',
            'work_title',
            'work_format_id',
            'scene_l',
            'rating_id',
            'big_genre_id',
            'medium_genre_id',
            'small_genre_id',
            'url_cd',
            'ccc_work_cd',
//            'w1.jacket_l',
            'p2.jacket_l',
            'p2.sale_start_date',
            'p2.product_type_id',
            'p2.product_unique_id',
            'product_name',
            'maker_name',
            'game_model_name',
            'adult_flg',
            'p2.msdb_item',
            'media_format_id',
            'number_of_volume',
            'item_cd',
            'maker_cd'
        ];
    }
}
