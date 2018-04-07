<?php
/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2018/04/07
 * Time: 12:26
 */
function newFlg($saleStartDate)
{
    $end = date('Y-m-d', strtotime('-1 month', time()));
    if ($end < $saleStartDate) {
        return true;
    }
    return false;
}