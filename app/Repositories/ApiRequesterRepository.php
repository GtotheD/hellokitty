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
                throw new NotFoundHttpException();
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
