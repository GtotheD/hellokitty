<?php

class ChangeNotificationStatus extends \Laravel\Lumen\Testing\TestCase
{
    public $basePath = '';

    public function __construct(string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->basePath = env('URL_PATH_PREFIX') . env('API_VERSION');
    }

    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }

    /**
     * Get status of receive notification
     */
    public function testChangeNotificationStatus()
    {
        $params = [
            'tolId' => '9v9W6VweqVmiIK2PyIx50%2FGEpF%2BOYv7wkTIdfk0qJlc%3D',
            'isRegistered ' => false
        ];
        $this->json('POST',
            $this->basePath . '/member/status/arrival/notification/update',
            $params,
            ['HTTP_Authorization' => 'k8AJR0NxM114Ogdl']);
        $this->assertResponseStatus(200);
    }

}