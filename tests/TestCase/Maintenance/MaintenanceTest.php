<?php

class MaintenanceTest extends \Laravel\Lumen\Testing\TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }

    /**
     * Test data response of maintenance
     */
    public function testMaintenanceResponse()
    {
        $data = [
            "text" => "いつもTSUTAYAアプリをご利用いただきありがとうございます。",
            "endDate" => "2020/12/31 23:00:00",
            "button" => [
                "text" => "利用制限中のアプリを利用する",
                "link" => "tsutayaapp://sample?sample=true"
            ]
        ];
        $path = 'tests' . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . 'maintenance' . DIRECTORY_SEPARATOR . 'maintenance.json';
        $repo = new \App\Repositories\MaintenanceRepository($path);
        $result = $repo->loadMaintenanceData();

        $this->assertEquals($data, $result);
    }

    /**
     * This function use to test valid for datetime
     * Valid format is YYYY-MM-DD HH:II:SS or YYYY-MM-DD
     */
    public function testValidateDate()
    {
        $repo = new \App\Repositories\MaintenanceRepository();

        // Just date
        $actual = $this->invokeMethod(
            $repo,
            'validate',
            [
                [
                    'dispStartDate' => '2020-01-01',
                    'dispEndDate' => '2040-05-01'
                ]
            ]
        );
        $this->assertEquals(true, $actual);

        // Mix date and time
        $actual = $this->invokeMethod(
            $repo,
            'validate',
            [
                [
                    'dispStartDate' => '2020-01-01 15:08:45',
                    'dispEndDate' => '2040-05-01'
                ]
            ]
        );
        $this->assertEquals(true, $actual);

        // Just date and time
        $actual = $this->invokeMethod(
            $repo,
            'validate',
            [
                [
                    'dispStartDate' => '2020-01-01 15:08:45',
                    'dispEndDate' => '2040-05-01 10:25:41'
                ]
            ]
        );
        $this->assertEquals(true, $actual);

        // Datetime invalid
        $actual = $this->invokeMethod(
            $repo,
            'validate',
            [
                [
                    'dispStartDate' => '0000-00-00 00:00:00',
                    'dispEndDate' => '0000-01-01'
                ]
            ]
        );
        $this->assertEquals(false, $actual);
    }

    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
