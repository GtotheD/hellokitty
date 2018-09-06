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

    private function stub($apiName, $filename)
    {

        $path = base_path('tests/');
        $path = $path . $apiName;
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        $file = file_get_contents($path . DIRECTORY_SEPARATOR . $filename);
        return json_decode($file, TRUE);
    }
}