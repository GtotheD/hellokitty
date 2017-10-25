<?php
namespace App\Repositories;

use App\Repositories\TWSRepository;

/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/13
 * Time: 15:02
 */

class SectionRepository
{

    public function fixBanner() {
        $fixBanner = [

        ];
        return $fixBanner;
    }

    public function normal() {
        $normal = [

        ];
        return $normal;
    }

    public function banner($goodsName, $typeName, $sectionName) {
        $banner = [

        ];
        return $banner;
    }

    public function ranking($goodsName, $typeName, $sectionName) {
        $tws = new TWSRepository;
        return $tws->ranking('D045')->get();
    }

    public function recommend() {
        $recommend = [

        ];
        return $recommend;

    }
}