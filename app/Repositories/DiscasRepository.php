<?php

namespace App\Repositories;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\ClientException;
use App\Libraries\TlscEncryption;
use App\Model\TolFlatRentalOperation;
use \App\Libraries\Security;
use function GuzzleHttp\Psr7\str;

class DiscasRepository extends ApiRequesterRepository
{
    protected $sort;
    protected $offset;
    protected $limit;
    protected $apiHost;
    protected $apiTWSHost;
    protected $apiMINTHost;
    protected $apiNewHost;
    protected $apiTTVHost;
    protected $apiTWSKey;
    private $tolId;
    private $memId;
    private $tolKey;
    private $nowDate;

    const DISCAS_REVIEW_API = '/netdvd/sp/webapi/review/reviewInfo'; // 作品詳細用
    const DISCAS_CUSTOMER_API = '/v2/customer'; // 作品詳細用
    const HASDIS_CUSTOMER_API = '/v2/customer/hasDisc'; // 会員情報の拡充 API
    const TTV_RECOMMEND_API = '/web/v1/vod/recommend?recommend_type=1';
    const TTV_CONTENTS_API = '/web/v1/vod/contents?content_title_id=';
    const MINT_API = '/api/Cwar2010'; // 作品詳細用


    use TlscEncryption, Security {
        Security::encrypt insteadof TlscEncryption;
    }

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        parent::__construct();
        $this->sort = $sort;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->apiHost = env('DISCAS_API_HOST');
        $this->apiNewHost = env('DISCAS_NEW_API_HOST');
        $this->apiTTVHost = env('TTV_API_HOST');
        $this->tolKey = env('TOL_ENCRYPT_KEY');
        $this->apiTWSHost = env('TWS_API_HOST');
        $this->apiTWSKey = env('TWS_API_KEY');
        $this->apiMINTHost = env('MINT_API_HOST');
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

    public function setTolId($tolId)
    {
        $this->tolId = $tolId;
    }

    public function setNowDate($nowDate = null)
    {
        $this->nowDate = $nowDate ? $nowDate : date('Y-m-d');
    }

    public function getNowDate()
    {
        return $this->nowDate;
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

    public function customer($tlsc)
    {
        $userAgent = 'GuzzleHttp/6.2.0 curl/7.29.0 PHP/7.0.14';
        $this->apiPath = $this->apiNewHost . self::DISCAS_CUSTOMER_API;

        /**
         * Add process for convert loginToken from Tlsc in local
         */
        if(env('APP_ENV') !== 'local' && env('APP_ENV') !== 'testing' ){
            $lv2Token = $this->getLv2LoginTokenFromTlsc($tlsc);
        } else {
            $lv2Token = $tlsc;
        }
        $this->id = $lv2Token;

        $this->setHeaders([
            'User-Agent' => $userAgent,
    	    'host' => parse_url($this->apiNewHost, PHP_URL_HOST),
            'X-Requested-With' => 'XMLHttpRequest',
            'content-type' => 'application/json; charset=utf-8',
    	    'cookie' => 'lv2LoginTkn=' . $lv2Token
        ]);
        return $this;
    }

    /**
     * In case API include tolId data
     * @param $response
     * @return mixed
     * @throws \App\Exceptions\NoContentsException
     */
    public function processTtvWithTolid($response)
    {
        /**
         * Process response data if has tolId
         */
        if (env('APP_ENV') !== 'local' && env('APP_ENV') !== 'testing') {
            $this->memId = $this->decodeMemid($this->tolKey, $this->tolId);
        } else {
            $this->memId = $this->tolId;
        }

        // 定額レンタル操作 mfr001
        $tolFlatRentalOperationModel = new TolFlatRentalOperation($this->memId);
        $tolFlatRentalOperationCollection = $tolFlatRentalOperationModel->getDetail();
        if (empty($tolFlatRentalOperationCollection)) {
            return $response;
        }

        // Get data by tolid success, so change http code and unset status if has
        $response['httpcode'] = '200';
        if (isset($response['status'])) {
            unset($response['status']);
        }

        // Check ttvid
        if (!isset($response['ttvid'])) {
            $response['ttvid'] = '';
        }

        $tolFlatRentalOperation = current($tolFlatRentalOperationCollection->all());

        if (isset($tolFlatRentalOperation['storeCode'])) {
            $response['tenpoCode'] = $tolFlatRentalOperation['storeCode'];

            // Get storeName by request to API (tenpoName)
            $response['tenpoName'] = $this->getStoreName($tolFlatRentalOperation['storeCode']);

            // Get tenpoPlanFee by request to API
            if (isset($tolFlatRentalOperation['flatPlanNumber'])) {
                if (env('APP_ENV') == 'local' || env('APP_ENV') == 'testing') {
                    $response['tenpoPlanFee'] = 1100; // Example data
                } else {
                    $response['tenpoPlanFee'] = $this->getTenpoPlanFee($tolFlatRentalOperation['storeCode'],
                        $tolFlatRentalOperation['flatPlanNumber']);
                }
            }
        }

        // Process for nextUpdateDate
        if (isset($tolFlatRentalOperation['flatPlanRegistrationDate'])) {
            $response['nextUpdateDate'] = date('Y-m-d h:i:s',
                strtotime($this->getNextUpdateDate($tolFlatRentalOperation['flatPlanRegistrationDate'])));
        }
        return $response;
    }

    /**
     * Process store name
     * @param $storeCode
     * @return string
     * @throws \App\Exceptions\NoContentsException
     */
    private function getStoreName($storeCode)
    {
        $this->setMethod('GET');
        $data = $this->storeDetail($storeCode)->get();
        return isset($data['entry']['storeName']) ? $data['entry']['storeName'] : '';
    }

    /**
     * Get store detail
     * @param $storeCode
     * @return $this
     */
    public function storeDetail($storeCode)
    {
        // For stub
        if (env('APP_ENV') === 'local' || env('APP_ENV') === 'testing') {
            $this->api = '/store/v0/store/';
            $this->id = $this->tolId;
            return $this;
        }
        // ---------

        $this->apiPath = $this->apiTWSHost . '/store/v0/products/detail.json';
        $this->queryParams = [
            'api_key' => $this->apiTWSKey,
            'tolPlatformCode' => '00',
            'storeId' => (string)$storeCode,
        ];
        return $this;
    }

    /**
     * Process fee
     * @param $storeCode
     * @param $flatPlanNumber
     * @return float|int
     */
    private function getTenpoPlanFee($storeCode, $flatPlanNumber)
    {
        $data = $this->authKey([$storeCode, $flatPlanNumber]);
        return isset($data['ResultList'][0][3]) ? (float)$data['ResultList'][0][3] : 0;
    }

    private function authKey($sqlValueList = [])
    {
        $this->apiPath = $this->apiHost . self::MINT_API;

        $request = [
            'AuthPass' => env('AUTH_PASS'),
            'SqlDiscNumber' => '7080000005',
            'RequestType' => '00',
            'SqlValueList' => $sqlValueList
        ];
        $this->queryParams = json_encode($request);
        return $this->postBody(true);
    }

    /**
     * Process next update date
     * @param $flatPlanRegistrationDate
     * @return false|string
     */
    public function getNextUpdateDate($flatPlanRegistrationDate)
    {
        $nextUpdateDate = '';
        if (is_null($this->nowDate)) {
            $nowDate = date('Y-m-d');
        } else {
            $nowDate = date('Y-m-d', strtotime($this->nowDate));
        }

        //1. 本日を取得
        if ($flatPlanRegistrationDate === '') {
            return $nextUpdateDate;
        }

        //2. 定額プラン登録日を取得
        $regDay = date('d', strtotime($flatPlanRegistrationDate));
        //3. 今月末を取得
        $lastDate = date('Y-m-d', strtotime('last day of ' . $nowDate));
        $lastDay = date('d', strtotime('last day of ' . $nowDate));
        //4. 来月を取得
        $nextDate = date('Y-m-t', strtotime(date('Y-m-01', strtotime($nowDate)) . '+1 month'));
        $nextDay = date('t', strtotime(date('Y-m-01', strtotime($nowDate)) . '+1 month'));
        //5. 登録日（日付）と、末日を比較. 暫定次回更新日を作成する
        if (intval($regDay) <= intval($lastDay)) {
            $tmpNextUpdate = date('Y-m-' . $regDay);
        } else {
            $tmpNextUpdate = $lastDate;
        }
        //6. 暫定次回更新日と、本日を比較. 次回更新日を作成する
        if (strtotime($tmpNextUpdate) > strtotime($nowDate)) {
            //6-1. 暫定次回更新日が未来
            $nextUpdateDate = $tmpNextUpdate;
        } else {
            //6-2. 暫定次回更新日が本日または過去
            //6-2-1. 来月の末日と登録日を比較
            if ($regDay <= $nextDay) {
                $nextUpdateDate = date('Y-m-' . $regDay, strtotime('+1 month'));
            } else {
                $nextUpdateDate = $nextDate;
            }
        }
        return $nextUpdateDate;
    }

    public function customerRental($tlsc)
    {
        $userAgent = 'GuzzleHttp/6.2.0 curl/7.29.0 PHP/7.0.14';
        $this->apiPath = $this->apiNewHost . self::HASDIS_CUSTOMER_API;

        /**
         * Add process for convert loginToken from Tlsc in local
         */
        if(env('APP_ENV') !== 'local' && env('APP_ENV') !== 'testing' ){
            $lv2Token = $this->getLv2LoginTokenFromTlsc($tlsc);
        } else {
            $lv2Token = $tlsc;
        }
        $this->id = $lv2Token;

        $this->setHeaders([
            'User-Agent' => $userAgent,
            'host' => parse_url($this->apiNewHost, PHP_URL_HOST),
            'X-Requested-With' => 'XMLHttpRequest',
            'content-type' => 'application/json; charset=utf-8',
            'cookie' => 'lv2LoginTkn=' . $lv2Token
        ]);
        return $this;
    }

    //ttvで編成している「TSUTAYAプレミアムのおすすめ」を取得する
    public function ttvRecommendList()
    {
      $userAgent = 'GuzzleHttp/6.2.0 curl/7.29.0 PHP/7.0.14';
      $this->apiPath = $this->apiTTVHost . self::TTV_RECOMMEND_API;

      return $this;
    }

    //ttv作品・商品データを取得する
    public function getTTVContents($ttvContentsCd)
    {
        $userAgent = 'GuzzleHttp/6.2.0 curl/7.29.0 PHP/7.0.14';
        $this->apiPath = $this->apiTTVHost . self::TTV_CONTENTS_API . $ttvContentsCd;

        return $this;
    }

    public function getLv2LoginTokenFromTlsc($tlsc)
    {
        $tlscConfig = config('tlsc_encryption');
        $this->convertKeys = $tlscConfig['convert_keys'];
        $this->checkDegitWeight = $tlscConfig['check_degit_weight'];

        // envファイルから環境毎の値を取得
        $key = env('LV2TOKEN_ENCRYPT_KEY');
        $iv = env('LV2TOKEN_INIT_VECTOR');

        // traitにて実装されているファンクションにてST内部管理番号を取得
        $stId = $this->decrypt($tlsc);

        //2. ST内部管理番号＋現在時刻の120分後の値を作成 (＝レベル２認証トークン)
        $now = date('YmdHis', strtotime('+120 minute'));
        $value = $stId . $now;

        //3. レベル2認証トークンの暗号化キー、初期化ベクトルを用意
        $hashed = md5($key, true);
        $generatedKey = $hashed . md5($hashed, true);

        //4. レベル2認証トークンを暗号化 AES-256-CBC > base64×2
        return base64_encode(base64_encode(openssl_encrypt($value, 'AES-256-CBC', $generatedKey, true, $iv)));
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
