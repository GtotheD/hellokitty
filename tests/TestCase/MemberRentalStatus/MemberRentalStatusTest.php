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

    /**
     * @test
     */
    public function パターン項番1()
    {
        $id = '1';
        $json = json_encode([
            'tolId' => $id,
        ]);

        $url = '/member/rental/status';
        $actual = $this->postWithAuth($url, $json);
        $this->actualDifference($id, $actual);
    }

    /**
     * @test
     */
    public function パターン項番2()
    {
        $id = '2';
        $json = json_encode([
            'tolId' => $id,
        ]);

        $url = '/member/rental/status';
        $actual = $this->postWithAuth($url, $json);
        $this->actualDifference($id, $actual);
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