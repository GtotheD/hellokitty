<?php
use Illuminate\Support\Facades\Artisan;
use tests\TestData;

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
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
        Artisan::call('migrate');
        $testData = new TestData;
        $testData->jsonInitialize();
    }

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }
}
