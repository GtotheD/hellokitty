<?php

namespace App\Repositories;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\Repositories\WorkRepository;
use App\Model\Work;

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

    const ID_TYPE = '0102';
    const INTEGRATION_API = '/search/crossworks';

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        $this->sort = $sort;
        $this->offset = $offset;
        $this->limit = $limit;
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
    public function crosswork($ids, $idType = self::ID_TYPE)
    {
        $this->api = 'crossworks';
        $this->id = $ids;
        if(env('APP_ENV') === 'local'){
            return $this;
        }
        foreach ($ids as $id) {
            $queryId[] = $idType . ':' . $id;
        }
        $this->params = [
            '_system' => 'TsutayaApp',
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

    /**
     * @param $ids
     *
     * @return $this
     */
    public function xmediaSeries($ids, $idType = self::ID_TYPE)
    {
        $this->api = 'xmediaSeries'; // for stub
        $this->id = $ids;
        if(env('APP_ENV') === 'local'){
            return $this;
        }
        foreach ($ids as $id) {
            $queryId[] = $idType . ':' . $id;
        }
        $this->params = [
            '_system' => 'TsutayaApp',
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

    /**
     * @param $ids
     *
     * @return $this
     */
    public function xmediaRelation($ids, $idType = self::ID_TYPE)
    {
        $idType = '';
        $this->api = 'xmediaRelation';
        $this->id = $ids;
        if(env('APP_ENV') === 'local'){
            return $this;
        }
        foreach ($ids as $id) {
            $queryId[] = $idType . ':' . $id;
        }
        $this->params = [
            '_system' => 'TsutayaApp',
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

    public function productDetail($ids, $idType = self::ID_TYPE )
    {
        $this->api = 'productDetail';
        $this->id = $ids;
        if(env('APP_ENV') === 'local'){
            return $this;
        }
        foreach ($ids as $id) {
            $queryId[] = $idType . ':' . $id;
        }
        $this->params = [
            '_system' => 'TsutayaApp',
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

    // override
    // getが実行された際に、キャッシュへ問い合わせを行う。
    // データ存在していれば、DBから値を取得
    // 存在していなければ、Himoから取得して返却する
    // 返却した値は、DBに格納する
    public function get()
    {
        // Check and read array workId
        if(!is_array($this->id)) {
            return $this->stub($this->api, $this->id);
        }

        // Get multi works in local
        $results = [];
        foreach ($this->id as $key => $workId) {
            if(!$results) {
                $results = $this->stub($this->api, $workId);
            }
            else {
                $response = $this->stub($this->api, $workId);
                if($response) {
                    $results['results']['rows'][] = array_first($response['results']['rows']);
                    $results['results']['total'] = $key + 1;
                }
            }
        }
        return $results;
    }

    private function stub($apiName, $filename)
    {
        if($this->api === 'xmediaSeries') {
            $filename .= '_1';
            $apiName = 'xmedia';
        } else  if ($this->api === 'xmediaRelation') {
            $filename .= '_2';
            $apiName = 'xmedia';
        }

        $path = base_path('tests/himo/');
        $path = $path . $apiName;
        if(!realpath($path . '/' . $filename)) {
            return null;
        }
        $file = file_get_contents($path . '/' . $filename);
        return json_decode($file, TRUE);
    }
}
