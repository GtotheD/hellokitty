<?php

namespace App\Repositories;

use App\Model\Structure;

/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/13
 * Time: 15:01
 */
class StructureRepository
{
    const DVD = 1;
    const CD = 2;
    const BOOK = 3;
    const GAME = 4;

    const RENTAL = 1;
    const SELL = 2;

    protected $structure;
    protected $limit;
    protected $offset;
    protected $hasNext;
    protected $totalCount;
    protected $page;
    protected $rows;

    public function __construct()
    {
        $this->structure = New Structure;
    }

    /**
     * @return mixed
     */
    public function getHasNext()
    {
        return $this->hasNext;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return (int)$this->limit;
    }

    /**
     * @return mixed
     */
    public function getOffset()
    {
        return (int)$this->offset;
    }

    /**
     * @return mixed
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @return Array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @param mixed $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return object
     */
    public function get($goodsType, $saleType)
    {
        $goodsType = $this->convertGoodsTypeToId($goodsType);
        $saleType = $this->convertSaleTypeToId($saleType);
        $this->structure->set($goodsType, $saleType);
        $this->totalCount = $this->structure->count();
        $structures = $this->structure->get($this->limit, $this->offset);
        if (count($structures) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }
        $rows = [];
        foreach ($structures as $structure) {
            $apiUrl = null;
            if (!empty($structure->section_file_name)) {
                $apiUrl = $structure->api_url . $structure->section_file_name;
            } else {
                $apiUrl = $structure->api_url;
            }
            $unit = [
                'sectionType' => $structure->section_type,
                'title' => $structure->title,
                'apiUrl' => $apiUrl,
                'linkUrl' => $structure->link_url,
                'isTapOn' => $structure->is_tap_on ? true : false,
                'isRanking' => $structure->is_ranking ? true : false,
            ];
            if ($structure->section_type == 1) {
                $unit['width'] = $structure->banner_width;
                $unit['height'] =$structure->banner_height;
            }
            $rows[] = $unit;

        }
        $this->rows = $rows;
        return $this;
    }

    /**
     * @return integer
     */
    public function convertGoodsTypeToId($goodsType)
    {
        switch ($goodsType) {
            case 'dvd':
                return self::DVD;
            case 'book':
                return self::BOOK;
            case 'cd':
                return self::CD;
            case 'game':
                return self::GAME;
            default:
                return false;
        }
    }

    /**
     * @return integer
     */
    public function convertSaleTypeToId($saleType)
    {
        switch ($saleType) {
            case 'rental':
                return self::RENTAL;
            case 'sell':
                return self::SELL;
            default:
                return false;
        }
    }

}