<?php
/**
 * Created by PhpStorm.
 * User: usuda
 * Date: 2018/11/29
 * Time: 15:53
 */

namespace App\Clients;

/**
 * Class TolClient
 * @package App\Clients
 */
class TolClient extends BaseClient
{
    protected $key;
    protected $memId;
    protected $tolApiHost;
    protected $testTolApiPath;

    const TEST_API_PATH = 'tests/Data/tol';
    const MMC200 = '/ms/resources/ap09mmc200';
    const MMC208 = '/ms/resources/ap08mmc208';
    const MFR001 = '/ms/resources/ap10mfr001';
    const MRE001 = '/ms/resources/ap07mre001';
    const SP101 = '/ms/resources/ap11sp101';
    const PIG01 = '/ms/resources/AP14PushInfoGet01';
    const PIP01 = '/ms/resources/AP14PushInfoPost01';
    const NTF001 = '/ms/resources/MA13Reservation01P/get';
    const NTF002 = '/ms/resources/MA13Reservation02P/post';
    const MCE001 = '/ms/resources/AP13CampaignEntryCountGet01';
    const MCA001 = '/ms/resources/AP12CampaignAnswerPost01';


    /**
     * TolClient constructor.
     * @param $memId
     */
    public function __construct($memId)
    {
        parent::__construct();
        $this->tolApiHost = env('TOL_API_HOST');
        $this->testTolApiPath = base_path(self::TEST_API_PATH);
        $this->memId = $memId;
    }

    /**
     * @return mixed|string
     * @throws \App\Exceptions\NoContentsException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getMemberDetail()
    {
        $this->apiPath = $this->createPath(self::MMC200);
        $this->queryParams = [
            'memid' => $this->memId
        ];
        return $this->get(false);
    }

    /**
     * @return mixed|string
     * @throws \App\Exceptions\NoContentsException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCMemberList()
    {
        $this->apiPath = $this->createPath(self::MMC208);
        $this->queryParams = [
            'memid' => $this->memId
        ];
        return $this->get(false);
    }

    /**
     * @return mixed|string
     * @throws \App\Exceptions\NoContentsException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getFlatRentalOperation()
    {
        $this->apiPath = $this->createPath(self::MFR001);
        $this->queryParams = [
            'memid' => $this->memId
        ];
        return $this->get(false);
    }

    /**
     * @return mixed|string
     * @throws \App\Exceptions\NoContentsException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getNotification()
    {
        $this->apiPath = $this->createPath(self::NTF001);
        $this->setHeaders([
            'tolid' => $this->memId,
            'X-TOL-Platform-Code' => '00'
        ]);
        return $this->get(false);
    }

    /**
     * @param int $chkReservation
     * @return mixed|string
     * @throws \App\Exceptions\NoContentsException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateNotification($chkReservation = 0)
    {
        $this->setMethod('POST');
        $this->apiPath = $this->createPath(self::NTF002);
        $this->setHeaders([
            'tolid' => $this->memId,
            'X-TOL-Platform-Code' => '00'
        ]);
        $this->queryParams = [
            'chkReservation' => $chkReservation
        ];
        return $this->get(false);
    }

    /**
     * @return mixed|string
     * @throws \App\Exceptions\NoContentsException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRentalApplication()
    {
        $this->apiPath = $this->createPath(self::MRE001);
        $this->queryParams = [
            'syorikbn' => 2,
            'memid' => $this->memId
        ];
        $this->setMethod('POST');
        return $this->get(false);
    }

    /**
     * @param $shopCode
     * @return mixed|string
     * @throws \App\Exceptions\NoContentsException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPoint($shopCode)
    {
        $this->apiPath = $this->createPath(self::SP101);
        $this->queryParams = [
            'tenpoCd' => $shopCode,
            'memid' => $this->memId
        ];
        return $this->get(false);
    }

    /**
     * @return mixed|string
     * @throws \App\Exceptions\NoContentsException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPromotionStatus($promotionId)
    {
        $this->apiPath = $this->createPath(self::MCE001);
        $this->queryParams = [
            'memId' => $this->memId,
            'campId' => $promotionId,
        ];
        $this->setMethod('POST');
        return $this->get(false);
    }

    /**
     * @return mixed|string
     * @throws \App\Exceptions\NoContentsException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function registPromotion($params)
    {
        $this->apiPath = $this->createPath(self::MCA001);
        $this->setHeaders([
            'memid' => $this->memId,
            'X-TOL-Platform-Code' => '00'
        ]);
        $this->queryParams = $params;
        $this->setMethod('POST');
        return $this->get(false);
    }

    /**
     * @return mixed|string
     * @throws \App\Exceptions\NoContentsException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPushNotification()
    {
        $this->apiPath = $this->createPath(self::PIG01);
        $this->setHeaders([
            'memid' => $this->memId,
            'X-TOL-Platform-Code' => '00'
        ]);
        return $this->get(false);
    }

    /**
     * @return mixed|string
     * @throws \App\Exceptions\NoContentsException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function registPushNotification($params)
    {
        $this->apiPath = $this->createPath(self::PIP01);
        $this->setHeaders([
            'memid' => $this->memId,
            'X-TOL-Platform-Code' => '00'
        ]);
        $this->queryParams = $params;
        return $this->get(false);
    }

    /**
     * @param $api
     * @return string
     */
    private function createPath($api)
    {
        if (env('APP_ENV') === 'local' || env('APP_ENV') === 'testing') {
            return $this->testTolApiPath . $api . DIRECTORY_SEPARATOR . $this->memId;
        } else {
            return $this->tolApiHost . $api;
        }
    }
}
