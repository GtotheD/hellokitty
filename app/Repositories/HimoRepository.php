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
    public function detail($id)
    {
        $this->id = $id;
//        foreach ($ids as $id) {
//            $queryId[] = $idType . ':' . $id;
//        }
//
//        $queryId = ['0106:101017982'];
//        $this->params = [
//            '_system' => 'TsutayaApp',
//            'id_value' => implode(' || ', $queryId),
//            'service_id' => 'tol',
//            'msdb_item' => 'video',
//            'adult_flg' => '2',
//            'response_level' => '9',
//            'offset' => $this->offset,
//            'limit' => $this->limit,
//            'sort_by' => 'auto:asc',
//        ];

        return $this;
    }

    // override
    // getが実行された際に、キャッシュへ問い合わせを行う。
    // データ存在していれば、DBから値を取得
    // 存在していなければ、Himoから取得して返却する
    // 返却した値は、DBに格納する
    public function get()
    {
        switch ($this->id) {
            case 'PTA0000RV0LG':
                return $this->stub('dvd');
            case 'PTA0000M6ZK2':
                return $this->stub('cd');
            case 'PTA0000G4CSA':
                return $this->stub('book');
            case 'PTA0000U8W8U':
                return $this->stub('game');
        }
    }

    private function stub($filename)
    {
        $path = base_path('tests/fixture/himo');
        $file = file_get_contents($path . '/' .$filename);
        return json_decode($file, TRUE);
    }
}