<?php
namespace App\Repositories;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/13
 * Time: 15:01
 */

class TAPRepository extends ApiRequesterRepository
{

    private $sort;
    private $offset;
    private $limit;
    private $apiHost;
    private $apiKey;

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        parent::__construct();
        $this->sort = $sort;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->apiHost = env('TAP_API_HOST');
        $this->apiKey = env('TAP_API_KEY');
    }

    /*
     * 日付ベースの検索結果を取得するAPIをセットする
     */
    public function release($genreId, $storeProductItemCd) {
        $this->apiPath = $this->apiHost . 'store/v0/products/searchDetail.json';
        $this->queryParams = [
            'api_key' => $this->apiKey,
            '_secure' => '1',
            'page' => '1',
            'dispNums' => '10',
            'adultAuthOK' => '0',
            'adultFlag'=> '1',
            'sortingOrder'=> '2',
            'lg' => $genreId, // 大ジャンルコード
            'ic' => $this->itemCodeMapping($storeProductItemCd), // アイテム集約コード
            'storeProductItemCd' => $storeProductItemCd, // 店舗取扱いアイテムコード
            'dfy'=> date('Y'),
            'dfm'=> date('m'),
            'dfd'=> date('d'),
            '_pretty' => '1'
        ];
        return $this;
    }
}