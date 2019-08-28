<?php

use tests\TestData;
use Illuminate\Support\Carbon;

/*
 * Work（作品情報取得） APIテスト
 *
 */
class RecommendTest  extends \Laravel\Lumen\Testing\TestCase
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

    public function testRecommend() {
        $params = [
            'image' => 'newBook.png',
            'imageNew' => 'newBookBadge.png'
        ];

        $expect =[
            'imageUrl' => env('RECOMMEND_IMAGE_HOST') . 'newBook.png',
            'imageWithBadgeUrl' => env('RECOMMEND_IMAGE_HOST') . 'newBookBadge.png'
        ];

        $output = $this->json('GET',
            $this->basePath . '/section/banner/recommend',
            $params,
            ['HTTP_Authorization' => 'k8AJR0NxM114Ogdl']);

        $this->assertResponseStatus(200);
        $this->assertEquals($expect, json_decode($output->response->getContent(), true));
    }
}