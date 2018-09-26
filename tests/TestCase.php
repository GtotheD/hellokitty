<?php
use Illuminate\Support\Facades\Artisan;
use tests\TestData;

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    private static $isSetup = false;

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

    public function init()
    {
        parent::setUp();
        if(self::$isSetup === false){
            Artisan::call('migrate');
            $testData = new TestData;
            $testData->jsonInitialize();
            self::$isSetup = true;
        }
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function getWithAuth($apiPath, $param = [])
    {
        return $this->call('GET',
            env('URL_PATH_PREFIX') . env('API_VERSION') . $apiPath,
            $param, [], [], ['HTTP_Authorization' => 'k8AJR0NxM114Ogdl'], []
        );
    }

    public function getJsonWithAuth($uri, $param = [])
    {
        return $this->json('GET', $uri,
            $param, ['HTTP_Authorization' => 'k8AJR0NxM114Ogdl']);
    }

    public function postJsonWithAuth($uri, $param = [])
    {
        return $this->json('POST', $uri,
            $param, ['HTTP_Authorization' => 'k8AJR0NxM114Ogdl']);
    }

    public function setIsSetUp($isSetUp = true) {
        self::$isSetup = true;
    }
}
