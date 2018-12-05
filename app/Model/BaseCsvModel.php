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
    public function getCollectionFromCSV($header, $csv)
    {
        $reader = Reader::createFromString($csv);
        $results = $reader->getRecords($header);
        foreach ($results as $row) {
            $csvArray[] = $row;
        }
        return collect($csvArray);
    }

    /**
     * CSVを受け取ってコレクション配列を返す
     * @param $csv
     * @param $header
     * @return \Illuminate\Support\Collection
     */
    public function getCollection($csv, $header = null)
    {
        $reader = Reader::createFromString($csv);
        if (!empty($header)) {
            $results = $reader->getRecords($header);

        } else {
            $results = $reader->getRecords();
        }
        foreach ($results as $row) {
            $csvArray[] = $row;
        }
        return collect($csvArray);
    }

}