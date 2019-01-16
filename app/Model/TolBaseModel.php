<?php
namespace App\Model;

use League\Csv\Reader;
use App\Clients\TolClient;

class TolBaseModel
{
    public $tolClient;

    function __construct($memId)
    {
        $this->tolClient = new TolClient($memId);
    }

    /**
     * CSVを受け取ってコレクション配列を返す
     * @param $header
     * @param $csv
     * @return \Illuminate\Support\Collection
     */
    public function getCollectionFromCSV($header, $csv)
    {
        // 文字コードがSJISでくるので変換する。
//        $csv = mb_convert_encoding(urldecode($csv), "UTF-8", "SJIS");
        $reader = Reader::createFromString($csv);
        $results = $reader->getRecords($header);
        foreach ($results as $row) {
            $csvArray[] = $row;
        }
        return collect($csvArray);
    }
}