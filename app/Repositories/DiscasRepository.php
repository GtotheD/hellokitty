<?php

namespace App\Repositories;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\ClientException;

class DiscasRepository extends ApiRequesterRepository
{

    protected $sort;
    protected $offset;
    protected $limit;
    protected $apiHost;

    const DISCAS_REVIEW_API = '/netdvd/sp/webapi/review/reviewInfo'; // 作品詳細用
    const DISCAS_CUSTOMER_API = '/v2/customer'; // 作品詳細用

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        parent::__construct();
        $this->sort = $sort;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->apiHost = env('DISCAS_API_HOST');
    }

    /*
     *  setter
     *
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

    }

    public function setOffset($offset)
    {
        if (isset($offset)) {
            $this->offset = $offset;
        }
    }

    public function setLimit($limit)
    {
        if (isset($limit)) {
            $this->limit = $limit;
        }
    }

    public function getReview($cccProductId)
    {
        $reviews = null;
        $params = [
            'order' => 1,
            'pageNo' => 1,
            'titleID' => $cccProductId,
            'diskKind' => 0
        ];

        $reviews = [
            'totalCount' => 0,
            'averageRating' => 0,
            'rows' => []
        ];

        $xmlObj = $this->discasReviewAPI($params);
        if (!empty($xmlObj)) {
            foreach ($xmlObj->reviewList as $value) {
                $reviews['rows'][] = [
                    'rating' => floatval(number_format($value->reviewRating / 100, 1)),
                    'contributor' => (string)$value->handle,
                    'contributorDate' => '',
                    'contents' => (string)$value->review,
                ];
                $reviews['totalCount']++;
            }
            if (!empty($reviews['rows'])) {
                $reviews['averageRating'] = floatval(number_format($xmlObj->rating / 100, 1));
                $reviews['rows'] = array_slice($reviews['rows'], $this->offset, $this->limit);
                return $reviews;
            }
        }

        return null;
    }


    public function discasReviewAPI($params)
    {
        $this->apiPath = $this->apiHost . self::DISCAS_REVIEW_API;
        $this->queryParams = $params;

        $key = 'd$kpB#vQrrbYmpLc';
        $iv = '&qQ%4XY&&qQ%4XY&';
        $message = date('YmdHis');
        $encrypted = openssl_encrypt($message, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);

        $userAgent = 'jieri fang ti/1.0.0 (iPhone; iOS 10.2; Scale/3.00) ;DISCAS/6.0';

        $this->setHeaders([
            'User-Agent' => $userAgent,
            'at' => urlencode(base64_encode($encrypted))
        ]);


        $contents = $this->get(false);

        try {
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($contents);
            if (!$xml) {
                return null;
            } else {
                return $xml;
            }
        } catch (ErrorException $e) {
            return null;
        }
    }

    public function customer($lv2Token)
    {
        $this->apiPath = $this->apiHost . self::DISCAS_CUSTOMER_API;
        $this->id = $lv2Token;
        $cookieJar = CookieJar::fromArray([
            'lv2LoginTkn' => $lv2Token
        ], $this->apiHost);
        $this->queryParams = [
            'cookies' => $cookieJar
        ];
        return $this;
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
        throw new ClientException();
        $path = base_path('tests/Data/discas');
        $path = $path . $apiName . '/' . $filename;
        if(!realpath($path)) {
            return null;
        }
        $file = file_get_contents($path);
        // Remove new line character
        return json_decode(str_replace(["\n","\r\n","\r", PHP_EOL], '', $file), true);
        // return json_decode($file, TRUE);
    }

}
