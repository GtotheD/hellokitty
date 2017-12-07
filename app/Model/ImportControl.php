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

    public function setCondtionFindFilename($goodsType, $saleType, $fileName)
    {
        $this->dbObject = DB::table($this->table)
            ->where([
                'goods_type' => $goodsType,
                'sale_type' => $saleType,
                'file_name' => $fileName
            ]);
        return $this;
    }
    public function upInsertByCondition($goodsType, $saleType, $fileName, $timestamp)
    {
        $this->setCondtionFindFilename($goodsType, $saleType, $fileName);
        if ($this->count() === 0) {
            return DB::table($this->table)
                ->insert([
                    'goods_type' => $goodsType,
                    'sale_type' => $saleType,
                    'file_name' => $fileName,
                    'unix_timestamp' => $timestamp,
                    'created_at' => DB::raw('now()'),
                    'updated_at' => DB::raw('now()')
                ]);

        } else {
            return DB::table($this->table)
                ->where([
                    'goods_type' => $goodsType,
                    'sale_type' => $saleType,
                    'file_name' => $fileName,
                ])
                ->update([
                    'unix_timestamp' => $timestamp,
                    'updated_at' => DB::raw('now()')
                ]);
        }
    }

}