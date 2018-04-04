<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Log;
use App\Model\Work;
use App\Model\Product;
use App\Exceptions\NoContentsException;

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
     * @return mixed
     */
    public function getHasNext()
    {
        return $this->hasNext;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return (int)$this->limit;
    }

    /**
     * @return mixed
     */
    public function getOffset()
    {
        return (int)$this->offset;
    }

    /**
     * @return mixed
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @return Array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @param mixed $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }


    public function get($workId)
    {
        $work = new Work();
        $productRepository = new ProductRepository();
        $work->setConditionByWorkId($workId);
        if ($work->count() == 0) {
            $himo = new HimoRepository();
            $himoResult = $himo->crosswork($workId)->get();
            if(!$himoResult['results']['rows']) {
                throw new NoContentsException();
            }
            foreach ($himoResult['results']['rows'] as $row) {
                $base =[];
                $base = $this->format($row);
                $insertResult = $work->insert($base);
                foreach ($row['products'] as $product) {
                    // インサートの実行
                    $productRepository->insert($row['work_id'], $product);
                }
                // インサートしたものを取得するため条件を再設定
                $work->setConditionByWorkId($workId);
            }
        }
//        $response = (array)$work->toCamel()->getOne();
        $response = (array)$work->getOne();
        // productsからとってくるが、仮データ
        $productModel = new Product();
        $product = $productModel->setConditionByWorkIdNewestProduct($workId)->getOne();
        dd($product);
        $response['supplement'] = 'aaaa';
        $response['makerName'] = $product['maker_name'];
        $response['bookReleaseMonth'] = $product['maker_name'];
        $response['newFlg'] = true;

        return $response;
    }



    private function format($row)
    {
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
        return array_merge($base, $additional);
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