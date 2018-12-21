<?php

namespace App\Repositories;

use App\Exceptions\NoContentsException;
use App\Repositories\WorkRepository;
use Carbon\Carbon;

class FavoriteRepository extends ApiRequesterRepository
{
    protected $sort;
    protected $offset;
    protected $limit;
    protected $apiHost;

    protected $tlsc;

    public function __construct($sort = 'asc', $offset = 0, $limit = 2000)
    {
        $this->sort = $sort;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->apiHost = env('FAVORITE_API_HOST');
        $this->systemId = env('FAVORITE_API_SYSTEM_ID');
        $this->method = 'POST';
    }

	/**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return (int)$this->limit;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * Set sort
     * @param type $sort 
     * @return type
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * Set tlsc
     * @param type $tlsc 
     * @return type
     */
    public function setTlsc($tlsc)
    {
        $this->tlsc = $tlsc;
    }

    /**
     * Set work ids
     * @param type $rowsData
     * @return string
     */
    public function setWorkIds($rowsData)
    {
        $workIds = [];
        foreach ($rowsData as $row) {
            array_push($workIds, $row['workId']);
        }
        $this->workIds = $workIds;
    }

    /**
     * List
     * @param type $tlsc
     * @return string
     * @throws NoContentsException
     */
    public function list($tlsc) 
    {
        $this->apiPath = $this->apiHost . '/api/v1/favorite/list/?'.'limit='.$this->limit.'&offset='.$this->offset.'&sort='.$this->sort;
        $this->queryParams = json_encode($tlsc);
        return $this->postBody(true);
    }

    /**
     * Add
     * @param type $tlsc
     * @return string
     * @throws NoContentsException
     */
    public function add($id)
    {
        $workRepository = New WorkRepository;
        // PTAがあった場合はworkId
        if (preg_match('/^PTA/', $id)) {
            $work = $workRepository->get($id);
        // なかった場合はurlCd
        } else {
            $work = $workRepository->get($id,null,'0105');
        }
        // 検索がヒットしなかった場合はfalseを返却
        if (empty($work)) {
            return false;
        }
        $date = Carbon::now();
        $request = [
            'tlsc' => $this->tlsc,
            'systemId' => $this->systemId,
            'rows' =>[
                [
                    'workId' => $work['workId'],
                    'msdbItem' => $work['msdbItem'],
                    'workFormatId' => $work['workFormatId'],
                    'appCreatedAt' => $date->toDateTimeString()
                ]
            ]
        ];
    	$this->apiPath = $this->apiHost . '/api/v1/favorite/add/';
        $this->queryParams = json_encode($request);
        return $this->postBody(true);
    }

    /**
     * Merge
     * @param type $tlsc
     * @return string
     * @throws NoContentsException
     */
    public function merge($ids)
    {
        $ids =  $this->convertUrlCdToWorkId($ids);
        // 検索がヒットしなかった場合はfalseを返却
        if (empty($ids)) {
            return false;
        }
        $workIds = [];
        $mergedIds = [];
        foreach ($ids['works'] as $id) {
            $workIds[] = [
                'workId' => $id['id'],
                'msdbItem' => $id['msdbItem'],
                'workFormatId' => $id['workFormatId'],
                'appCreatedAt' => $id['appCreatedAt']
            ];
        }
        foreach ($ids['mergedIds'] as $mergedId) {
            $mergedIds[] = [
                'workId' => $mergedId,
            ];
        }
        $request = [
            'tlsc' => $this->tlsc,
            'systemId' => $this->systemId,
            'rows' => $workIds,
            'mergedId' => $mergedIds,
        ];
        $this->apiPath = $this->apiHost . '/api/v1/favorite/add?force=true';
        $this->queryParams = json_encode($request);
        return $this->postBody(true);
    }

    /**
     * Description
     * @param type $request 
     * @return string
     * @throws NoContentsException
     */
    public function delete($ids)
    {
        $tempIds = [];
        // convert ids to array of id
        foreach ($ids as $id) {
            $tempIds[]['id'] = $id;
        }
        if(!empty($tempIds)) {
            $ids = $tempIds;
        }
        $ids =  $this->convertUrlCdToWorkId($ids);
        // 検索がヒットしなかった場合はfalseを返却
        if (empty($ids)) {
            return false;
        }
        // 通常の削除対象
        foreach ($ids['works'] as $id) {
            $workIds[] = ['workId' => $id['id']];
        }
        // 作品IDがつけ変わった場合、新作品IDで削除に行くために
        // 旧IDでも削除させる。
        foreach ($ids['mergedIds'] as $mergedId) {
            $workIds[] = ['workId' => $mergedId];
        }
        $request = [
            'tlsc' => $this->tlsc,
            'rows' => $workIds
        ];
    	$this->apiPath = $this->apiHost . '/api/v1/favorite/delete/';
        $this->queryParams = json_encode($request);
        return $this->postBody(true);
    }

    /**
     * count record
     * @param type $tlsc
     * @return string
     * @throws NoContentsException
     */
    public function count($tlsc)
    {
        $this->apiPath = $this->apiHost . '/api/v1/favorite/count/';
        $tlscObj['tlsc'] = $tlsc;
        $this->queryParams = json_encode($tlscObj);
        $response = $this->postBody(true);
        return $response['totalCount'];
    }

    /**
     * get favorite version
     * @param type $tlsc
     * @return string
     * @throws NoContentsException
     */
    public function getFavoriteVersion($tlsc)
    {
        $this->apiPath = $this->apiHost . '/api/v1/favorite/version/';
        $tlscObj['tlsc'] = $tlsc;
        $this->queryParams = json_encode($tlscObj);
        $response = $this->postBody(true);
        return $response['version'];
    }

    /**
     * Description
     * @param type $response 
     * @return type
     */
    public function formatData($response) {
        $rowsFormat = [];
        $productRepository = New ProductRepository;
        $workRepository = new WorkRepository;
        foreach ($response['rows'] as $rowElement) {
            $tempElemet['workId'] = $rowElement['workId'];
            $tempElemet['itemType'] = $productRepository->convertMsdbItemToItemType($rowElement['msdbItem']);
            if ($rowElement['workFormatId'] == $workRepository::WORK_FORMAT_ID_MUSICVIDEO) {
                $tempElemet['itemType'] = 'dvd';
            }
            $tempElemet['createdAt'] = $rowElement['appCreatedAt'];
            array_push($rowsFormat, $tempElemet);
        }

        $responseFormat = [
            'hasNext' => $response['hasNext'],
            "isUpdate" => true,
            'totalCount' => $response['totalCount'],
            'rows' => $rowsFormat
        ];
        return $responseFormat;
    }

    public function convertUrlCdToWorkId($ids)
    {
        $workRepository = new WorkRepository;
        $urlCd = [];
        $workIds =[];
        $newIds = [];
        $works = [];
        $acquireWorkId = [];
        $unacquireWorkId = [];
        foreach ($ids as $id) {
            // PTAがあった場合はworkId
            if (!preg_match('/^PTA/', $id['id'])) {
                $urlCd[] = $id['id'];
            } else {
                $workIds[] = $id['id'];
            }
        }
        if (!empty($urlCd)) {
            $works = $workRepository->getWorkList($urlCd, ['work_id', 'url_cd', 'msdb_item', 'work_format_id'], '0105', true)['rows'];
        }
        // UrlCdで検索後workが存在していた場合はマージ
        if (!empty($works)) {
            $workIdWorks = $workRepository->getWorkList($workIds, ['work_id', 'url_cd', 'msdb_item', 'work_format_id'], null, true)['rows'];
            // workが取れた時にマージする。
            if (!empty($workIdWorks)) {
                $works = array_merge($works, $workIdWorks);
            }
            // workが存在していない場合はマージなしで通常取得
        } else {
            $works = $workRepository->getWorkList($workIds, ['work_id', 'url_cd', 'msdb_item', 'work_format_id'], null, true)['rows'];
        }
        // work_id取得できなかったもので付け替えのものがあるかもしれないので再チャレンジ。
        // 単体問い合わせする。
        // urlCdの場合は付け替えは考慮しないで対象外のため、$workIdsとの比較を行う。
        // 取得できた場合はwork_idのみ抽出
        if (!empty($works)) {
            foreach ($works as $work) {
                $acquireWorkId[] = $work['workId'];
            }
            $unacquireWorkId = array_diff($workIds, $acquireWorkId);
            //１件も取得できなかった場合全部対象にする。
        } else {
            $unacquireWorkId = $workIds;
        }
        $mergedIds = [];
        foreach ($unacquireWorkId as $unacquireWorkIdRow) {
            $workTmp = $workRepository->get($unacquireWorkIdRow);
            // array_searchで検索する為、idのみ抽出
            foreach ($ids as $id) {
                $idsTmp[] = $id['id'];
            }
            // 対象ID位置の検索
            $idsIndex = array_search($unacquireWorkIdRow, $idsTmp);
            // IDの差し替え
            $mergedIds[] = $ids[$idsIndex]['id'];
            // 20181214サーバー上にて追加
            if (!empty($workTmp['workId'])) {
                $ids[$idsIndex]['id'] = $workTmp['workId'];
                $works[] = [
                    'workId' => $workTmp['workId'],
                    'urlCd' => $workTmp['urlCd'],
                    'msdbItem' => $workTmp['msdbItem'],
                    'workFormatId' => $workTmp['workFormatId'],
                ];
            }
        }
        // 検索がヒットしなかった場合はfalseを返却
        if (empty($works)) {
            return [];
        }
        foreach ($ids as $key => $id) {
            foreach ($works as $work) {
                // マッチしたものだけ入れる
                if($work['urlCd'] === $id['id'] || $work['workId'] === $id['id']) {
                    $id['id'] = $work['workId'];
                    $id['msdbItem'] = $work['msdbItem'];
                    $id['workFormatId'] = $work['workFormatId'];
                    if (array_key_exists('app_created_at', $id)) {
                        $id['appCreatedAt'] = $id['app_created_at'];
                    }
                    $newIds[$key] = $id;
                }
            }
        }
        return [
            'works' => $newIds,
            'mergedIds' => $mergedIds
        ];
    }
}
