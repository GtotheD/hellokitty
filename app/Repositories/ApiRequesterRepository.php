<?php

namespace App\Repositories;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

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
                ['query' => $this->queryParams]
            );
        } catch (ClientException $e) {
            throw new $e;
        }
        return json_decode($result->getBody()->getContents(), true);
    }
}