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

class TAPRepository extends ApiRequesterRepository
{

    private $sort;
    private $offset;
    private $limit;
    private $apiHost;
    private $apiKey;

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        parent::__construct();
        $this->sort = $sort;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->apiHost = env('TAP_API_HOST');
        $this->apiKey = env('TAP_API_KEY');
    }

    /*
     * 日付ベースの検索結果を取得するAPIをセットする
     */
    public function release($category, $releaseDateTo) {
        $this->apiPath = $this->apiHost . '/tsutayaappapi/Release';
        $this->queryParams = [
            'apiKey' => $this->apiKey,
            'category' => $category,
            'releaseDateTo' => $releaseDateTo
        ];
        return $this;
    }
}