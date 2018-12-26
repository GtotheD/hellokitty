<?php

use League\Csv;
use League\Csv\Writer;

class MemberRentalStatusTest extends TestCase
{
    public function __construct(string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->testDir = __DIR__;
    }

    public function data()
    {
        return [
//            '項番1' => [
//                'memid' => '1',
//                'itemNumber' => 1,
//                'rentalExpirationDate' => ''
//            ],
//            '項番2' => [
//                'memid' => '2',
//                'itemNumber' => 2,
//                'rentalExpirationDate' => ''
//            ],
//            '項番3' => [
//                'memid' => '3',
//                'itemNumber' => 3,
//                'rentalExpirationDate' => ''
//            ],
//            '項番4' => [
//                'memid' => '4',
//                'itemNumber' => 4,
//                'rentalExpirationDate' => ''
//            ],
//            '項番5' => [
//                'memid' => '5',
//                'itemNumber' => 5,
//                'rentalExpirationDate' => ''
//            ],
//            '項番6' => [
//                'memid' => '6',
//                'itemNumber' => 1,
//                'rentalExpirationDate' => ''
//            ],
//            '項番7' => [
//                'memid' => '7',
//                'itemNumber' => 2,
//                'rentalExpirationDate' => ''
//            ],
//            '項番8' => [
//                'memid' => '8',
//                'itemNumber' => 3,
//                'rentalExpirationDate' => ''
//            ],
//            '項番9' => [
//                'memid' => '9',
//                'itemNumber' => 4,
//                'rentalExpirationDate' => ''
//            ],
//            '項番10' => [
//                'memid' => '10',
//                'itemNumber' => 5,
//                'rentalExpirationDate' => ''
//            ],
            '項番11' => [
                'memid' => '11',
                'itemNumber' => 6,
                'rentalExpirationDate' => ''
            ],
            '項番12' => [
                'memid' => '12',
                'itemNumber' => 7,
                'rentalExpirationDate' => ''
            ],
            '項番13' => [
                'memid' => '13',
                'itemNumber' => 8,
                'rentalExpirationDate' => ''
            ],
            '項番14' => [
                'memid' => '14',
                'itemNumber' => 8,
                'rentalExpirationDate' => ''
            ],
//            '項番15' => [
//                'memid' => '15',
//                'itemNumber' => 15,
//                'rentalExpirationDate' => ''
//            ],
//            '項番16' => [
//                'memid' => '16',
//                'itemNumber' => 16,
//                'rentalExpirationDate' => ''
//            ],
//            '項番17' => [
//                'memid' => '17',
//                'itemNumber' => 17,
//                'rentalExpirationDate' => ''
//            ],
//            '項番18' => [
//                'memid' => '18',
//                'itemNumber' => 18,
//                'rentalExpirationDate' => ''
//            ],
//            '項番19' => [
//                'memid' => '19',
//                'itemNumber' => 19,
//                'rentalExpirationDate' => ''
//            ],
//            '項番20' => [
//                'memid' => '20',
//                'itemNumber' => 20,
//                'rentalExpirationDate' => ''
//            ],
//            '項番21' => [
//                'memid' => '21',
//                'itemNumber' => 21,
//                'rentalExpirationDate' => ''
//            ],
//            '項番22' => [
//                'memid' => '22',
//                'itemNumber' => 22,
//                'rentalExpirationDate' => ''
//            ],
//            '項番23' => [
//                'memid' => '23',
//                'itemNumber' => 23,
//                'rentalExpirationDate' => ''
//            ],
//            '項番24' => [
//                'memid' => '24',
//                'itemNumber' => 24,
//                'rentalExpirationDate' => ''
//            ],
//            '項番25' => [
//                'memid' => '25',
//                'itemNumber' => 25,
//                'rentalExpirationDate' => ''
//            ],
//            '項番26' => [
//                'memid' => '26',
//                'itemNumber' => 26,
//                'rentalExpirationDate' => ''
//            ],
        ];
    }

    /**
     * @test
     * @dataProvider data
     */
    public function パターンチェック($memid,$itemNumber, $rentalExpirationDate)
    {
        $json = json_encode([
            'tolId' => $memid,
        ]);

        $url = '/member/rental/status';
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