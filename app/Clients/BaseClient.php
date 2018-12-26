<?php
/**
 * Created by PhpStorm.
 * User: usuda
 * Date: 2018/11/29
 * Time: 15:53
 */

namespace App\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\Exceptions\NoContentsException;

/**
 * Class BaseClient
 * @package App\Clients
 */
class BaseClient
{
    protected $apiPath;
    protected $queryParams;
    protected $method;
    protected $headers = [];

    protected $stubPath;

    /**
     * BaseClient constructor.
     */
    public function __construct()
    {
        $this->method = 'GET';
    }

    /**
     * @param $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * GET
     * @param bool $jsonResponse
     * @return mixed|string
     * @throws NoContentsException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($jsonResponse = true)
    {
        // 環境によってスタブで取得するかどうかをきめる
        if (env('APP_ENV') === 'local' || env('APP_ENV') === 'testing') {
            return $this->getLocal($this->apiPath, $jsonResponse);
        }

        $url = $this->apiPath;
        $client = new Client();
        if ($this->method === 'POST') {
            $requestParamName = 'form_params';
        } else {
            $requestParamName = 'query';
        }
        try {
            $result = $client->request(
                $this->method,
                $url,
                [
                    $requestParamName => $this->queryParams,
                    'headers' => $this->headers,
                ]
            );
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            if ($statusCode == '404') {
                throw new NoContentsException();
            }
            throw new $e;
        }
        if ($jsonResponse) {
            return json_decode($result->getBody()->getContents(), true);
        }
        return $result->getBody()->getContents();
    }


    /**
     * POST
     * @param bool $jsonResponse
     * @return mixed|string
     * @throws NoContentsException
     */
    public function postBody($jsonResponse = true)
    {
        $client = new Client([
            'headers' => [ 'Content-Type' => 'application/json' ]
        ]);
        try {
            $result = $client->post($this->apiPath,[
                'body'  => $this->queryParams
            ]);
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            if ($statusCode == '404') {
                throw new NoContentsException();
            }
            throw new $e;
        }
        if ($jsonResponse) {
            return json_decode($result->getBody()->getContents(), true);
        }
        return $result->getBody()->getContents();
    }

    /**
     * @param $key
     * @param $value
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * @param $params
     */
    public function setHeaders($params)
    {
        foreach ($params as $key => $value) {
            $this->headers[$key] = $value;
        }
    }

    /**
     * スタブ用
     * @param $path
     * @param bool $jsonResponseType
     * @return mixed
     */
    private function getLocal($path, $jsonResponseType = true)
    {
        if (!realpath($path)) {
            return null;
        }
        $file = file_get_contents($path);
        if($jsonResponseType) {
            return json_decode($file);
        } else {
            return $file;
        }
    }

}