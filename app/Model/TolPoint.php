<?php
/**
 * Created by PhpStorm.
 * User: usuda
 * Date: 2018/11/29
 * Time: 16:05
 */

namespace App\Model;

/**
 * Tポイント取得API
 * @package App\Model
 */
class TolPoint extends TolBaseModel
{
    public function getDetail()
    {
        // todo:　CSVで取得してきたものを変換して返却する
        return $this->tolClient->getPoint();
    }
}
