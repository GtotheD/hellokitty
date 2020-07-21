<?php

namespace App\Repositories;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\ClientException;

class MintRepository extends ApiRequesterRepository
{

    protected $sort;
    protected $offset;
    protected $limit;
    protected $apiHost;
    protected $apiNewHost;

    const MINT_API = '/api/Cwar2010'; // 作品詳細用

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        parent::__construct();
        $this->apiHost = env('MINT_API_HOST');
    }

    public function authKey($tInternalNumber)
    {
        $userAgent = 'GuzzleHttp/6.2.0 curl/7.29.0 PHP/7.0.14';
        $this->apiPath = $this->apiHost . self::MINT_API;
        
        $request = [
            'AuthId' => env('AUTH_ID'),
            'AuthPass' => env('AUTH_PASS'),
            'SqlDiscNumber' => env('SQL_DISC_NUMBER'),
            'RequestType' => env('REQUEST_TYPE'),
            'SqlValueList' => ["'" . $tInternalNumber . "'"]
        ];
        $this->queryParams = json_encode($request);
        return $this->postBody(true);
    }

    /**
     * Get tenpo plan information
     *
     * @param $storeCode
     * @param $flatPlanNumber
     * @return string
     * @throws \App\Exceptions\NoContentsException
     */
    public function getTenpoPlanInfo($storeCode, $flatPlanNumber)
    {
        $this->apiPath = $this->apiHost . self::MINT_API;

        $request = [
            'AuthId' => env('AUTH_ID'),
            'AuthPass' => env('AUTH_PASS'),
            'SqlDiscNumber' => env('SQL_DISC_NUMBER_FEE'),
            'RequestType' => env('REQUEST_TYPE'),
            'SqlValueList' => ["'".$storeCode."'", "'".$flatPlanNumber."'"]
        ];

        $this->queryParams = json_encode($request);
        return $this->postBody(true);
    }

}
