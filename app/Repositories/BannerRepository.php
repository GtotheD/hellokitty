<?php

namespace App\Repositories;

use App\Model\Structure;
use App\Repositories\TWSRepository;
use App\Repositories\TAPRepository;
use App\Repositories\SectionRepository;
use App\Repositories\FixtureRepository;
use App\Model\Section;
use App\Model\Banner;
use App\Exceptions\NoContentsException;

/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/13
 * Time: 15:02
 */
class BannerRepository
{

    protected $section;
    protected $limit;
    protected $offset;
    protected $hasNext;
    protected $totalCount;
    protected $page;
    protected $rows;
    protected $height;
    protected $width;


    public function __construct()
    {
        $this->banner = New Banner;
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
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    public function banner($bannerName)
    {
        $rows = null;
        $this->banner = new Banner;
        $this->banner->conditionSectionBanner($bannerName);
        $this->totalCount = $this->banner->count();
        $banners = $this->banner->get();
        if (count($banners) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }
        foreach ($banners as $banner) {
            $rows[] =
                [
                    'imageUrl' => $banner->image_url,
                    'isTapOn' => $banner->is_tap_on? true : false,
                    'linkUrl' => $banner->link_url
                ];
            $this->width = $banner->banner_width;
            $this->height = $banner->banner_height;
        }
        $this->rows = $rows;
        return $this;
    }
}