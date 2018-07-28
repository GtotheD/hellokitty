<?php

namespace App\Repositories;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class FavoriteRepository extends ApiRequesterRepository 
{
    protected $sort;
    protected $offset;
    protected $limit;
    protected $apiHost;

    public function __construct($sort = 'asc', $offset = 0, $limit = 2000)
    {
        $this->sort = $sort;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->apiHost = env('FAVORITE_API_HOST');
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
     * @return type
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
     * @return type
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
     * @return type
     */
    public function add($request) 
    {
    	$this->apiPath = $this->apiHost . '/api/v1/favorite/add/';
        $this->queryParams = json_encode($request);
        return $this->postBody(true);
    }

    /**
     * Merge
     * @param type $tlsc 
     * @return type
     */
    public function merge($request) 
    {
        $this->apiPath = $this->apiHost . '/api/v1/favorite/add?force=true';
        $this->queryParams = json_encode($request);
        return $this->postBody(true);
    }

    /**
     * Description
     * @param type $request 
     * @return type
     */
    public function delete($request) 
    {
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
        foreach ($response['rows'] as $rowElement) {
            $tempElemet['workId'] = $rowElement['workId'];
            $tempElemet['item_type'] = $rowElement['msdbItem'];
            $tempElemet['created_at'] = $rowElement['appCreatedAt'];
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

?>