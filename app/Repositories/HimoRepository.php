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
class HimoRepository extends ApiRequesterRepository
{

    protected $sort;
    protected $offset;
    protected $limit;
    protected $apiHost;
    protected $apiKey;

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        $this->sort = $sort;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->apiHost = env('TWS_API_HOST');
        $this->apiKey = env('TWS_API_KEY');
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /*
     * 詳細情報を取得するAPIをセットする
     */
    public function detail($janCode)
    {
        foreach ($ids as $id) {
            $queryId[] = $idType . ':' . $id;
        }
        $this->params = [
            '_system' => 'TsutayaPassport',
            'id_value' => implode(' || ', $queryId),
            'service_id' => 'tol',
            'msdb_item' => 'video',
            'adult_flg' => '2',
            'response_level' => '9',
            'offset' => $this->offset,
            'limit' => $this->limit,
            'sort_by' => 'auto:asc',
        ];

        return $this;
    }


}