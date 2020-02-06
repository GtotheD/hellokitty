<?php

namespace App\Repositories;

use App\Exceptions\NoContentsException;

/**
 * User: sukegawa
 * Date: 2020/1/16
 * Time: 10:59
 */
class MoanaRepository extends ApiRequesterRepository
{
    protected $apiHost;
    protected $authKey;

    const INTEGRATION_API = '/tagsRecommended/masterData';

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        parent::__construct();
        $this->apiHost = env('MOANA_API_HOST');
        $this->authKey = env('MOANA_AUTH_KEY');
    }

    /*
     * MoanaAPIへのパラメータセット
     */
    public function masterData($tag)
    {
        $this->apiPath = $this->apiHost . '/tagsRecommended/masterData/' . $tag;
        $this->setHeaders([
            'Authorization' => $this->authKey
        ]);


        return $this;
    }

    public function getMasterData($tag)
    {
        $apiResult = $this->masterData($tag)->get();
        $tagId = '';
        $tagMessage = '';
        if (!empty($apiResult)) {
            $tagId = $apiResult['tagMasters'][0]['id'];
            $tagMessage = $apiResult['tagMasters'][0]['text']; 
        }

        $tagData = [
            'tag' => $tagId,
            'tagTitle' => '',
            'tagMessage' => $tagMessage
        ];

        return (object)$tagData;
    }

    // override
    // getが実行された際に、キャッシュへ問い合わせを行う。
    // データ存在していれば、DBから値を取得
    // 存在していなければ、Himoから取得して返却する
    // 返却した値は、DBに格納する
    public function get($jsonResponse = true)
    {
        if(env('APP_ENV') !== 'local' && env('APP_ENV') !== 'testing' ){
            return parent::get($jsonResponse);
        }
    }
}
