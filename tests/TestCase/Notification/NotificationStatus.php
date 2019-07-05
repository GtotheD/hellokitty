<?php

class NotificationStatus extends \Laravel\Lumen\Testing\TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }

    /**
     * @throws \App\Exceptions\NoContentsException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testGetNotificationStatus()
    {
        $tolId = "9v9W6VweqVmiIK2PyIx50%2FGEpF%2BOYv7wkTIdfk0qJlc%3D";
        $NotificationStatus = new \App\Repositories\NotificationRepository($tolId);
        $result = (array)$NotificationStatus->getNotificationStatus();
        $result['isRegistered'] = $result['registerStatus'] === '1';
        unset($result['registerStatus']);
        $output =["status"=>"SUCCESS","isRegistered"=>true];

        $this->assertEquals($output, $result);
    }

}