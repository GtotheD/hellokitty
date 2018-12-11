<?php

namespace App\Repositories;

use App\Exceptions\NoContentsException;

/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/13
 * Time: 15:01
 */
class TWSRepository extends ApiRequesterRepository
{

    protected $sort;
    protected $offset;
    protected $limit;
    protected $page;
    protected $apiHost;
    protected $apiKey;

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        parent::__construct();
        $this->sort = $sort;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->page = '1';
        $this->apiHost = env('TWS_API_HOST');
        $this->apiKey = env('TWS_API_KEY');
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

    /**
     * @param mixed $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
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

    public function stock($storeId, $productKey)
    {
        $this->apiPath = $this->apiHost . '/store/v0/products/detail.json';
        $this->api = 'stock';
        $this->id = $storeId . '_' . $productKey;

        $this->queryParams = [
            'api_key' => $this->apiKey,
            'productKey' => $productKey,
            'fieldSet' => 'stock',
            'storeId' => $storeId,
            'adultAuthOK' => '1',
            'tolPlatformCode' => '00',
            'syf' => '1' // 集約解除フラグ1=集約しない
        ];
        return $this;
    }
    /*
     * ランキング情報を取得するAPIをセットする
     */
    public function ranking($rankingConcentrationCd, $period)
    {
        $this->api = 'ranking';
        $this->id = $rankingConcentrationCd;
        if(env('APP_ENV') === 'local'){
            return $this;
        }

        $this->apiPath = $this->apiHost . '/media/v0/works/tsutayarankingresult.json';
        $this->queryParams = [
            'api_key' => $this->apiKey,
            'rankingConcentrationCd' => $rankingConcentrationCd,
            'tolPlatformCode' => '00',
            'rankinglimit' => '100',
            'dispNums' => $this->limit,
            'dispPageNo' => $this->page,
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
            'dispNums' => '20',
            'adultAuthOK' => '0',
            'adultFlag' => '1',
            'sortingOrder' => '2',
            'lg' => $genreId, // 大ジャンルコード
            'ic' => $this->itemCodeMapping($storeProductItemCd), // アイテム集約コード
            'storeProductItemCd' => $storeProductItemCd, // 店舗取扱いアイテムコード
            'dfy' => date('Y',strtotime('-1 month')),
            'dfm' => date('m',strtotime('-1 month')),
            'dfd' => date('d',strtotime('-1 month')),
            'dty' => date('Y',strtotime('next sunday')),
            'dtm' => date('m',strtotime('next sunday')),
            'dtd' => date('d',strtotime('next sunday')),
            '_pretty' => '1'
        ];
        return $this;
    }

    public function review($urlCd){
        $this->apiPath = $this->apiHost . '/media/v0/works/review.json';
        $page = floor(($this->offset + $this->limit) / $this->limit);

        $this->queryParams = [
            'api_key' => $this->apiKey,
            '_secure' => '1',
            'dispPageNo' => $page,
            'dispNums' => $this->limit,
            'tolPlatformCode' => '00',
            '_pretty' => '1',
            'urlCd' => $urlCd
        ];

        return $this;
    }

    public function getReview($urlCd){
        $apiResult = $this->review($urlCd)->get();
        $reviews = [
            'totalCount' => 0,
            'averageRating' => 0,
            'rows' => []
        ];
        if (!empty($apiResult) && array_key_exists('entry', $apiResult)) {
            foreach ($apiResult['entry'] as $review) {
                $reviews['rows'][] = [
                    'rating' => floatval(number_format($review['evalPoint'], 1)),
                    'contributor' => $review['contributorName'],
                    'contributeDate' => $review['contributeDate'],
                    'contents' => $review['commentText'],
                ];
            }
            if (!empty($reviews['rows'])) {
                $reviews['averageRating'] = floatval(number_format($apiResult['averageScore'], 1));
                $reviews['totalCount'] = intval($apiResult['totalResults']);
                return $reviews;
            }
        }

        return null;
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
        return $this->stub($this->api, $this->id);
    }

    private function stub($apiName, $filename)
    {
        $path = base_path('tests/Data/tws/');
        $path = $path . $apiName;
        if(!realpath($path . '/' . $filename)) {
            return null;
        }
        $file = file_get_contents($path . '/' . $filename);
        //　存在しなかった場合はTWS同様に擬似的に204をかえす
        if (empty($file)) {
            throw new NoContentsException('tws no contents exception');
        }
        // Remove new line character
        return \GuzzleHttp\json_decode(str_replace(["\n","\r\n","\r", PHP_EOL], '', $file), true);
        // return json_decode($file, TRUE);
    }

}