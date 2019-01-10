<?php
namespace App\Repositories;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Log;
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
    /*
     * 日付ベースの検索結果を取得するAPIをセットする
     */
    public function release($category, $releaseDateTo) {
        $this->apiPath = $this->apiHost . '/tsutayaappapi/Release';
        $this->queryParams = [
            'apiKey' => $this->apiKey,
            'category' => $category,
            'releaseDateTo' => $releaseDateTo
        ];
        return $this;
    }

    public function getReview($filmarksId)
    {

        $apiResult = $this->tapReviewApi($filmarksId);
        $reviews = [
            'totalCount' => 0,
            'averageRating' => 0,
            'rows' => []
        ];

        if (!empty($apiResult) && array_key_exists('entry', $apiResult)) {
            foreach ($apiResult['entry']['movie']['reviews'] as $review) {
                $reviews['rows'][] = [
                    'rating' => floatval(number_format($review['score'], 1)),
                    'contributor' => $review['userName'],
                    'contributeDate' => date('Y-m-d', strtotime($review['createdAt'])),
                    'contents' => $review['review'],
                ];
            }
            if (!empty($reviews['rows'])) {
                $reviews['totalCount'] = $apiResult['entry']['movie']['markCount'];
                $reviews['averageRating'] = floatval(number_format($apiResult['entry']['movie']['averageScore'], 1));
                return $reviews;
            }
        }

        return null;
    }


    public function tapReviewApi($filmarksId)
    {

        $this->apiPath ='/tsutayaappapi/works/fm/review';
       
        $this->apiPath = $this->apiHost . $this->apiPath;
        $this->queryParams = [
            'api_key' => $this->apiKey,
            'filmarksid' => $filmarksId,
            'isReview' => '1',
            'score' => '1',
            'limit' => $this->limit
        ];
        return $this->get();
    }

    public function getCoupon($storeCd, $tokuban, $deliveryId, $deliveryStartDate, $deliveryEndDate)
    {

        $this->apiPath ='/tsutayaappapi/tm/cpn/qr';

        $this->apiPath = $this->apiHost . $this->apiPath;
        $this->queryParams = [
            'api_key' => $this->apiKey,
            'fcid' => $storeCd,
            'tokuid' => $tokuban,
            'sendid' => $deliveryId,
            'validfrom' => $deliveryStartDate,
            'validto' => $deliveryEndDate
        ];
        return $this->get();
    }

    /**
     * TOL会員状態取得
     * @param $tolid
     * @return mixed|null|string
     * @throws \App\Exceptions\NoContentsException
     */
    public function getMemberStatus($tolid)
    {
        $this->api = 'tm/memberStatus';
        $this->id = $tolid;
        $this->apiPath ='/tsutayaappapi/tm/memberStatus';
        $this->apiPath = $this->apiHost . $this->apiPath;
        // パラメーターで指定するとエンコードがかかって変なリクエストになる為
        $this->apiPath = $this->apiPath . '?api_key=' . $this->apiKey;
        $this->apiPath = $this->apiPath . '&tolid=' . $tolid;
        return $this->get();
    }

    /**
     * @param bool $jsonResponse
     * @return mixed|null|string
     * @throws \App\Exceptions\NoContentsException
     */
    public function get($jsonResponse = true)
    {
        if(env('APP_ENV') !== 'local' && env('APP_ENV') !== 'testing' ){
            return parent::get($jsonResponse);
        }
        return $this->stub($this->api, $this->id);
    }

    /**
     * @param $apiName
     * @param $filename
     * @return mixed|null
     */
    private function stub($apiName, $filename)
    {
        $path = base_path('tests/Data/tap/');
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