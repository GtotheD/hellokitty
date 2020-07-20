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

        /**
         * Common true
         */
        // Just date
        $result = $repo->validateDate('2020-01-01'); // date min
        $this->assertEquals(true, $result);
        $result = $repo->validateDate('2020-06-15'); // date middle
        $this->assertEquals(true, $result);
        $result = $repo->validateDate('2020-12-31'); // date max
        $this->assertEquals(true, $result);

        // Date and time
        $result = $repo->validateDate('2020-01-01 00:00:00'); // time min
        $this->assertEquals(true, $result);
        $result = $repo->validateDate('2020-06-25 12:30:45'); // time min
        $this->assertEquals(true, $result);
        $result = $repo->validateDate('2020-12-31 23:59:59'); // time max
        $this->assertEquals(true, $result);


        /**
         * Common fail
         */
        $result = $repo->validateDate('2020-01-00');
        $this->assertEquals(false, $result);
        $result = $repo->validateDate('2020-00-01');
        $this->assertEquals(false, $result);
        $result = $repo->validateDate('2020-12-32');
        $this->assertEquals(false, $result);
        $result = $repo->validateDate('2020-13-31');
        $this->assertEquals(false, $result);
        $result = $repo->validateDate('20-04-09');
        $this->assertEquals(false, $result);
        $result = $repo->validateDate('2020-4-9');
        $this->assertEquals(false, $result);
        $result = $repo->validateDate('2020-14-09');
        $this->assertEquals(false, $result);
        $result = $repo->validateDate('2020-04-09 12:5:16');
        $this->assertEquals(false, $result);
        $result = $repo->validateDate('2020-04-09 26:05:16');
        $this->assertEquals(false, $result);
        $result = $repo->validateDate('2020-04-09 16:65:16');
        $this->assertEquals(false, $result);
        $result = $repo->validateDate('2020-04-09 16:25:66');
        $this->assertEquals(false, $result);
        $result = $repo->validateDate('0000-01-02 16:25:15');
        $this->assertEquals(false, $result);
    }
}
