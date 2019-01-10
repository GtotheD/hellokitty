<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Carbon;

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    private static $isSetup = false;
    var $basePath;
    var $testDir;

    public function __construct(string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->basePath = env('URL_PATH_PREFIX') . env('API_VERSION');
        // NewFlagが変更されるため、現在時刻を変更
        Carbon::setTestNow(new Carbon('2018-10-01 00:00:00'));
    }

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
    }

    public function setUp()
    {
        parent::setUp();
        if (self::$isSetup === false) {
            Artisan::call('migrate');
            Artisan::call('truncateTable');
            Artisan::call('db:seed', ['--class' => 'WorkRecommendOtherTestSeeder']);
            Artisan::call('db:seed', ['--class' => 'ReleaseTestSeeder']);
            Artisan::call('db:seed', ['--class' => 'PointDetailsTestSeeder']);
            self::$isSetup = true;
        }
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function getWithAuth($uri, $param = [])
    {
        return $this->call('GET',
            $this->basePath . $uri,
            $param,
            [],
            [],
            ['HTTP_Authorization' => 'k8AJR0NxM114Ogdl'],
            []
        );
    }

    public function postWithAuth($uri, $json)
    {
        return $this->call(
            'POST',
            $this->basePath . $uri,
            [],
            [],
            [],
            [
                'HTTP_Authorization' => 'k8AJR0NxM114Ogdl',
                'CONTENT_TYPE' => 'application/json'
            ],
            $json
        );
    }

    public function getJsonWithAuth($uri, $param = [])
    {
        return $this->json('GET',
            $this->basePath . $uri,
            $param, ['HTTP_Authorization' => 'k8AJR0NxM114Ogdl']);
    }

    public function postJsonWithAuth($uri, $param = [])
    {
        return $this->json('POST',
            $this->basePath . $uri,
            $param, ['HTTP_Authorization' => 'k8AJR0NxM114Ogdl']);
    }

    public function setIsSetUp($isSetUp = true)
    {
        self::$isSetup = $isSetUp;
    }

    /*
     * 販売種別用テスト
     * 200の時は期待値ファイルとの比較を行う。
     * 200以外の場合はステータスコードの比較を行う。
     */
    public function saleTypeTestCase($workId, $saleType, $responseCode, $actualResponse)
    {
        if ($responseCode === 200) {
            $actual = json_decode($actualResponse->getContent(), true);
            $expected = json_decode(file_get_contents( $this->testDir . '/expected/' . $workId . '_' . $saleType), true);
            unset($expected['data']['createdAt']);
            unset($expected['data']['updatedAt']);
            unset($actual['data']['createdAt']);
            unset($actual['data']['updatedAt']);
            $this->assertEquals($expected, $actual);
        } else {
            $actualStatusCode =$actualResponse->getStatusCode();
            $this->assertEquals($responseCode, $actualStatusCode);
        }
    }
    /*
      * 結果比較テスト
      * 期待値ファイルとの比較を行う。
      */
    public function actualDifference($workId ,$actualResponse)
    {
        $actual = json_decode($actualResponse->getContent(), true);
        $expected = json_decode(file_get_contents( $this->testDir . '/expected/' . $workId), true);
        unset($expected['data']['createdAt']);
        unset($expected['data']['updatedAt']);
        unset($actual['data']['createdAt']);
        unset($actual['data']['updatedAt']);
        $this->assertEquals($expected, $actual);
    }

    /**
     * 全データのインポートを行う。
     */
    public function importAllData()
    {
        $path = base_path('tests/Data/himo/crossworks/');
        $audioList = glob($path . '/audio/*');
        $videoList = glob($path . '/video/*');
        $bookList = glob($path . '/book/*');
        $gameList = glob($path . '/game/*');
        $list = array_merge($audioList, $videoList, $bookList, $gameList);
        foreach ($list as $row) {
            $workId = basename($row);
            $url = '/work/' . $workId;
            $this->getWithAuth($url);
        }
    }

}
