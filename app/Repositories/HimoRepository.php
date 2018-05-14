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
        $this->apiHost = env('HIMO_API_HOST');
        $this->method = 'POST';
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /*
     * 詳細情報を取得するAPIをセットする
     */
    public function crosswork($ids, $idType = self::ID_TYPE, $responseLevel = '9')
    {
        $this->api = 'crossworks';
        $this->id = $ids;
        if(env('APP_ENV') === 'local'){
            return $this;
        }
        $this->apiPath = $this->apiHost . '/search/crossworks';
        foreach ($ids as $id) {
            $queryId[] = $idType . ':' . $id;
        }
        $this->queryParams = [
            '_system' => 'TsutayaApp',
            'id_value' => implode(' || ', $queryId),
            'service_id' => 'tol',
            'scene_limit' => '20',
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

        $this->apiPath = $this->apiHost . '/search/xmedia';
        foreach ($ids as $id) {
            $queryId[] = $idType . ':' . $id;
        }
        $this->queryParams = [
            '_system' => 'TsutayaApp',
            'id_value' => implode(' || ', $queryId),
            'service_id' => 'tol',
            'xmedia_mode' => '1',
            // 'msdb_item' => ['music','video','book','game'],
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
    public function xmediaRelatedWork($ids, $idType = self::ID_TYPE)
    {
        $this->api = 'xmediaRelation';
        $this->id = $ids;
        if(env('APP_ENV') === 'local'){
            return $this;
        }
        $this->apiPath = $this->apiHost . '/search/xmedia';
        foreach ($ids as $id) {
            $queryId[] = $idType . ':' . $id;
        }
        $this->queryParams = [
            '_system' => 'TsutayaApp',
            'id_value' => implode(' || ', $queryId),
            'service_id' => 'tol',
            'xmedia_mode' => '5',
            'xmedia_item_type' => ['1', '2', '3', '4'],
            'response_level' => '9',
            'offset' => $this->offset,
            'limit' => $this->limit,
            'sort_by' => 'auto:asc',
        ];

        return $this;
    }

    public function productDetail($ids, $idType = self::ID_TYPE ,$produtTypeId )
    {

        $this->api = 'product_detail';
        $this->id = $ids;
        if(env('APP_ENV') === 'local'){
            return $this;
        }
        foreach ($ids as $id) {
            $queryId[] = $idType . ':' . $id;
        }
        $this->apiPath = $this->apiHost . '/search/product_detail';
        $this->queryParams = [
            '_system' => 'TsutayaApp',
            'id_value' => implode(' || ', $queryId),
            'msdb_item' => 'audio',
            'product_type_id' => $produtTypeId,
            'service_id' => 'tol',
            'offset' => $this->offset,
            'limit' => $this->limit,
            'sort_by' => 'auto:asc',
        ];

        return $this;
    }
    public function searchCrossworksForRelease($params = [], $sort = null)
    {
        $this->api = $params['api'];
        $this->id = $params['id'];
        if (env('APP_ENV') === 'local') {
            return $this;
        }
        $this->apiPath = $this->apiHost . '/search/crossworks';
        $this->queryParams = [
            '_system' => 'TsutayaApp',
            'service_id' => 'tol',
            'response_level' => '9',
            'adult_flg' => '2',
            'offset' => $this->offset,
            'limit' => $this->limit,
            'sort_by' => $params['sort'],
            'work_products_service_id' => ['tol'],
            'genre_id' => $params['genre'],
            'msdb_item' => $params['msdbItem'],
            'sale_start_month' => $params['saleStartMonth'],
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

        $this->apiPath = $this->apiHost . '/search/crossworks';
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

        if(array_key_exists('responseLevel',$params)) {
            $this->queryParams['responseLevel'] = $params['itemType'];
        }

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
        if (array_key_exists('adultFlg', $params)) {
            if ($params['adultFlg'] !== 'true') {
                $this->queryParams['adult_flg'] = '2';
            }
        }

        //check periodType
        if(array_key_exists('periodType',$params)){
            $saleStartDateTo = date('Y-m-d');
            $saleStartDateFrom = $productSellRentalFlg = null;
            if ($params['periodType'] == 'rental3' || $params['periodType']  == 'sale3') {
                $saleStartDateFrom = date('Y-m-d', strtotime('-3 months'));
            } elseif ($params['periodType']  == 'rental12' || $params['periodType']  == 'sale12') {
                $saleStartDateFrom = date('Y-m-d', strtotime('-12 months'));
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

        // Check personId
        if(array_key_exists('personId', $params)){
            $this->queryParams['id_value'] = "0301:" . $params['personId'];
        }
        return $this;
    }


    public function searchPeople($ids, $idType, $msdbItem = null, $responseLevel = 9)
    {
        $this->api = 'people';
        $this->id = $ids;
        if(env('APP_ENV') === 'local'){
            return $this;
        }
        foreach ($ids as $id) {
            $queryId[] = $idType . ':' . $id;
        }
        $this->apiPath = $this->apiHost . '/search/people';
        $this->queryParams = [
            '_system' => 'TsutayaApp',
            'service_id' => 'tol',
            'id_value' => implode(' || ', $queryId),
            'response_level' => $responseLevel,
            'offset' => $this->offset,
            'limit' => $this->limit,
            'sort_by' => 'auto:asc',
        ];
        if ($msdbItem) {
            $this->queryParams['msdb_item'] = $msdbItem;
        }
        return $this;
    }

    public function searchRelatedPeople($id) {
        $this->api = 'related_people';
        $this->id = $id;
        if(env('APP_ENV') === 'local'){
            return $this;
        }
        $this->apiPath = $this->apiHost . '/search/related_people';
        $this->queryParams = [
            '_system' => 'TsutayaApp',
            'service_id' => 'tol',
            'person_id' => $id,
            'offset' => $this->offset,
            'limit' => $this->limit,
            // 'sort_by' => 'auto:asc',
        ];
        return $this;
    }

    // override
    // getが実行された際に、キャッシュへ問い合わせを行う。
    // データ存在していれば、DBから値を取得
    // 存在していなければ、Himoから取得して返却する
    // 返却した値は、DBに格納する
    public function get($jsonResponse = true)
    {
        if(env('APP_ENV') !== 'local' && env('APP_ENV') !== 'testing' ){
            return parent::get($jsonResponse);
        }
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
        // Remove new line character
        return \GuzzleHttp\json_decode(str_replace(["\n","\r\n","\r", PHP_EOL], '', $file), true);
       // return json_decode($file, TRUE);
    }
}
