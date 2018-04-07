<?php
/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2018/04/07
 * Time: 12:27
 */
function trimImageTag($data)
{
    $data = trim(preg_replace('/<.*>/', '', $data));
    $data = preg_replace('/^\/\//', 'https://cdn.', $data);
    return $data;
}