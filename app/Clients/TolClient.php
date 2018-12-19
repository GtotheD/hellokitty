<?php
/**
 * Created by PhpStorm.
 * User: usuda
 * Date: 2018/11/29
 * Time: 15:53
 */

namespace App\Clients;


class TolClient extends BaseClient
{
    protected $memId;

    public function __construct($memId)
    {
        parent::__construct();
        $this->memId = $memId;
    }

    public function getMemberDetail()
    {
        // todo stub
        $path = base_path('tests/Data/tol/');
        $csv = file_get_contents($path . 'mmc200');
        return $csv;
    }

    public function getCMemberList()
    {
        // todo stub
        $path = base_path('tests/Data/tol/');
        $csv = file_get_contents($path . 'mmc208');
        return $csv;
    }

    public function getFlatRentalOperation()
    {
        // todo stub
        $path = base_path('tests/Data/tol/');
        $csv = file_get_contents($path . 'mfr001');
        return $csv;
    }

    public function getRentalApplication()
    {
        // todo stub
        $path = base_path('tests/Data/tol/');
        $csv = file_get_contents($path . 'mre001');
        return $csv;
    }

    public function getMembershipStatus()
    {
        // todo stub
        $path = base_path('tests/Data/tol/');
        $csv = file_get_contents($path . 'membershipStatus');
        return $csv;
    }

}