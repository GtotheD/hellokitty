<?php

namespace App\Repositories;

use App\Model\Product;
use App\Model\Work;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/13
 * Time: 15:01
 */
class ProductRepository
{
    private $product;

    protected $sort;
    protected $offset;
    protected $limit;
    protected $apiHost;
    protected $apiKey;
    protected $saleType;
    protected $totalCount;
    protected $hasNext;

    const PRODUCT_TYPE_SELL = '1';
    const PRODUCT_TYPE_RENTAL = '2';

    const MSDBITEM_NAME_AUDIO = 'audio';
    const MSDBITEM_NAME_VIDEO = 'video';
    const MSDBITEM_NAME_BOOK = 'book';
    const MSDBITEM_NAME_GAME = 'game';

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        $this->sort = $sort;
        $this->offset = $offset;
        $this->limit = $limit;

        $this->product = new Product();
    }


    /**
     * @return mixed
     */
    public function getHasNext()
    {
        return $this->hasNext;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return (int)$this->limit;
    }

    /**
     * @return mixed
     */
    public function getOffset()
    {
        return (int)$this->offset;
    }

    /**
     * @return mixed
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @return Array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @param mixed $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @param mixed $offset
     */
    public function setSaleType($saleType)
    {
        $this->saleType = $saleType;
    }

    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    public function get($productUniqueId)
    {
        $product = $this->product->setConditionByProductUniqueId($productUniqueId)->toCamel(['id','base_product_code','is_dummy'])->getOne();
        if (empty($product)) {
            return null;
        }
        // CDレンタルだった場合
        if ($product->msdbItem === 'audio' && $product->productTypeId == $this->product::PRODUCT_TYPE_ID_RENTAL) {
            $rentalProducts = $this->product->setConditionByWorkIdForRentalCd($product->workId)->toCamel(['id','base_product_code','is_dummy'], 't2.')->get();
            if (empty($rentalProducts)) {
                return null;
            }
            if (count($rentalProducts) === 1) {
                return $this->productReformat([$product])[0];
            }
            $baseRentalProduct = [];
            foreach ($rentalProducts as $rentalProduct) {
                $rentalProduct = (array)$rentalProduct;
                // ベースとなるプロダクトを設定
                if (empty($baseRentalProduct)) {
                    $baseRentalProduct = $rentalProduct;
                    $work = new Work();
                    $workData =  $work->setConditionByWorkId($product->workId)->toCamel()->getOne();
                    $baseRentalProduct['productName'] = $workData->workTitle;
                    $baseRentalProduct['contents'] = "■" . $rentalProduct['productName'] . "\n" . $rentalProduct['contents'];
                } else {
                // コンテンツのマージ
                $baseRentalProduct['productCode'] = $baseRentalProduct['productCode'] . ', '.$rentalProduct['productCode'];
                // コンテンツのマージ
                    $baseRentalProduct['contents'] = $baseRentalProduct['contents'] . "\n" . "■" . $rentalProduct['productName'] . "\n" . $rentalProduct['contents'];
                }
            }
            return $this->productReformat([$baseRentalProduct])[0];
        }
        return $this->productReformat([$product])[0];
    }


    public function getByWorkId($workId)
    {
        $results = $this->product->setConditionByWorkIdSaleType($workId, $this->saleType)->toCamel()->get();
        return $this->productReformat($results);
    }

    public function getNarrow($workId)
    {
        $order = null;
        $column = [
            "t2.product_name",
            "t2.product_unique_id",
            "t2.item_cd",
            "t2.item_name",
            "t2.product_type_id",
            "t2.jacket_l",
            "t2.jan",
            "t2.rental_product_cd",
            "t2.number_of_volume",
            "t2.sale_start_date",
            "t2.price_tax_out",
        ];
        $isAudio = false;
        $products = $this->product->setConditionByWorkIdNewestProduct($workId)->select('msdb_item')->getOne();
        if(empty($products)) {
            return null;
        }
        // レンタルCDだった場合
        if ($products->msdb_item === 'audio' && $this->saleType === 'rental') {
            $isAudio = true;
            $rentalProducts = $this->product->setConditionByWorkIdForRentalCd($workId)->get();
            // 複数ある場合
            if (count($rentalProducts) > 1) {
                // Productをworkのタイトルに書き換える対応
                // 結果を１件にセット
                $column = array_diff($column, ['t2.product_name']);
                // カラムを入れ替えるために追加
                $column[] = 't1.work_title AS productName';
                $this->totalCount = 1;
                $this->hasNext = false;
                $results = $this->product->selectCamel($column)->get(1, 0);
                return $this->productReformat($results);
            }
        }

        $this->totalCount = $this->product
            ->setConditionProductGroupingByWorkIdSaleType($workId, $this->saleType, $this->sort, $isAudio, false)
            ->count();
        $results = $this->product
            ->setConditionProductGroupingByWorkIdSaleType($workId, $this->saleType, $this->sort, $isAudio, false)
            ->selectCamel($column)
            ->get($this->limit, $this->offset);

        if ($products->msdb_item === 'video') {

            // １，PPTを除いた取得結果VHS等の特殊媒体のみだった場合PPTも検索
            $research = true;
            if(!empty($results)) {
                foreach ($results as $row) {
                    $baseItemCode = substr($row->itemCd, -2);
                    // いずれかが存在してい場合は再検索を実施しない
                    if($baseItemCode === '21' || $baseItemCode === '21' ) {
                        $research = false;
                        break;
                    }
                }
            }

            // PPTを除いたProductが存在しない場合PPTを含めて検索する。
            // また、１にて再検索が必要だった場合は再検索
            $pptResults = false;
            if (empty($results) || $research === true) {
                $this->product = new Product();
                $pptResults = $this->product
                    ->setConditionProductGroupingByWorkIdSaleType($workId, $this->saleType, $this->sort, $isAudio, true)
                    ->selectCamel($column)
                    ->get($this->limit, $this->offset);
                // 検索した結果dvdもしくはbrが存在していればVHS以外は除いて出力する。
                $pptResults = $this->checkVideoDataFilter($pptResults);
                // データが存在しなかった場合はresultにコピーして件数を取得しなおす
                if (!empty($pptResults)) {
                    $results = $pptResults;
                    $this->totalCount = $this->product
                        ->setConditionProductGroupingByWorkIdSaleType($workId, $this->saleType, $this->sort, $isAudio, true)
                        ->count();
                }
            }
        }


        if (count($results) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }

        return $this->productReformat($results);
    }

    public function getRentalGroup($workId, $sort = null)
    {
        $columnOutput = [
            "t2.product_name AS productName",
            "t2.product_unique_id AS productUniqueId",
            "t2.jacket_l AS jacketL",
            "t2.sale_start_date AS saleStartDate",
            "t2.ccc_family_cd AS cccFamilyCd",
        ];
        $this->totalCount = $this->product->setConditionRentalGroup($workId, $sort)->count();
        $results = $this->product->get($this->limit, $this->offset);
        foreach ($results as $result) {
            $tmp = $this->product->setConditionRentalGroupNewestCccProductId(
                $result->work_id, $result->ccc_family_cd, $result->sale_start_date
            )->select($columnOutput)->getOne();
            $tmp->dvd = $result->dvd;
            $tmp->bluray = $result->bluray;
            $response[] = $tmp;
        }
        if (empty($response)) {
            return null;
        }
        if (count($results) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }
        return $this->rentalGroupReformat($response);
    }

    private function rentalGroupReformat($products)
    {
        $reformatResult = null;
        $message = config('messages.onlyVHS');
        // reformat data
        foreach ($products as $product) {
            $product = (array)$product;
            $product['jacketL'] = trimImageTag($product['jacketL']);
            $product['productKeys'] = [
                'dvd' => $product['dvd'],
                'bluray' => $product['bluray'],
            ];
            $product['newFlg'] = newFlg($product['saleStartDate']);
            unset($product['dvd']);
            unset($product['bluray']);
            // VHSのみだった場合のメッセージ
            $product['message'] = $message;

            $reformatResult[] = $product;
        }
        return $reformatResult;

    }

    private function productReformat($products)
    {
        $reformatResult = [];
        $message = config('messages.onlyVHS');
        // reformat data
        foreach ($products as $product) {
            $product = (array)$product;
            if ((substr($product['itemCd'], -2) === '75' && !empty($product['numberOfVolume'])) ||
                (substr($product['itemCd'], -2) === '76' && !empty($product['numberOfVolume']))) {
                $product['productName'] = $product['productName'] . "（{$product['numberOfVolume']}）";
            }
            $product['productKey'] = ($product['productTypeId'] == self::PRODUCT_TYPE_SELL) ? $product['jan'] : $product['rentalProductCd'];
            if (array_key_exists('docs', $product)) {
                $docs = json_decode($product['docs'], true);
                if (!empty($docs)) {

                    if ($product['msdbItem'] === 'video') {
                        $product['docText'] = getSummaryComment(DOC_TABLE_MOVIE['tol'], $docs);
                    } else if ($product['msdbItem'] === 'book') {
                        $product['docText'] = getSummaryComment(DOC_TABLE_BOOK['tol'], $docs);
                    } else if ($product['msdbItem'] === 'audio') {
                        $product['docText'] = getSummaryComment(DOC_TABLE_MUSIC['tol'], $docs, true);
                    } else if ($product['msdbItem'] === 'game') {
                        $product['docText'] = getSummaryComment(DOC_TABLE_GAME['tol'], $docs);
                    }

                    if ($product['msdbItem'] === 'video') {
                        $product['contents'] = getProductContents(DOC_TABLE_MOVIE['tol'], DOC_TYPE_ID_TITLE, $docs);
                    } else if ($product['msdbItem'] === 'book') {
                        $product['contents'] = getProductContents(DOC_TABLE_BOOK['tol'], DOC_TYPE_ID_SCENE, $docs);
                    } else if ($product['msdbItem'] === 'audio') {
                    } else if ($product['msdbItem'] === 'game') {
                        }

                    if ($product['msdbItem'] === 'video') {
                        $product['privilege'] = getProductContents(DOC_TABLE_MOVIE['tol'], DOC_TYPE_ID_BONUS, $docs);
                    } else if ($product['msdbItem'] === 'book') {
                        $product['privilege'] = getProductContents(DOC_TABLE_BOOK['tol'], DOC_TYPE_ID_BONUS, $docs);
                    } else if ($product['msdbItem'] === 'audio') {
                        $product['privilege'] = getProductContents(DOC_TABLE_MUSIC['tol'], DOC_TYPE_ID_BONUS, $docs);
                    } else if ($product['msdbItem'] === 'game') {
                        $product['privilege'] = getProductContents(DOC_TABLE_GAME['tol'], DOC_TYPE_ID_BONUS, $docs);
                        }

                }
                unset($product['docs']);
            }
            if (array_key_exists('playTime', $product)) {
                $product['playTime'] = $this->editPlayTimeFormat($product['playTime']);
            }
            $product['itemName'] = $this->convertItemCdToStr($product['itemCd']);
            $product['saleType'] = $this->convertProductTypeToStr($product['productTypeId']);
            $product['jacketL'] = trimImageTag($product['jacketL']);
            $product['newFlg'] = newFlg($product['saleStartDate']);

            // best_album_flg の 状況に応じて文字列（ベスト盤）を返す
            if (array_key_exists('bestAlbumFlg', $product)) {
                $product['bestAlbumFlg'] =  ($product['bestAlbumFlg'] == '1') ? 'ベスト盤' : '';
            }
            // VHSのみだった場合のメッセージ
            $product['message'] = $message;
            $reformatResult[] = $product;
        }
        return $reformatResult;
    }

    public function convertProductTypeToStr($productTypeId)
    {
        $productTypeStr = null;
        switch ($productTypeId) {
            case self::PRODUCT_TYPE_SELL:
                $productTypeStr = 'sell';
                break;
            case self::PRODUCT_TYPE_RENTAL:
                $productTypeStr = 'rental';
                break;
        }
        return $productTypeStr;
    }

    public function convertItemCdToStr($itemCd)
    {
        $item = '';
        $itemCd = substr($itemCd, -2);
        switch ($itemCd) {
            case '20':
                $item = 'vhs';
                break;
            case '21':
                $item = 'dvd';
                break;
            case '22':
                $item = 'bluray';
                break;
            case '40':
                $item = 'game';
                break;
            case '75':
                $item = 'book';
                break;
        }
        return $item;
    }

    public function insert($workId, $product)
    {
        $productModel = new Product();
        $productBase = $this->format($workId, $product);
        return $productModel->insert($productBase);

    }

    /**
     * Get newest product by workId and saleType
     *
     * @param $workId
     * @param $saleType
     *
     * @return mixed
     */
    public function getNewestProductWorkIdSaleType($workId, $saleType, $isMovie)
    {
        $result = $this->product->setConditionByWorkIdNewestProduct($workId, $saleType, $isMovie)->toCamel()->getOne();
        return $result;
    }

    /**
     * Format data for Product object
     *
     * @param $workId
     * @param $product
     * @return array
     */
    public function format($workId, $product,bool $isVideo = false)
    {
        $productBase = [];
        $productBase['work_id'] = $workId;
        $productBase['product_unique_id'] = $product['id'];
        $productBase['product_id'] = $product['product_id'];
        $productBase['product_code'] = $product['product_code'];
        $productBase['base_product_code'] = preg_replace('/([A-Z]|[a-z]){1,2}$/','' ,$product['product_code']);
        $productBase['is_dummy'] = preg_match('/([A-Z]|[a-z]){1,2}$/', $product['product_code'], $matches);
        $productBase['jan'] = $product['jan'];
        $productBase['game_model_id'] = $product['game_model_id'];
        $productBase['game_model_name'] = $product['game_model_name'];
        $productBase['ccc_family_cd'] = $product['ccc_family_cd'];
        $productBase['ccc_product_id'] = $product['ccc_product_id'];
        $productBase['rental_product_cd'] = $product['rental_product_cd'];
        $productBase['product_name'] = $product['product_name'];
        $productBase['product_type_id'] = $product['product_type_id'];
        $productBase['product_type_name'] = $product['product_type_name'];
        $productBase['sale_start_date'] = $product['sale_start_date'];
        $productBase['service_id'] = $product['service_id'];
        $productBase['service_name'] = $product['service_name'];
        if ($isVideo ) {
            $productBase['msdb_item'] = 'video';
        } else {
            $productBase['msdb_item'] = $product['msdb_item'];
        }
        $productBase['item_cd'] = $product['item_cd'];
        $productBase['item_cd_right_2'] = substr($product['item_cd'], -2);
        $productBase['item_name'] = $product['item_name'];
        $productBase['number_of_volume'] = $product['number_of_volume'];
        $productBase['disc_info'] = $product['disc_info'];
        $productBase['subtitle'] = $product['subtitle'];
        $productBase['sound_spec'] = $product['sound_spec'];
        $productBase['region_info'] = $product['region_info'];
        $productBase['price_tax_out'] = $product['price_tax_out'];
        $productBase['play_time'] = $product['play_time'];
        $productBase['jacket_l'] = trimImageTag($product['jacket_l']);
        $productBase['docs'] = json_encode($product['docs']);
        $productBase['sale_start_date'] = $product['sale_start_date'];
        if ($product['msdb_item'] === 'audio') {
            $productBase['contents'] = $this->getDetail($product['product_id'], $product['product_type_id']);
        }


        $productBase['book_page_number'] = $product['book_page_number'];
        $productBase['book_size'] = $product['book_size'];
        $productBase['isbn10'] = $product['isbn_10'];
        $productBase['isbn13'] = $product['isbn_13'];
        $productBase['subtitle_flg'] = $product['subtitle_flg'];

        $productBase['best_album_flg'] = $product['best_album_flg'];
        $productBase['maker_cd'] = $product['maker_cd'];
        $productBase['maker_name'] = $product['maker_name'];
        $productBase['media_format_id'] = $product['media_format_id'];

        return $productBase;
    }

    public function stock($storeId, $productKey)
    {

        $message = null;
        $rentalPossibleDay = null;
        $lastUpdate = null;
        $res = null;
        $statusCode = 0;
        $isAudio = false;
        $length = strlen($productKey);
        // レンタルの場合はPPT等複数媒体がある場合がある為、対象を複数取得する
        if ($length === 9) {
            // CDかどうか確認する為に対象媒体を一度検索
            $products =  $this->product->setConditionByRentalProductCd($productKey)->select('msdb_item')->getOne();
            if(empty($products)) {
                return null;
            }
            if ($products->msdb_item === 'audio') {
                $isAudio = true;
            }
            $res = $this->product->setConditionByRentalProductCdFamilyGroup($productKey, $isAudio)->get();
            foreach ($res as $item) {
                $queryIdList[] = $item->rental_product_cd;
            }
        // JANで渡ってきた場合は、販売商品の為単一検索
        } elseif ($length === 13) {
            $queryIdList[] = $productKey;
        } else {
            throw new BadRequestHttpException();
        }
        $twsRepository = new TWSRepository();
        foreach ($queryIdList as $queryId) {
            $stockInfo = (array)$twsRepository->stock($storeId, $queryId)->get();
            if ($stockInfo !== null) {
                $stockStatus = $stockInfo['entry']['stockInfo'][0]['stockStatus'];
                if ($statusCode > $stockStatus['level']) {
                    continue;
                }
                $statusCode = 0;
                $message = null;
                $rentalPossibleDay = null;
                $lastUpdate = null;

                if ($stockStatus['level'] == 0) {
                    $statusCode = 0;
                } else if ($stockStatus['level'] == 1) {
                    $statusCode = 1;
                } else {
                    $statusCode = 2;
                }
                if (array_key_exists('rentalPossibleDay', $stockInfo['entry']['stockInfo'][0])) {
                    $rentalPossibleDay = date('Y-m-d', strtotime($stockInfo['entry']['stockInfo'][0]['rentalPossibleDay']));
                }
                if (array_key_exists('message', $stockStatus)) {
                    $message = $stockStatus['message'];
                }
                if (array_key_exists('lastUpDate', $stockInfo['entry']['stockInfo'][0])) {
                    $lastUpdate = date('Y-m-d H:i:s', strtotime($stockInfo['entry']['stockInfo'][0]['lastUpDate']));
                }
            }
        }
        return [
            'stockStatus' => $statusCode,
            'message' => $message,
            'rentalPossibleDay' => $rentalPossibleDay,
            'lastUpdate' => $lastUpdate,
        ];
    }


    /**
     * GET newest product by $workId. If work_id not exists in system. Call workRepository.
     *
     * @param $workId
     * @param null $saleType
     * @return mixed
     *
     * @throws NoContentsException
     */
    public function getNewestProductByWorkId($workId, $saleType = null, $isMovie = false)
    {
        $workRepository = new WorkRepository();
        $product = new Product();
        if ($saleType) {
            $workRepository->setSaleType($saleType);
        }

        $work = new Work();
        $work->setConditionByWorkId($workId);

        if ($work->count() == 0) {
            $response = $workRepository->get($workId);
            if (empty($response)) {
                throw new NoContentsException();
            }
        }

        return $product->setConditionByWorkIdNewestProduct($workId, $saleType, $isMovie);
    }

    public function getDetail($productId, $typeId)
    {
        $himo = new HimoRepository;
        $himoResult = $himo->productDetail([$productId], '0202', $typeId)->get();
        if (empty($himoResult)) {
            return null;
        }
        foreach ($himoResult['results']['rows'] as $item) {
            foreach ($item['tracks'] as $trackItem) {
                $trackGroups[$trackItem['disc_no']][] = [
                    'trackNo' => $trackItem['track_no'],
                    'trackTitle' => $trackItem['track_title'],
                    'playtime' => $trackItem['play_time']
                ];
            }
        }
        if (empty($trackGroups)) {
            return null;
        }
        $text = null;
        foreach ($trackGroups as $key => $strackGroup) {
            $text .= "Disc.{$key}\n";
            foreach ($strackGroup as $track) {
                $text .= "{$track['trackNo']}.{$track['trackTitle']}\n";
            }
            $text .= "\n";
        }
        return $text;
    }

    function editPlayTimeFormat($string)
    {
        $hour = (int)substr($string, 0, 2);
        $min = (int)substr($string, 2, 2);
        $min = $min + $hour * 60;
        if ($min === 0) {
            return '';
        }
        return ($hour != '00') ? "{$min}分" : "{$min}分";
    }

    public function convertMsdbItemToItemType($msdbItem)
    {
        $itemType = null;
        switch ($msdbItem) {
            case self::MSDBITEM_NAME_AUDIO :
                $itemType = 'cd';
                break;
            case self::MSDBITEM_NAME_VIDEO:
                $itemType = 'dvd';
                break;
            case self::MSDBITEM_NAME_BOOK:
                $itemType = 'book';
                break;
            case self::MSDBITEM_NAME_GAME:
                $itemType = 'game';
                break;
        }
        return $itemType;
    }


    /*
     * PPTフィルター
     * DBから商品を取得後、PPTのみ商品の場合はPPTを出力
     * PPT以外の場合は、PPTを削除する。
     */
    public function checkVideoDataFilter ($products) {
        // 存在確認
        $deleteFlg = false;
        foreach ($products as $product) {
            $itemCd = substr($product->itemCd, -2);
            if ($itemCd === '21' || $itemCd === '22') {
                $normalProducts[] = $product;
            } else {
                $otherProducts[] = $product;
            }
        }
        if (count($normalProducts) > 0 ) {
            return $normalProducts;
        }
        return false;
    }
}
