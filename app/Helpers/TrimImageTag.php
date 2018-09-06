<?php
/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2018/04/07
 * Time: 12:27
 */
function trimImageTag($data, $isScene=false)
{
    $data = trim(preg_replace('/<.*>/', '', $data));
    $data = preg_replace('/^\/\//', 'https://cdn.', $data);
    if ($isScene == true) {
        $data = preg_replace('/www\.tsutaya\.co\.jp/', 'store-tsutaya.tsite.jp', $data);
    }
    return $data;
}
