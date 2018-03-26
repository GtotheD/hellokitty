<?php

namespace App\Repositories;

use App\Model\Work;
use Illuminate\Support\Facades\Log;

/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/13
 * Time: 15:01
 */
class WorkRepository
{

    protected $sort;
    protected $offset;
    protected $limit;
    protected $apiHost;
    protected $apiKey;

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        $this->sort = $sort;
        $this->offset = $offset;
        $this->limit = $limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }


    public function get($workId)
    {
        $base =[];
        $work = new Work();
        $work->setConditionByWorkId($workId);
        if ($work->count() == 0) {
            $himo = new HimoRepository();
            $himoResult = $himo->detail($workId)->get();
            foreach ($himoResult['results']['rows'] as $row) {
                // ベースのデータの整形
                $base['work_id'] = $row['work_id'];
                $base['work_type_id'] = $row['work_type_id'];
                $base['work_format_id'] = $row['work_format_id'];
                $base['work_format_name'] = $row['work_format_name'];
                $base['work_title'] = $row['work_title'];
                $base['work_title_orig'] = $row['work_title_orig'];
                $base['jacket_l'] = $row['jacket_l'];
                $base['sale_start_date'] = $row['sale_start_date'];
                $base['big_genre_id'] = $row['genres'][0]['big_genre_id'];
                $base['big_genre_name'] = $row['genres'][0]['big_genre_name'];
                $base['medium_genre_id'] = $row['genres'][0]['medium_genre_id'];
                $base['medium_genre_name'] = $row['genres'][0]['medium_genre_name'];
                $base['rating_name'] = $row['rating_name'];
                $base['created_year'] = $row['created_year'];
                $base['created_countries'] = $row['created_countries'];
                $base['book_series_name'] = $row['book_series_name'];
                // アイテム種別毎に整形フォーマットを変更できるように
                switch ($row['work_type_id']) {
                    case '1':
                        $additional = $this->cdFormat($row);
                        break;
                    case '2':
                        $additional = $this->dvdFormat($row);
                        break;
                    case '3':
                        $additional = $this->bookFormat($row);
                        break;
                    case '4':
                        $additional = $this->gameFormat($row);
                        break;
                }
                $base = array_merge($base, $additional);
                $insertResult = $work->insert($base);
                // インサートできなかった場合エラー
                // インサートしたものを取得
                $work->setConditionByWorkId($workId);
            }
        }
        return $work->get();
    }

    private function dvdFormat($row)
    {
        $data['doc_text'] = $row['docs'][0]['doc_text'];
        return $data;
    }
    private function cdFormat($row)
    {
        $data['doc_text'] = '';
        return $data;
    }
    private function bookFormat($row)
    {
        $data['doc_text'] = '';
        return $data;
    }
    private function gameFormat($row)
    {
        $data['doc_text'] = $row['docs'][0]['doc_text'];
        return $data;
    }
}