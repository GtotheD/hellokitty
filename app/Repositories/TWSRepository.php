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

class TWSRepository
{

    private $sort;
    private $offset;
    private $limit;
    private $apiHost;
    private $apiKey;
    private $apiPath;
    private $queryParams;


    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        $this->sort = $sort;
        $this->offset = $offset;
        $this->limit = $limit;

        $this->apiHost = env('TWS_API_HOST');
        $this->apiKey = env('TWS_API_KEY');
    }

    /*
     * 取得の実行
     */
    public function get() {

        $url = $this->apiHost . $this->apiPath;
        $client = new Client();
        try {
            $result = $client->request(
                'GET',
                $url,
                ['query' => $this->queryParams]
            );
        } catch (ClientException $e) {
            throw new $e;
        }

        return json_decode($result->getBody()->getContents(), true);

    }

    /*
     * 詳細情報を取得するAPIをセットする
     */
    public function detail($janCode) {
        $this->apiPath = '/store/v0/products/detail.json';
        $this->queryParams = [
            'api_key' => $this->apiKey,
            'productKey' => $janCode,
            'tolPlatformCode' => '00',
            '_secure' => '1'
        ];
        return $this;
    }

    /*
     * ランキング情報を取得するAPIをセットする
     */
    public function ranking($rankingConcentrationCd) {
        $this->apiPath = '/media/v0/works/tsutayarankingresult.json';
        $this->queryParams = [
            'api_key' => $this->apiKey,
            'rankingConcentrationCd' => $rankingConcentrationCd,
            'tolPlatformCode' => '00',
            'rankinglimit' => $this->limit,
//            'dispNums' => '100', todo: rankinglimitで制限されているからいらない？
            '_secure' => '1'
        ];
        return $this;
    }

    /*
     * 日付ベースの検索結果を取得するAPIをセットする
     */
    public function release($startDate, $itemCode) {
        $this->apiPath = '/store/v0/products/searchDetail.json';
        $this->queryParams = [
            'api_key' => $this->apiKey,
            '_secure' => '1',
            'page' => '1',
            'dispNums' => '10',
            'adultAuthOK' => '0',
            'adultFlag'=> '1',
            'sortingOrder'=> '2',
            'storeProductItemCd' => '221',
            'lg' => '25', // アイテム集約コード
            'ic' => '002',
            'dfy'=> '2017',
            'dfm'=> '11',
            'dfd'=> '04'
        ];
        return $this;
    }



}