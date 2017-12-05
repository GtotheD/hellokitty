<?php
namespace App\Repositories;

use League\Csv\Reader;

class FixtureRepository
{
    public function getGenreMap()
    {
        $reader = Reader::createFromPath($this->getConfigPath().'/maps.csv', 'r')
            ->setHeaderOffset(0);
        $records = $reader->getRecords();
        foreach ($records as $record) {
            $maps[$record['HimoGenreCode']] = $record;
        }
        return $maps;
    }
    public function getConfigPath() {
        return base_path(). '/config';
    }
}