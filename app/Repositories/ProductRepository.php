<?php

namespace App\Repositories;

use App\Model\Product;
use App\Model\Work;
use Illuminate\Support\Facades\Log;
use App\Repositories\WorkRepository;
/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/13
 * Time: 15:01
 */
class ProductRepository
{

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

    public function get($workId)
    {
        $productModel = new Product();
        $result = $productModel->setConditionByWorkIdSaleType($workId, $this->saleType)->toCamel()->get();
        return $this->productReformat($results);
    }

    public function getNarrow($workId)
    {
        $productModel = new Product();
        $column = [
            "product_name AS productName",
            "product_unique_id AS productUniqueId",
            "item_cd AS itemCd",
            "item_name AS itemName",
            "product_type_id AS productTypeId",
            "jacket_l AS jacketL",
            "sale_start_date AS saleStartDate",
        ];
        $this->totalCount = $productModel->setConditionByWorkIdSaleType($workId, $this->saleType)->count();
        $results = $productModel->select($column)->get($this->limit, $this->offset);
        if (count($results) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }

        return $this->productReformat($results);
    }

    private function productReformat($products) {
        $workRepository = new WorkRepository();
        // reformat data
        foreach ($products as $product) {
            $product = (array)$product;
            $product['itemName'] = $this->convertItemCdToStr($product['itemCd']);
            $product['saleType'] = $this->convertProductTypeToStr($product['productTypeId']);
            $product['jacketL'] = $workRepository->trimImageTag($product['jacketL']);
            $product['newFlg'] = $workRepository->newLabel($product['saleStartDate']);
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
    public function insert($workId,  $product)
    {
        $productModel = new Product();
        $productBase = [];
        $productBase['product_unique_id'] = $product['id'];
        $productBase['product_id'] = $product['product_id'];
        $productBase['product_code'] = $product['product_code'];
        $productBase['jan'] = $product['jan'];
        $productBase['ccc_family_cd'] = $product['ccc_family_cd'];
        $productBase['ccc_product_id'] = $product['ccc_product_id'];
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
        return $productModel->insert($workId, $productBase);
    }

}