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
    protected $loginType;


    public function __construct()
    {
        $this->banner = New Banner;
        $this->totalCount = 0;
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
     * @param mixed $loginType
     */
    public function setLoginType($loginType)
    {
        $this->loginType = $loginType;
    }

    public function banner($bannerName, $isFixBanner = false)
    {
        $rows = null;
        $structures = [];
        $this->banner = new Banner;
        $loginType = $this->loginType === 'true' ? 1 : 0;
        $this->banner->setLoginType($loginType);
        $structure = new structure();
        $structures = $structure->setConditionFindBySectionfilenameWithDispTime($bannerName)->getOne();
        if (empty($structures)) {
            return $this;
        }
        if ($isFixBanner) {
            $this->banner->conditionSectionFixedBanner($bannerName);
        } else {
            $this->banner->conditionSectionBanner($structures->id);
        }
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
                    'isTapOn' => $banner->is_tap_on ? true : false,
                    'linkUrl' => $banner->link_url
                ];
        }
        $this->width = $structures->banner_width;
        $this->height = $structures->banner_height;
        $this->rows = $rows;
        return $this;
    }
}