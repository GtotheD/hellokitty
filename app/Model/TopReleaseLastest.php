<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Repositories\WorkRepository;

class TopReleaseLastest extends Model
{
    const TABLE = 'ts_top_release_lastest';

    function __construct($table = self::TABLE)
    {
        parent::__construct($table);
    }

    public function setConditionByGenreId($genreId)
    {
        $this->dbObject = DB::table($this->table)
            ->where([
                'tap_genre_id' => $genreId,
            ])
            ->orderBy('sort', 'asc');
        return $this;
    }

    /**
     * Insert bulk records
     *
     * @param array $data
     *
     * @return mixed
     */
    public function insertBulk($orders)
    {
        $insertData = [];
        $ignoreColumn = ['id', 'created_at', 'updated_at'];
        $columns = Schema::getColumnListing(self::TABLE);
        foreach ($orders as $key => $row) {
            $insertData[$key]['updated_at'] = date('Y-m-d H:i:s');
            foreach ($columns as $column) {
                if (!in_array($column, $ignoreColumn)) {
                    $insertData[$key][$column] = array_get($row, $column) ?: '';
                }
            }
            $insertData[$key]['created_at'] = date('Y-m-d H:i:s');
        }
        return $this->bulkInsertOnDuplicateKey($insertData);
    }

}
