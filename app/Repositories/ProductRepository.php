<?php

namespace App\Repositories;

use App\Model\Product;
use App\Model\Work;
use Illuminate\Support\Facades\Log;

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
        return $result;
    }
    public function getNarrow($workId)
    {
        $productModel = new Product();
        $column = [
            "product_name",
            "product_unique_id",
//            "product_key",
            "item_cd",
            "jacket_l",
            "sale_start_date",
        ];
        $result = $productModel->setConditionByWorkIdSaleType($workId, $this->saleType)->select($column)->get();
        return $result;
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