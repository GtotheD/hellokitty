<?php

namespace App\Repositories;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\Repositories\WorkRepository;
use App\Model\Work;
use Illuminate\Filesystem;

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
    const MEDIA_FORMAT_ID_VHS = 'EXT00001Q3OJ';

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
        if (env('APP_ENV') === 'local') {
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
            'work_products_service_id' => ['tol', 'musico', 'discas','st'],
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
        if (env('APP_ENV') === 'local') {
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
        if (env('APP_ENV') === 'local') {
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
            'xmedia_mode' => '7',
            'offset' => $this->offset,
            'limit' => $this->limit,
        ];

        return $this;
    }

    public function productDetail($ids, $idType = self::ID_TYPE, $produtTypeId)
    {

        $this->api = 'product_detail';
        $this->id = $ids;
        if (env('APP_ENV') === 'local') {
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
        // リリカレ情報のみを取得するために制定現の情報しか取得しない
        $this->queryParams = [
            '_system' => 'TsutayaApp',
            'service_id' => 'tol',
            'response_level' => '1',
            'adult_flg' => $params['adultFlg'],
            'offset' => $this->offset,
            'limit' => $this->limit,
            'sort_by' => $params['sort'],
            'work_products_service_id' => ['tol'],
            'msdb_item' => $params['msdbItem'],
            'product_sell_rental_flg' => $params['productSellRentalFlg'],
            'sale_start_date_newest_from' => $params['saleStartDateFrom'],
            'sale_start_date_newest_to' => $params['saleStartDateTo'],
        ];

        if (array_key_exists('genre', $params)) {
            if ($params['msdbItem'][0] === 'game') {
                $this->queryParams['game_model_id'] = $params['genre'];
            } else {
                $this->queryParams['genre_id'] = $params['genre'];
            }
        }
        if (array_key_exists('workTags', $params)) {
            $this->queryParams['work_tags'] = $params['workTags'];
        }
        return $this;
    }

    public function crossworksArtistRelatedWork($personId, $sort = null)
    {
        $this->api = 'crossworks';
        $this->id = $personId;
        if (env('APP_ENV') === 'local') {
            return $this;
        }
        $this->apiPath = $this->apiHost . '/search/crossworks';
        $this->queryParams = [
            '_system' => 'TsutayaApp',
            'service_id' => 'tol',
            'scene_limit' => '20',
            'response_level' => '1',
            'id_value' => '0301:' . $personId,
            // ※ アイテムコードの以下を除外）1051:アクセサリー, 1054:グッズ, 1056:チケット
            'item_cd' => '-1051 && -1054 && -1056',
            'offset' => $this->offset,
            'limit' => $this->limit,
            'work_products_service_id' => ['tol'],
            'msdb_item' => ['audio']
        ];
        $this->queryParams['sort_by'] = 'auto:asc';
        if ($sort == 'old') {
            $this->queryParams['sort_by'] = 'sale_start_date:asc';
        } else {
            $this->queryParams['sort_by'] = 'sale_start_date:desc';
        }
        return $this;
    }

    public function searchCrossworks($params = [], $sort = null, $musicVideoGenre = false)
    {
        $this->api = $params['api'];
        $this->id = $params['id'];
        if (env('APP_ENV') === 'local') {
            return $this;
        }
        $this->apiPath = $this->apiHost . '/search/crossworks';
        $sortBy = 'auto:desc';
        if ($sort == 'new') {
            $sortBy = 'sale_start_date:desc';
        } else if ($sort == 'old') {
            $sortBy = 'sale_start_date:asc';
        }

        $this->queryParams = [
            '_system' => 'TsutayaApp',
            'service_id' => ['tol', 'st'],
            'scene_limit' => '20',
            'work_products_service_id' => ['tol', 'st'],
            'response_level' => '9',
            'offset' => $this->offset,
            'limit' => $this->limit,
            'sort_by' => $sortBy,
        ];

        if (array_key_exists('responseLevel', $params)) {
            $this->queryParams['responseLevel'] = $params['itemType'];
        }

        //check itemType
        if (array_key_exists('itemType', $params)) {
            $msdbItem = ['audio', 'video', 'book', 'game'];
            switch (strtolower($params['itemType'])) {
                case 'cd':
                    $msdbItem = ['audio'];
                    break;
                case 'dvd':
                    if ($musicVideoGenre === true) {
                        $msdbItem = ['video', 'audio'];
                    } else {
                        $msdbItem = ['video'];
                    }
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
        if (array_key_exists('periodType', $params)) {
            $saleStartDateTo = date('Y-m-d');
            $saleStartDateFrom = $productSellRentalFlg = null;
            if ($params['periodType'] == 'rental3' || $params['periodType'] == 'sell3') {
                $saleStartDateFrom = date('Y-m-d', strtotime('-3 months'));
            } elseif ($params['periodType'] == 'rental12' || $params['periodType'] == 'sell12') {
                $saleStartDateFrom = date('Y-m-d', strtotime('-12 months'));
            }

            if (strpos($params['periodType'], 'rental') !== false) {
                $productSellRentalFlg = 2;
            } elseif (strpos($params['periodType'], 'sell') !== false) {
                $productSellRentalFlg = 1;
            }

            if (!empty($saleStartDateFrom)) {
                $this->queryParams['sale_start_date_newest_from'] = $saleStartDateFrom;
                $this->queryParams['sale_start_date_newest_to'] = $saleStartDateTo;
                $this->queryParams['product_sell_rental_flg'] = $productSellRentalFlg;
            }
        }

        //checkKeyword
        if (array_key_exists('keyword', $params)) {
            $this->queryParams['query'] = $params['keyword'];
            // キーワード検索の時にVHS除外の条件を足す
            // $this->queryParams['media_format_id'] = '-'.self::MEDIA_FORMAT_ID_VHS;
            // VHSを許可するように修正
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
        if (array_key_exists('genreId', $params)) {
            $this->queryParams['genre_id'] = $params['genreId'] . ':';
        }

        // Check personId
        if (array_key_exists('personId', $params)) {
            $this->queryParams['id_value'] = "0301:" . $params['personId'];
        }
        return $this;
    }


    public function searchPeople($ids, $idType, $msdbItem = null, $responseLevel = 9)
    {
        $this->api = 'people';
        $this->id = $ids;
        if (env('APP_ENV') === 'local') {
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
            // 新しいもの上位100件で取得するためソートを指定
            // 新しい順指定ができないので、おすすめ順に変更
            'sort_by' => 'auto:desc',
        ];
        if ($msdbItem) {
            $this->queryParams['msdb_item'] = $msdbItem;
        }
        return $this;
    }

    public function searchRelatedPeople($id)
    {
        $this->api = 'related_people';
        $this->id = $id;
        if (env('APP_ENV') === 'local') {
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
        if (env('APP_ENV') !== 'local' && env('APP_ENV') !== 'testing') {
            return parent::get($jsonResponse);
        }
        // Check and read array workId
        if (!is_array($this->id)) {
            return $this->stub($this->api, $this->id);
        }

        // Get multi works in local
        $results = [];
        foreach ($this->id as $key => $workId) {
            if (!$results) {
                $results = $this->stub($this->api, $workId);
            } else {
                $response = $this->stub($this->api, $workId);
                if ($response) {
                    $results['results']['rows'][] = array_first($response['results']['rows']);
                    $results['results']['total'] = $key + 1;
                }
            }
        }
        return $results;
    }

    private function stub($apiName, $filename)
    {
        if ($this->api === 'xmediaSeries') {
            $filename .= '_1';
            $apiName = 'xmedia';
        } else if ($this->api === 'xmediaRelation') {
            $filename .= '_2';
            $apiName = 'xmedia';
        }
        $path = base_path('tests/himo/');
        $path = $path . $apiName;
        if ($apiName === 'crossworks') {
            $list = glob($path . '/*/' . $filename);
            if (!empty($list)) {
                $file = file_get_contents($list[0]);
            } else {
                return null;
            }
        } else {
            if (!realpath($path . '/' . $filename)) {
                return null;
            }
            $file = file_get_contents($path . '/' . $filename);
        }
        $file = str_replace(["\n", "\r\n", "\r", PHP_EOL], '', $file);
        return json_decode($file, TRUE);
    }
}
