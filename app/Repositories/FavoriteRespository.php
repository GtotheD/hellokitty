<?php

namespace App\Repositories;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\Repositories\WorkRepository;
use App\Model\Work;

class FavoriteRepository extends ApiRequesterRepository 
{
	public function __construct($sort = 'asc', $offset = 0, $limit = 2000)
    {
        $this->sort = $sort;
        $this->offset = $offset;
        $this->limit = $limit;
    }

	/**
     * @return int
     */
    public function setTlsc($tlsc)
    {
        $this->tlsc = $tlsc;
    }

    /**
     * @return int
     */
    public function setWorkIds($work_ids)
    {
        $this->work_ids = $work_ids;
    }

    public function get($tlsc) 
    {
    	return $this->tlsc;
    }

    public function add($tlsc, $work_ids) 
    {
    	
    }

    public function merge($tlsc, $work_ids) 
    {
    	
    }

    public function delete($tlsc, $work_ids) 
    {
    	
    }
}

?>