<?php
namespace App\Repositories;

use League\Csv\Reader;
use League\Csv\Writer;

class FixtureRepository
{
    private $basePath = 'app/';

    public function getGenreMap()
    {
        $reader = Reader::createFromPath(storage_path('app/maps.csv'), 'r')
            ->setHeaderOffset(0);
        $records = $reader->getRecords();
        foreach ($records as $record) {
            $maps[$record['HimoGenreCode']] = $record;
        }
        return $maps;
    }
}