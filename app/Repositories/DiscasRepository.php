<?php

namespace App\Repositories;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\Repositories\WorkRepository;
use App\Model\Work;


class DiscasRepository extends ApiRequesterRepository
{

    protected $sort;
    protected $offset;
    protected $limit;
    protected $apiHost;

    const DISCAS_REVIEW_API = '/netdvd/sp/webapi/review/reviewInfo'; // 作品詳細用

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


}
