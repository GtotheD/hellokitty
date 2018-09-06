<?php
/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2018/04/07
 * Time: 12:26
 */
use Illuminate\Support\Carbon;

function newFlg($saleStartDate)
{
    $start = Carbon::parse('last month')->startOfDay();
    $end = Carbon::parse('next month')->startOfDay();
    if ($start <= $saleStartDate && $end >= $saleStartDate) {
        return true;
    }
    return false;
}