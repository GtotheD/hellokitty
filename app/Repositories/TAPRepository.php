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
                    'rating' => number_format($review['score'], 1),
                    'contributor' => $review['userName'],
                    'contributeDate' => date('Y-m-d', strtotime($review['createdAt'])),
                    'contents' => $review['review'],
                ];
                $reviews['totalCount']++;
            }
            if (!empty($reviews)) {
                $reviews['averageRating'] = number_format($apiResult['entry']['movie']['averageScore'], 1);
                return $reviews;
            }
        }

        return null;
    }


    public function tapReviewApi($filmarksId)
    {

        $this->apiPath ='/tsutayaappapi/works/fm/review';
        //local data
        if (env('APP_ENV') == 'local') {
            return $this->stub($this->apiPath, $filmarksId);
        }
        $this->apiPath = $this->apiHost . $this->apiPath;
        $this->params = [
            'api_key' => $this->apiKey,
            'filmarksid' => $filmarksId,
            'isReview' => '1',
            'score' => '1'
        ];
        Log::debug("tapReviewApi() params[" . $filmarksId . "][1][1] url[" . $this->apiPath . "]");
        $client = new Client();
        try {
            $result = $client->request(
                'GET',
                $this->apiPath,
                ['query' => $this->params]
            );
        } catch (Exception $e) {
            Log::warn("tapReviewApi() statusCode[" . $e->getResponse()->getStatusCode() . "]");
            return null;
        }

        Log::info("tapReviewApi() statusCode[" . $result->getStatusCode() . "]");
        return json_decode($result->getBody()->getContents(), true);
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