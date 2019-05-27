<?php

class ProductListWithDummyOption extends \Laravel\Lumen\Testing\TestCase
{
    public $basePath = '';

    public function __construct(string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->basePath = env('URL_PATH_PREFIX') . env('API_VERSION');
        // NewFlagが変更されるため、現在時刻を変更
    }

    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }

    /**
     * Test add condition for data response from data of Himo
     */
    public function testProductListWithDummyOption()
    {
        // Test with product has msdb_item = audio
        $url = '/work/PTA0000XZUNO/products';

        /**
         * Load data has saleType=sell and isDummy=1
         */
        $param = [
            'saleType' => 'sell',
            'isDummy' => 'true'
        ];
        $this->json('GET',
            $this->basePath . $url,
            $param, ['HTTP_Authorization' => 'k8AJR0NxM114Ogdl']);
        // If has no data, status will 204
        $actualCode = $this->response->getStatusCode();
        if ($actualCode == 204) {
            $this->assertResponseStatus(204);
        } else
            $this->assertResponseStatus(200);

        /**
         * Load data has saleType=sell and isDummy=0
         */
        $param = [
            'saleType' => 'sell',
            'isDummy' => 'false'
        ];
        $this->json('GET',
            $this->basePath . $url,
            $param, ['HTTP_Authorization' => 'k8AJR0NxM114Ogdl']);
        // If has no data, status will 204
        $actualCode = $this->response->getStatusCode();
        if ($actualCode == 204) {
            $this->assertResponseStatus(204);
        } else
            $this->assertResponseStatus(200);

        /**
         * Load data has saleType=rental and isDummy=1
         */
        $param = [
            'saleType' => 'rental',
            'isDummy' => 'true'
        ];
        $this->json('GET',
            $this->basePath . $url,
            $param, ['HTTP_Authorization' => 'k8AJR0NxM114Ogdl']);
        // If has no data, status will 204
        $actualCode = $this->response->getStatusCode();
        if ($actualCode == 204) {
            $this->assertResponseStatus(204);
        } else
            $this->assertResponseStatus(200);

        /**
         * Load data has saleType=rental and isDummy=0
         */
        $param = [
            'saleType' => 'rental',
            'isDummy' => 'false'
        ];
        $this->json('GET',
            $this->basePath . $url,
            $param, ['HTTP_Authorization' => 'k8AJR0NxM114Ogdl']);
        // If has no data, status will 204
        $actualCode = $this->response->getStatusCode();
        if ($actualCode == 204) {
            $this->assertResponseStatus(204);
        } else
            $this->assertResponseStatus(200);


        // Test with product has msdb_item = video
        $url = '/work/PTA0000V1EBP/products';
        /**
         * Load data has saleType=rental and isDummy=1
         */
        $param = [
            'saleType' => 'rental',
            'isDummy' => 'false'
        ];
        $this->json('GET',
            $this->basePath . $url,
            $param, ['HTTP_Authorization' => 'k8AJR0NxM114Ogdl']);
        // If has no data, status will 204
        $actualCode = $this->response->getStatusCode();
        if ($actualCode == 204) {
            $this->assertResponseStatus(204);
        } else
            $this->assertResponseStatus(200);

        /**
         * Load data has saleType=rental and isDummy=0
         */
        $param = [
            'saleType' => 'sell',
            'isDummy' => 'false'
        ];
        $this->json('GET',
            $this->basePath . $url,
            $param, ['HTTP_Authorization' => 'k8AJR0NxM114Ogdl']);
        // If has no data, status will 204
        $actualCode = $this->response->getStatusCode();
        if ($actualCode == 204) {
            $this->assertResponseStatus(204);
        } else
            $this->assertResponseStatus(200);
    }

}