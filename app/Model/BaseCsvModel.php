<?php
namespace App\Model;

use League\Csv\Reader;

class BaseCsvModel
{
    /**
     * CSVを受け取ってコレクション配列を返す
     * @param $header
     * @param $csv
     * @return \Illuminate\Support\Collection
     */
    public function getCollection($header, $csv)
    {
        $reader = Reader::createFromString($csv);
        $results = $reader->getRecords($header);
        foreach ($results as $row) {
            $csvArray[] = $row;
        }
        return collect($csvArray);
    }
}