<?php

namespace App\Repositories;

use App\Model\Product;
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

    public function get($productUniqueId)
    {
        return $this->product->setConditionByProductUniqueId($productUniqueId)->toCamel(['id'])->getOne();
    }


    public function getByWorkId($workId)
    {
        $results = $this->product->setConditionByWorkIdSaleType($workId, $this->saleType)->toCamel()->get();
        return $this->productReformat($results);
    }

    public function getNarrow($workId)
    {
        $column = [
            "product_name AS productName",
            "product_unique_id AS productUniqueId",
            "item_cd AS itemCd",
            "item_name AS itemName",
            "product_type_id AS productTypeId",
            "jacket_l AS jacketL",
            "jan AS jan",
            "rental_product_cd AS rentalProductCd",
            "sale_start_date AS saleStartDate",
        ];
        $this->totalCount = $this->product->setConditionByWorkIdSaleType($workId, $this->saleType)->count();
        $results = $this->product->select($column)->get($this->limit, $this->offset);
        if (count($results) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }

        return $this->productReformat($results);
    }
    public function getRentalGroup($workId)
    {
        $column = [
            "product_name AS productName",
            "jacket_l AS jacketL",
            "sale_start_date AS saleStartDate",
            "ccc_family_cd AS cccFamilyCd",
            "dvd",
            "bluray",
        ];
        $this->totalCount = $this->product->setConditionRentalGroup($workId)->count();
        $results = $this->product->select($column)->get($this->limit, $this->offset);
        if (count($results) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }

        return $this->rentalGroupReformat($results);


    }
    private function rentalGroupReformat($products)
    {
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

            $reformatResult[] = $product;
        }
        return $reformatResult;

    }

    private function productReformat($products)
    {
        // reformat data
        foreach ($products as $product) {
            $product = (array)$product;
            $product['itemName'] = $this->convertItemCdToStr($product['itemCd']);
            $product['saleType'] = $this->convertProductTypeToStr($product['productTypeId']);
            $product['jacketL'] = trimImageTag($product['jacketL']);
            $product['newFlg'] = newFlg($product['saleStartDate']);
            $reformatResult[] = $product;
        }
        return $reformatResult;
    }

    public function convertProductTypeToStr($productTypeId)
    {
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
            case '21':
                $item = 'dvd';
                break;
            case '22':
                $item = 'bluray';
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
    public function getNewestProductWorkIdSaleType($workId, $saleType)
    {
        $result = $this->product->setConditionByWorkIdNewestProduct($workId, $saleType)->toCamel()->getOne();
        return $result;
    }

    /**
     * Format data for Product object
     *
     * @param $workId
     * @param $product
     * @return array
     */
    public function format ($workId, $product) {
        $productBase = [];
        $productBase['work_id'] = $workId;
        $productBase['product_unique_id'] = $product['id'];
        $productBase['product_id'] = $product['product_id'];
        $productBase['product_code'] = $product['product_code'];
        $productBase['jan'] = $product['jan'];
        $productBase['ccc_family_cd'] = $product['ccc_family_cd'];
        $productBase['rental_product_cd'] = $product['rental_product_cd'];
        $productBase['product_name'] = $product['product_name'];
        $productBase['product_type_id'] = $product['product_type_id'];
        $productBase['product_type_name'] = $product['product_type_name'];
        $productBase['sale_start_date'] = $product['sale_start_date'];
        $productBase['service_id'] = $product['service_id'];
        $productBase['service_name'] = $product['service_name'];
        $productBase['msdb_item'] = $product['msdb_item'];
        $productBase['item_cd'] = $product['item_cd'];
        $productBase['item_name'] = $product['item_name'];
        $productBase['disc_info'] = $product['disc_info'];
        $productBase['subtitle'] = $product['subtitle'];
        $productBase['sound_spec'] = $product['sound_spec'];
        $productBase['region_info'] = $product['region_info'];
        $productBase['price_tax_out'] = $product['price_tax_out'];
        $productBase['play_time'] = $product['play_time'];
        $productBase['jacket_l'] = $product['jacket_l'];
        $productBase['sale_start_date'] = $product['sale_start_date'];
//                    $productBase['contents'] = $product['contents'];
//                    $productBase['privilege'] = $product['privilege'];
        $productBase['best_album_flg'] = $product['best_album_flg'];
        $productBase['maker_name'] = $product['maker_name'];

        return $productBase;
    }

    public function stock($storeId, $productKey)
    {
        $message = null;
        $lastUpdate = null;
        $res = null;
        $statusCode = 0;
        $length = strlen($productKey);
        // rental_product_cd
        if ($length === 9) {
            $res = $this->product->setConditionByRentalProductCd($productKey)->get();
            foreach ($res as $item)
            {
                $queryIdList[] = $item->rental_product_cd;
            }
        //jan
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
                if ($stockStatus['level'] == 0) {
                    $statusCode = 0;
                } else if ($stockStatus['level'] == 1) {
                    $statusCode = 1;
                } else {
                    $statusCode = 2;
                }
                if (array_key_exists('message', $stockStatus) ) {
                    $message = $stockStatus['message'];
                }
                if (array_key_exists('lastUpDate', $stockInfo['entry']['stockInfo'][0]) ) {
                    $lastUpdate = date('Y-m-d H:i', strtotime($stockInfo['entry']['stockInfo'][0]['lastUpDate']));
                }
            }
        }
        return [
            'status' => $statusCode,
            'message' => $message,
            'takeTime' => $lastUpdate,
        ];
    }


}