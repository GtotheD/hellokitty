<?php

namespace App\Repositories;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\Repositories\WorkRepository;
use App\Model\Work;

/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/13
 * Time: 15:01
 */
class HimoRepository extends ApiRequesterRepository
{

    protected $sort;
    protected $offset;
    protected $limit;
    protected $apiHost;
    protected $apiKey;

    const ID_TYPE = '0102';
    const INTEGRATION_API = '/search/crossworks';

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        $this->sort = $sort;
        $this->offset = $offset;
        $this->limit = $limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /*
     * 詳細情報を取得するAPIをセットする
     */
    public function crosswork($ids, $idType = self::ID_TYPE, $responseLevel = 9)
    {
        $this->api = 'crossworks';
        $this->id = $ids;
        if(env('APP_ENV') === 'local'){
            return $this;
        }
        foreach ($ids as $id) {
            $queryId[] = $idType . ':' . $id;
        }
        $this->params = [
            '_system' => 'TsutayaApp',
            'id_value' => implode(' || ', $queryId),
            'service_id' => 'tol',
            'msdb_item' => 'video',
            'adult_flg' => '2',
            'response_level' => $responseLevel,
            'offset' => $this->offset,
            'limit' => $this->limit,
            'sort_by' => 'auto:asc',
        ];

        return $this;
    }

    /**
     * @param $ids
     *
     * @return $this
     */
    public function xmediaSeries($ids, $idType = self::ID_TYPE)
    {
        $this->api = 'xmediaSeries'; // for stub
        $this->id = $ids;
        if(env('APP_ENV') === 'local'){
            return $this;
        }
        foreach ($ids as $id) {
            $queryId[] = $idType . ':' . $id;
        }
        $this->params = [
            '_system' => 'TsutayaApp',
            'id_value' => implode(' || ', $queryId),
            'service_id' => 'tol',
            'msdb_item' => 'video',
            'adult_flg' => '2',
            'response_level' => '9',
            'offset' => $this->offset,
            'limit' => $this->limit,
            'sort_by' => 'auto:asc',
        ];

        return $this;
    }

    /**
     * @param $ids
     *
     * @return $this
     */
    public function xmediaRelation($ids, $idType = self::ID_TYPE)
    {
        $idType = '';
        $this->api = 'xmediaRelation';
        $this->id = $ids;
        if(env('APP_ENV') === 'local'){
            return $this;
        }
        foreach ($ids as $id) {
            $queryId[] = $idType . ':' . $id;
        }
        $this->params = [
            '_system' => 'TsutayaApp',
            'id_value' => implode(' || ', $queryId),
            'service_id' => 'tol',
            'msdb_item' => 'video',
            'adult_flg' => '2',
            'response_level' => '9',
            'offset' => $this->offset,
            'limit' => $this->limit,
            'sort_by' => 'auto:asc',
        ];

        return $this;
    }

    public function productDetail($ids, $idType = self::ID_TYPE )
    {
        $this->api = 'productDetail';
        $this->id = $ids;
        if(env('APP_ENV') === 'local'){
            return $this;
        }
        foreach ($ids as $id) {
            $queryId[] = $idType . ':' . $id;
        }
        $this->params = [
            '_system' => 'TsutayaApp',
            'id_value' => implode(' || ', $queryId),
            'service_id' => 'tol',
            'msdb_item' => 'video',
            'adult_flg' => '2',
            'response_level' => '9',
            'offset' => $this->offset,
            'limit' => $this->limit,
            'sort_by' => 'auto:asc',
        ];

        return $this;
    }

    public function searchCrossworks($params = [], $sort = null)
    {
        $this->api = $params['api'];
        $this->id = $params['id'];

        if (env('APP_ENV') === 'local') {
            return $this;
        }

        $sortBy = 'auto:asc';
        if ($sort == 'new') {
            $sortBy = 'sale_start_date:desc';
        } else if ($sort == 'old') {
            $sortBy = 'sale_start_date:asc';
        }

        $this->queryParams = [
            '_system' => 'TsutayaApp',
            'service_id' => 'tol',
            'response_level' => '9',
            'offset' => $this->offset,
            'limit' => $this->limit,
            'sort_by' => $sortBy,
        ];

        //check itemType
        if(array_key_exists('itemType',$params)){
            $msdbItem = ['audio', 'video', 'book', 'game'];
            switch (strtolower($params['itemType'])) {
                case 'cd':
                    $msdbItem = ['audio'];
                    break;
                case 'dvd':
                    $msdbItem = ['video'];
                    break;
                case 'book':
                    $msdbItem = ['book'];
                    break;
                case 'game':
                    $msdbItem = ['game'];
                    break;
            }
            $this->queryParams['msdb_item'] = $msdbItem;
            $this->queryParams['facet_keys'] = 'msdb_item';
        }

        //check adultFlg
        $adultFlg = '2';
        if (array_key_exists('adultFlg', $params)) {
            if ($params['adultFlg'] == 'true') {
                $adultFlg = '0';
            } else {
                $adultFlg = '2';
            }
        }
        $this->queryParams['adult_flg'] = $adultFlg;

        //check periodType
        if(array_key_exists('periodType',$params)){
            $saleStartDateTo = date('m/d/Y');
            $saleStartDateFrom = $productSellRentalFlg = null;
            if ($params['periodType'] == 'rental3' || $params['periodType']  == 'sale3') {
                $saleStartDateFrom = date('m/d/Y', strtotime('-3 months'));
            } elseif ($params['periodType']  == 'rental12' || $params['periodType']  == 'sale12') {
                $saleStartDateFrom = date('m/d/Y', strtotime('-12 months'));
            }

            if (strpos($params['periodType'], 'rental') !== false) {
                $productSellRentalFlg = 2;
            } elseif (strpos($params['periodType'], 'sell') !== false) {
                $productSellRentalFlg = 1;
            }

            if (!empty($saleStartDateFrom)) {
                $this->queryParams['sale_start_date_from'] = $saleStartDateFrom;
                $this->queryParams['sale_start_date_to'] = $saleStartDateTo;
                $this->queryParams['product_sell_rental_flg'] = $productSellRentalFlg;
            }
        }

        //checkKeyword
        if(array_key_exists('keyword',$params)){
            $this->queryParams['query'] = $params['keyword'];
        }

        //check saleType
        if (array_key_exists('saleType', $params)) {
            $productSellRentalFlg = null;
            if ($params['saleType'] == 'rental') {
                $productSellRentalFlg = 2;
            } elseif ($params['saleType'] == 'sell') {
                $productSellRentalFlg = 1;
            }
            if (!empty($productSellRentalFlg)) {
                $this->queryParams['product_sell_rental_flg'] = $productSellRentalFlg;
            }
        }

        //check genre_id
        if(array_key_exists('genreId',$params)){
            $this->queryParams['genre_id'] = $params['genreId'].':';

        }

        return $this;
    }

    // override
    // getが実行された際に、キャッシュへ問い合わせを行う。
    // データ存在していれば、DBから値を取得
    // 存在していなければ、Himoから取得して返却する
    // 返却した値は、DBに格納する
    public function get($jsonResponse = true)
    {
        // Check and read array workId
        if(!is_array($this->id)) {
            return $this->stub($this->api, $this->id);
        }

        // Get multi works in local
        $results = [];

        foreach ($this->id as $key => $workId) {
            if(!$results) {
                $results = $this->stub($this->api, $workId);
            }
            else {
                $response = $this->stub($this->api, $workId);
                if($response) {
                    $results['results']['rows'][] = array_first($response['results']['rows']);
                    $results['results']['total'] = $key + 1;
                }
            }
        }
        return $results;
    }

    private function stub($apiName, $filename)
    {
        if($this->api === 'xmediaSeries') {
            $filename .= '_1';
            $apiName = 'xmedia';
        } else  if ($this->api === 'xmediaRelation') {
            $filename .= '_2';
            $apiName = 'xmedia';
        }

        $path = base_path('tests/himo/');
        $path = $path . $apiName;
        if(!realpath($path . '/' . $filename)) {
            return null;
        }
        $file = file_get_contents($path . '/' . $filename);
        return json_decode($file, TRUE);
    }
}
