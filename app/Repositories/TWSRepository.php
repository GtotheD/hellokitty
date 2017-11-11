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
class TWSRepository extends ApiRequesterRepository
{

    private $sort;
    private $offset;
    private $limit;
    private $apiHost;
    private $apiKey;

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        $this->sort = $sort;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->apiHost = env('TWS_API_HOST');
        $this->apiKey = env('TWS_API_KEY');
    }

    /*
     * 詳細情報を取得するAPIをセットする
     */
    public function detail($janCode)
    {
        $this->apiPath = $this->apiHost . '/store/v0/products/detail.json';
        $this->queryParams = [
            'api_key' => $this->apiKey,
            'productKey' => $janCode,
            'tolPlatformCode' => '00',
            '_secure' => '1',
            '_pretty' => '1'
        ];
        return $this;
    }

    /*
     * ランキング情報を取得するAPIをセットする
     */
    public function ranking($rankingConcentrationCd, $period)
    {
        $this->apiPath = $this->apiHost . '/media/v0/works/tsutayarankingresult.json';
        $this->queryParams = [
            'api_key' => $this->apiKey,
            'rankingConcentrationCd' => $rankingConcentrationCd,
            'tolPlatformCode' => '00',
            'rankinglimit' => $this->limit,
            'dispNums' => '10',
            '_secure' => '1',
            '_pretty' => '1'
        ];
        if (!empty($period)) {
            $this->queryParams['totalingPeriodFrom'] = $period;
        }
        return $this;
    }

    /*
     * 日付ベースの検索結果を取得するAPIをセットする
     */
    public function release($genreId, $storeProductItemCd)
    {
        $this->apiPath = $this->apiHost . '/store/v0/products/searchDetail.json';
        $this->queryParams = [
            'api_key' => $this->apiKey,
            '_secure' => '1',
            'page' => '1',
            'dispNums' => '10',
            'adultAuthOK' => '0',
            'adultFlag' => '1',
            'sortingOrder' => '2',
            'lg' => $genreId, // 大ジャンルコード
            'ic' => $this->itemCodeMapping($storeProductItemCd), // アイテム集約コード
            'storeProductItemCd' => $storeProductItemCd, // 店舗取扱いアイテムコード
            'dfy' => date('Y'),
            'dfm' => date('m'),
            'dfd' => date('d'),
            '_pretty' => '1'
        ];
        return $this;
    }

    private function itemCodeMapping($storeProductItemCd)
    {
        $maps = [
            '011' => '002',
            '012' => '002',
            '013' => '002',
            '020' => '001',
            '030' => '010',
            '111' => '002',
            '112' => '002',
            '113' => '002',
            '120' => '001',
            '130' => '010',
            '140' => '003'
        ];
        return $maps[$storeProductItemCd];
    }
}