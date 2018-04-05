<?php

namespace App\Repositories;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\Exceptions\NoContentsException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/13
 * Time: 15:01
 */
class ApiRequesterRepository
{

    protected $apiPath;
    protected $queryParams;
    protected $headers = [];
    public function __construct()
    {
    }

    /*
     * 取得の実行
     */
    public function get()
    {
        $url = $this->apiPath;
        $client = new Client();
        try {
            $result = $client->request(
                'GET',
                $url,
                [
                    'query' => $this->queryParams,
                    'headers' => $this->headers,
                ]
            );
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            if ($statusCode == '404') {
                throw new NotFoundHttpException();
            }
            throw new $e;
        }
        return json_decode($result->getBody()->getContents(), true);
    }

    public function getRaw()
    {
        $url = $this->apiPath;
        $client = new Client();
        try {
            $result = $client->request(
                'GET',
                $url,
                [
                    'query' => $this->queryParams,
                    'headers' => $this->headers,
                ]
            );
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            if ($statusCode == '404') {
                throw new NotFoundHttpException();
            }
            throw new $e;
        }
        return $result;
    }

    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    public function setHeaders($params)
    {
        foreach ($params as $key => $value) {
            $this->headers[$key] = $value;
        }
    }
}