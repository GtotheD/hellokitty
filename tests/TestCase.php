<?php
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    private static $isSetup = false;
    var $basePath;

    public function __construct(string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->basePath = env('URL_PATH_PREFIX') . env('API_VERSION');
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
        if(self::$isSetup === false){
            Artisan::call('migrate');
            Artisan::call('truncateTable');
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
            $param, [], [], ['HTTP_Authorization' => 'k8AJR0NxM114Ogdl'], []
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

    public function setIsSetUp($isSetUp = true) {
        self::$isSetup = true;
    }
}
