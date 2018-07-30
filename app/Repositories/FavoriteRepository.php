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
                    'msdbItem' => $workRepository->convertWorkTypeIdToMsdbItem($work['workTypeId']),
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
        $workRepository = New WorkRepository;
        $workIds = [];
        foreach ($ids as $id) {
            // PTAがあった場合はworkId
            if (preg_match('/^PTA/', $id['id'])) {
                $work = $workRepository->get($id['id']);
                // なかった場合はurlCd
            } else {
                $work = $workRepository->get($id['id'],null,'0105');
            }
            // 検索がヒットしなかった場合でもそのまま続行
            if (empty($work)) {
                continue;
            }
            $workIds[] = [
                'workId' => $work['workId'],
                'msdbItem' => $workRepository->convertWorkTypeIdToMsdbItem($work['workTypeId']),
                'appCreatedAt' => $id['appCreatedAt']
            ];
        }
        $request = [
            'tlsc' => $this->tlsc,
            'systemId' => $this->systemId,
            'rows' => $workIds
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
        $workRepository = New WorkRepository;
        $workIds = [];
        foreach ($ids as $id) {
            // PTAがあった場合はworkId
            if (preg_match('/^PTA/', $id)) {
                $work = $workRepository->get($id);
                // なかった場合はurlCd
            } else {
                $work = $workRepository->get($id,null,'0105');
            }
            // 検索がヒットしなかった場合でもそのまま続行
            if (empty($work)) {
                continue;
            }
            $workIds[] = ['workId' => $work['workId']];
        }
        $request = [
            'tlsc' => $this->tlsc,
            'rows' => $workIds
        ];
    	$this->apiPath = $this->apiHost . '/api/v1/favorite/delete/';
        $this->queryParams = json_encode($request);
        return $this->postBody(true);
    }

    public function count($tlsc)
    {
        $this->apiPath = $this->apiHost . '/api/v1/favorite/count/';
        $tlscObj['tlsc'] = $tlsc;
        $this->queryParams = json_encode($tlscObj);
        $response = $this->postBody(true);
        return $response['totalCount'];
    }

    /**
     * Description
     * @param type $response 
     * @return type
     */
    public function formatData($response) {
        $rowsFormat = [];
        $productRepository = New ProductRepository;
        foreach ($response['rows'] as $rowElement) {
            $tempElemet['workId'] = $rowElement['workId'];
            $tempElemet['itemType'] = $productRepository->convertMsdbItemToItemType($rowElement['msdbItem']);
            $tempElemet['createdAt'] = $rowElement['appCreatedAt'];
            array_push($rowsFormat, $tempElemet);
        }

        $responseFormat = [
            'hasNext' => $response['hasNext'],
            'totalCount' => $response['totalCount'],
            'rows' => $rowsFormat
        ];
        return $responseFormat;
    }
}
