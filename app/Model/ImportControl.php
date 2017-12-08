<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/27
 * Time: 19:47
 */
class ImportControl extends Model
{
    const TABLE = 'ts_import_control';

    function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function setCondtionFindFilename($fileName)
    {
        $this->dbObject = DB::table($this->table)
            ->where([
                'file_name' => $fileName
            ]);
        return $this;
    }
    public function upInsertByCondition($fileName, $timestamp)
    {
        $this->setCondtionFindFilename($fileName);
        if ($this->count() === 0) {
            return DB::table($this->table)
                ->insert([
                    'file_name' => $fileName,
                    'unix_timestamp' => $timestamp,
                    'created_at' => DB::raw('now()'),
                    'updated_at' => DB::raw('now()')
                ]);

        } else {
            return DB::table($this->table)
                ->where([
                    'file_name' => $fileName
                ])
                ->update([
                    'unix_timestamp' => $timestamp,
                    'updated_at' => DB::raw('now()')
                ]);
        }
    }

}
