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
    protected $memId;
    protected $tolApiHost;
    protected $testTolApiPath;

    const TEST_API_PATH = 'tests/Data/tol';
    const MMC200 = '/ms/resources/ap09mmc200';
    const MMC208 = '/ms/resources/ap08mmc208';
    const MFR001 = '/ms/resources/ap10mfr001';
    const MRE001 = '/ms/resources/ap07mre001';

    /**
     * TolClient constructor.
     * @param $memId
     */
    public function __construct($memId)
    {
        parent::__construct();
        $this->memId = $memId;
        $this->tolApiHost = env('TOL_API_HOST');
        $this->testTolApiPath = base_path(self::TEST_API_PATH);
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
    public function getRentalApplication()
    {
        $this->apiPath = $this->createPath(self::MRE001);
        $this->queryParams = [
            'memid' => $this->memId
        ];
        $this->setMethod('POST');
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