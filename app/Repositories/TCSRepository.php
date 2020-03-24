<?php
namespace App\Repositories;

use App\Model\Product;

class TCSRepository extends ApiRequesterRepository
{

    private $sort;
    private $offset;
    private $limit;
    private $apiHost;
    private $apiKey;

    const COMICSPACE_REVIEW_API = '/comics';

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        parent::__construct();
        $this->sort = $sort;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->apiHost = env('TCS_API_HOST');
        $this->apiKey = env('TCS_API_KEY');
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function getReview($workId)
    {
        $productModel = new Product();
        $product = $productModel->setConditionByWorkIdNewestProduct($workId, null)->toCamel()->getOne();
        $isbn = null;
        if (isset($product->isbn13) && $product->isbn13 !== '') {
            $isbn = $product->isbn13;
        } elseif (isset($product->isbn10) && $product->isbn10 !== '') {
            $isbn = $product->isbn10;
        } else {
            return;
        }

        $apiResult = $this->tcsReviewApi($isbn);
        $reviews = [
            'totalCount' => 0,
            'averageRating' => 0,
            'rows' => []
        ];

        if (!empty($apiResult) && array_key_exists('comic', $apiResult)) {
            $reviews['totalCount'] = $apiResult['comic']['yondaCount'];

            foreach ($apiResult['comic']['reviews'] as $review) {
                $reviews['rows'][] = [
                    'rating' => floatval(number_format($review['score'], 1)),
                    'contributor' => $review['userName'],
                    'contributeDate' => date('Y-m-d', strtotime($review['createdAt'])),
                    'contents' => $review['review'],
                ];
            }
            if (!empty($reviews['rows'])) {
                $reviews['averageRating'] = floatval(number_format($apiResult['comic']['averageScore'], 1));
                $reviews['rows'] = array_slice($reviews['rows'], 0, $this->limit);
                return $reviews;
            }
        }

        return null;
    }

    public function tcsReviewApi($isbn)
    {
        $this->apiPath = self::COMICSPACE_REVIEW_API;
        $this->apiPath = $this->apiHost . $this->apiPath . DIRECTORY_SEPARATOR . $isbn;
        $this->api = 'review';
        $this->id = $isbn;
        $this->queryParams = [
            'min_review_length' => '1',
            'limit' => $this->limit
        ];
        
        return $this->get();
    }

    /**
     * @param bool $jsonResponse
     * @return mixed|null|string
     * @throws \App\Exceptions\NoContentsException
     */
    public function get($jsonResponse = true)
    {
        if (env('APP_ENV') !== 'local' && env('APP_ENV') !== 'testing') {
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
        $path = base_path('tests/Data/tcs/');
        $path = $path . $apiName;
        if (!realpath($path . '/' . $filename)) {
            return null;
        }
        $file = file_get_contents($path . '/' . $filename);

        // Remove new line character
        return json_decode(str_replace(["\n","\r\n","\r", PHP_EOL], '', $file), true);
    }
}
