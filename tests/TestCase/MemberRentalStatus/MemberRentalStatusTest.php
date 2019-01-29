<?php

use League\Csv;
use League\Csv\Writer;
use Illuminate\Support\Carbon;

class MemberRentalStatusTest extends TestCase
{
    public function __construct(string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->testDir = __DIR__;
        Carbon::setTestNow(new Carbon('2018-10-01 00:00:00'));
    }
    /*
        1 : j5vhV9s8gYTuP4nbYx%2FgvPGEpF%2BOYv7wkTIdfk0qJlc%3D
        2 : qA7l7%2FoSjyHRjaL7p9k4jPGEpF%2BOYv7wkTIdfk0qJlc%3D
        3 : zI1eyTYGzH53jae7uc8yhvGEpF%2BOYv7wkTIdfk0qJlc%3D
        4 : k4ZfPxkZEOast9bDqbnYzfGEpF%2BOYv7wkTIdfk0qJlc%3D
        5 : AHuaOGkiIRVX%2BBZYQvPMzvGEpF%2BOYv7wkTIdfk0qJlc%3D
        6 : FaQtl8LeIjey6t5hu7CDcvGEpF%2BOYv7wkTIdfk0qJlc%3D
        7 : f2NMiQbgQ2sAR6VwylPen%2FGEpF%2BOYv7wkTIdfk0qJlc%3D
        8 : YC9Dk3IHc1sqIGUGBFxCwvGEpF%2BOYv7wkTIdfk0qJlc%3D
        9 : ELjrCnorPEOzMcoct6uLhfGEpF%2BOYv7wkTIdfk0qJlc%3D
        10 : n4vlx5%2FDJoKheOYYNFuPbvGEpF%2BOYv7wkTIdfk0qJlc%3D
        11 : uyfcqotdA3UEPq6FRhmutPGEpF%2BOYv7wkTIdfk0qJlc%3D
        12 : Ec%2BXPR9JqRMYZb%2B7OQcWTvGEpF%2BOYv7wkTIdfk0qJlc%3D
        13 : RTt6G3UbVxzZpIfs86T6avGEpF%2BOYv7wkTIdfk0qJlc%3D
        14 : XVK8OHWvANbEKsQqF4rGXfGEpF%2BOYv7wkTIdfk0qJlc%3D
        15 : yZYgLWBBk4RMHXsFaxb1xvGEpF%2BOYv7wkTIdfk0qJlc%3D
        16 : 9v9W6VweqVmiIK2PyIx50%2FGEpF%2BOYv7wkTIdfk0qJlc%3D
        17 : UOCJDoHmRGMNGHNj9HXw8vGEpF%2BOYv7wkTIdfk0qJlc%3D
        18 : GeW6EXlYsG1oEqzoNIWfZ%2FGEpF%2BOYv7wkTIdfk0qJlc%3D
        19 : eNe27HGNMNHy%2BDavpLl3UfGEpF%2BOYv7wkTIdfk0qJlc%3D
        20 : KRaA4Dwrv5qt1harfz%2F6CvGEpF%2BOYv7wkTIdfk0qJlc%3D
        21 : N2GWGHu4TdIQCLrLBRov9PGEpF%2BOYv7wkTIdfk0qJlc%3D
        22 : xahcuu4%2B9y5Fsj%2BBXNMWkvGEpF%2BOYv7wkTIdfk0qJlc%3D
        23 : WAZN%2FD72oQuSuJPouaJpk%2FGEpF%2BOYv7wkTIdfk0qJlc%3D
        24 : Xle3jYSSMsFVCxlGQf83p%2FGEpF%2BOYv7wkTIdfk0qJlc%3D
        25 : Y%2FJXhpmHjN5mvwNb%2Fp6bXPGEpF%2BOYv7wkTIdfk0qJlc%3D
   */
    public function data()
    {
        return [
            '項番1' => [
                'tolId' => 'j5vhV9s8gYTuP4nbYx%2FgvPGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 1,
                'rentalExpirationDate' => ''
            ],
            '項番2' => [
                'tolId' => 'qA7l7%2FoSjyHRjaL7p9k4jPGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 2,
                'rentalExpirationDate' => ''
            ],
            '項番3' => [
                'tolId' => 'zI1eyTYGzH53jae7uc8yhvGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 3,
                'rentalExpirationDate' => ''
            ],
            '項番4' => [
                'tolId' => 'k4ZfPxkZEOast9bDqbnYzfGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 4,
                'rentalExpirationDate' => ''
            ],
            '項番5' => [
                'tolId' => 'AHuaOGkiIRVX%2BBZYQvPMzvGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 5,
                'rentalExpirationDate' => ''
            ],
            '項番6' => [
                'tolId' => 'FaQtl8LeIjey6t5hu7CDcvGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 1,
                'rentalExpirationDate' => ''
            ],
            '項番7' => [
                'tolId' => 'f2NMiQbgQ2sAR6VwylPen%2FGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 2,
                'rentalExpirationDate' => ''
            ],
            '項番8' => [
                'tolId' => 'YC9Dk3IHc1sqIGUGBFxCwvGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 3,
                'rentalExpirationDate' => ''
            ],
            '項番9' => [
                'tolId' => 'ELjrCnorPEOzMcoct6uLhfGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 4,
                'rentalExpirationDate' => ''
            ],
            '項番10' => [
                'tolId' => 'n4vlx5%2FDJoKheOYYNFuPbvGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 5,
                'rentalExpirationDate' => ''
            ],
            '項番11' => [
                'tolId' => 'uyfcqotdA3UEPq6FRhmutPGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 6,
                'rentalExpirationDate' => ''
            ],
            '項番12' => [
                'tolId' => 'Ec%2BXPR9JqRMYZb%2B7OQcWTvGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 7,
                'rentalExpirationDate' => ''
            ],
            '項番13' => [
                'tolId' => 'RTt6G3UbVxzZpIfs86T6avGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 8,
                'rentalExpirationDate' => ''
            ],
            '項番14' => [
                'tolId' => 'XVK8OHWvANbEKsQqF4rGXfGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 8,
                'rentalExpirationDate' => ''
            ],
            '項番15' => [
                'tolId' => 'yZYgLWBBk4RMHXsFaxb1xvGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 9,
                'rentalExpirationDate' => '2018-12-15 00:00:00'
            ],
            '項番16' => [
                'tolId' => '9v9W6VweqVmiIK2PyIx50%2FGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 10,
                'rentalExpirationDate' => '2018-12-15 00:00:00'
            ],
            '項番17' => [
                'tolId' => 'UOCJDoHmRGMNGHNj9HXw8vGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 11,
                'rentalExpirationDate' => '2018-11-15 00:00:00'
            ],
            '項番18' => [
                'tolId' => 'GeW6EXlYsG1oEqzoNIWfZ%2FGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 12,
                'rentalExpirationDate' => '2018-11-15 00:00:00'
            ],
            '項番19' => [
                'tolId' => 'eNe27HGNMNHy%2BDavpLl3UfGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 13,
                'rentalExpirationDate' => '2018-11-15 00:00:00'
            ],
            '項番20' => [
                'tolId' => 'KRaA4Dwrv5qt1harfz%2F6CvGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 14,
                'rentalExpirationDate' => '2018-11-15 00:00:00'
            ],
            '項番21' => [
                'tolId' => 'N2GWGHu4TdIQCLrLBRov9PGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 15,
                'rentalExpirationDate' => '2018-11-15 00:00:00'
            ],
            '項番22' => [
                'tolId' => 'xahcuu4%2B9y5Fsj%2BBXNMWkvGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 16,
                'rentalExpirationDate' => '2018-11-15 00:00:00'
            ],
            '項番23' => [
                'tolId' => 'WAZN%2FD72oQuSuJPouaJpk%2FGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 6,
                'rentalExpirationDate' => ''
            ],
            '項番24' => [
                'tolId' => 'Xle3jYSSMsFVCxlGQf83p%2FGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 8,
                'rentalExpirationDate' => ''
            ],
            '項番25' => [
                'tolId' => 'Y%2FJXhpmHjN5mvwNb%2Fp6bXPGEpF%2BOYv7wkTIdfk0qJlc%3D',
                'itemNumber' => 17,
                'rentalExpirationDate' => ''
            ],
        ];
    }

    /**
     * @test
     * @dataProvider data
     */
    public function パターンチェック($tolid,$itemNumber, $rentalExpirationDate)
    {
        $json = json_encode([
            'tolId' => $tolid,
        ]);

        $url = '/member/status/rental';
        $response = $this->postWithAuth($url, $json);
        $actual = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($actual['itemNumber'], $itemNumber);
        $this->assertEquals($actual['rentalExpirationDate'], $rentalExpirationDate);

    }



    /**
     * テストケース抽出用
     */
    public function createPattern()
    {
        //
        $deletedFlgList = [
            0,
            1
        ];
        $expirationDateList = [
            '2018-10-31 28:59:59',
            '2018-11-01 00:00:00',
            '2018-11-01 00:00:01',
            '2018-11-30 23:59:59',
            '2018-12-01 00:00:00',
            '2018-12-01 00:00:01',
            '2018-12-31 23:59:59',
            '2019-01-01 00:00:00',
            '2019-01-01 00:00:01',
            '2019-01-31 23:59:59',
        ];
        $wcardFlgList = [
            null,
            'W1',
            'W2'
        ];
        $cKainList = [
            null,
            '00',
            '01'
        ];
        $status1List = [
            null,
            '00',
            '01',
            '80',
            '90',
            '99',
        ];
        $rentaltorokushinseistatusList = [
            1, 2, 3
        ];
        $rentalkoshinshinseistatusList = [
            1, 2, 3
        ];
        $honninkakuninyohiList = [
            0, 1
        ];
        $tmFlgList = [
            0, 1, 2, 3
        ];
        foreach ($deletedFlgList as $deletedFlgItem) {
            foreach ($expirationDateList as $expirationDateItem) {
                foreach ($wcardFlgList as $wcardFlgItem) {
                    foreach ($cKainList as $cKainItem) {
                        foreach ($status1List as $status1item) {
                            foreach ($rentaltorokushinseistatusList as $rentaltorokushinseistatusItem) {
                                foreach ($rentalkoshinshinseistatusList as $rentalkoshinshinseistatusItem) {
                                    foreach ($honninkakuninyohiList as $honninkakuninyohiItem) {
                                        foreach ($tmFlgList as $tmFlgItem) {
                                            $testData['deletedFlg'] = $deletedFlgItem;
                                            $testData['expirationDate'] = $expirationDateItem;
                                            $testData['wcardFlg'] = $wcardFlgItem;
                                            $testData['cKain'] = $cKainItem;
                                            $testData['status1'] = $status1item;
                                            $testData['tmFlg'] = $tmFlgItem;
                                            $testData['rentaltorokushinseistatus'] = $rentaltorokushinseistatusItem;
                                            $testData['rentalkoshinshinseistatus'] = $rentalkoshinshinseistatusItem;
                                            $testData['honninkakuninyohi'] = $honninkakuninyohiItem;
                                            $testDataList[] = $testData;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

//        $header = [
//            'deletedFlg',
//            'expirationDate',
//            'wcardFlg',
//            'cKain',
//            'status1',
//            'tmFlg',
//            'rentaltorokushinseistatus',
//            'rentalkoshinshinseistatus',
//            'honninkakuninyohi',
//        ];
//        $csv = Writer::createFromString('');
//        $csv->insertOne($header);
//        $csv->insertAll($testDataList);
//
//        $file = '/var/www/Tsutaya/tap/tap_r_api/test.csv';
//        file_put_contents($file, $csv->getContent()); //returns the CSV document as a string
        return $testDataList;
    }
}