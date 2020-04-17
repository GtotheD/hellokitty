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

    public function testValidateDate() {
        $repo = new \App\Repositories\MaintenanceRepository();

        $result = $repo->validateDate('2020-04-09');
        $this->assertEquals(true, $result);
        $result = $repo->validateDate('2020-04-09 12:45:16');
        $this->assertEquals(true, $result);

        $result = $repo->validateDate('2020-4-9');
        $this->assertEquals(false, $result);
        $result = $repo->validateDate('2020-14-9');
        $this->assertEquals(false, $result);

        $result = $repo->validateDate('2020-04-09 12:5:16');
        $this->assertEquals(false, $result);
        $result = $repo->validateDate('2020-04-09 26:05:16');
        $this->assertEquals(false, $result);
        $result = $repo->validateDate('2020-04-09 16:65:16');
        $this->assertEquals(false, $result);
        $result = $repo->validateDate('2020-04-09 16:25:66');
        $this->assertEquals(false, $result);
    }
}
