<?php
/**
 * Created by PhpStorm.
 * User: usuda
 * Date: 2018/11/29
 * Time: 15:53
 */

namespace App\Client;


class MintClient extends BaseClient
{
    public function getMemberDetail ()
    {
        // todo stub
        $path = base_path('tests/Data/tol/');
        $csv = file_get_contents($path . 'mmc200.csv');
        return $csv;
    }

    public function getCMemberList ()
    {
        //
    }

}