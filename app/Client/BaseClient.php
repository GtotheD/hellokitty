<?php
/**
 * Created by PhpStorm.
 * User: usuda
 * Date: 2018/11/29
 * Time: 15:53
 */

namespace App\Client;


class BaseClient
{
    protected $apiPath;
    protected $queryParams;
    protected $method;
    protected $headers = [];

    public function __construct()
    {
        $this->method = 'GET';
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    /*
     * 取得の実行
     */
    public function get($jsonResponse = true)
    {
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
     * post json in body
     * @param type|bool $jsonResponse
     * @return string
     */
    public function postBody($jsonResponse = true)
    {
        $url = $this->apiPath;
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