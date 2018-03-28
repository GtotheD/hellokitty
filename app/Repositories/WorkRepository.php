<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Log;
use App\Model\Work;
use App\Model\Product;

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
        $product = new Product();
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
                // インサートの実行
                $insertResult = $work->insert($base);
                // インサートの実行
                foreach ($row['products'] as $productItem) {
                    $productBase = [];
                    $productBase['product_unique_id'] = $productItem['id'];
                    $productBase['product_id'] = $productItem['product_id'];
                    $productBase['product_code'] = $productItem['product_code'];
                    $productBase['jan'] = $productItem['jan'];
                    $productBase['ccc_family_cd'] = $productItem['ccc_family_cd'];
                    $productBase['ccc_product_id'] = $productItem['ccc_product_id'];
                    $productBase['rental_product_cd'] = $productItem['rental_product_cd'];
                    $productBase['product_type_name'] = $productItem['product_type_name'];
                    $productBase['sale_start_date'] = $productItem['sale_start_date'];
                    $productBase['service_id'] = $productItem['service_id'];
                    $productBase['service_name'] = $productItem['service_name'];
                    $productBase['msdb_item'] = $productItem['msdb_item'];
                    $productBase['item_cd'] = $productItem['item_cd'];
                    $productBase['item_name'] = $productItem['item_name'];
                    $productBase['disc_info'] = $productItem['disc_info'];
                    $productBase['subtitle'] = $productItem['subtitle'];
                    $productBase['sound_spec'] = $productItem['sound_spec'];
                    $productBase['region_info'] = $productItem['region_info'];
                    $productBase['price_tax_out'] = $productItem['price_tax_out'];
                    $productBase['play_time'] = $productItem['play_time'];
                    $productBase['jacket_l'] = $productItem['jacket_l'];
                    $productBase['sale_start_date'] = $productItem['sale_start_date'];
//                    $productBase['contents'] = $productItem['contents'];
//                    $productBase['privilege'] = $productItem['privilege'];
                    $productBase['best_album_flg'] = $productItem['best_album_flg'];
                    $productBase['maker_name'] = $productItem['maker_name'];
                    $insertResult = $product->insert($row['work_id'], $productBase);
                }
                // インサートできなかった場合エラー
                // インサートしたものを取得
                $work->setConditionByWorkId($workId);
            }
        }
        $response = (array)$work->getOne();
        // productsからとってくるが、仮ダータ
        $response['supplement'] = 'aaaa';
        $response['makerName'] = 'aaaa';
        $response['bookReleaseMonth'] = 'aaaa';
        $response['newFlg'] = true;
        return $response;
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