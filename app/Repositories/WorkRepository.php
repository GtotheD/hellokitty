<?php

namespace App\Repositories;

use App\Model\Work;
use App\Model\Product;
use App\Exceptions\NoContentsException;
use DB;

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
    protected $saleType;
    protected $ageLimitCheck;

    const WORK_TYPE_CD = 1;
    const WORK_TYPE_DVD = 2;
    const WORK_TYPE_BOOK = 3;
    const WORK_TYPE_GAME = 4;

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

    /**
     * @param mixed $saleType
     */
    public function setSaleType($saleType)
    {
        $this->saleType = $saleType;
    }

    /**
     * @param mixed $ageLimitCheck
     */
    public function setAgeLimitCheck($ageLimitCheck)
    {
        $this->ageLimitCheck = $ageLimitCheck;
    }

    public function get($workId)
    {
        $work = new Work();
        // check workId is array
        // Get data by list workIds and return
        if(is_array($workId)) {
            $himo = new HimoRepository();
            $himoResult = $himo->crosswork($workId)->get();
            if(!$himoResult['results']['rows']) {
                throw new NoContentsException();
            }
            // インサートしたものを取得するため条件を再設定
            return $this->insert($himoResult, $work);
        }

        // Get data and return response for GET: work/{workId}
        $work->setConditionByWorkId($workId);
        if ($work->count() == 0) {
            $himo = new HimoRepository();
            $himoResult = $himo->crosswork($workId)->get();
            if(!$himoResult['results']['rows']) {
                throw new NoContentsException();
            }
            // インサートしたものを取得するため条件を再設定
            $this->insert($himoResult, $work);
            $work->setConditionByWorkId($workId);
        }
        $response = (array)$work->toCamel(['id'])->getOne();

        // productsからとってくるが、仮データ
        $productModel = new Product();
        $product = (array)$productModel->setConditionByWorkIdNewestProduct($workId, $this->saleType)->getOne();
        // TODO: peopleができてから実装する。
        $response['supplement'] = '（仮）監督・著者・アーティスト・機種';
        $response['makerName'] = $product['maker_name'];
        $response['newFlg'] = $this->newLabel($response['saleStartDate']);
        $response['adultFlg'] = ($response['adultFlg'] === '1')? true: false ;
        $response['itemType'] = $this->convertWorkTypeIdToStr($response['workTypeId']);
        $response['saleType'] = $this->saleType;
        $response['saleTypeHas'] = [
            'sell' => ($productModel->setConditionByWorkIdSaleType($workId, 'sell')->count() > 0)?: true,
            'rental' => ($productModel->setConditionByWorkIdSaleType($workId, 'rental')->count() > 0)?: true
        ];

        return $response;
    }

    public function insert($himoResult, $work) {

        $productRepository = new ProductRepository();
        $peopleRepository = new PeopleRepository();
        // Create transaction for insert multiple tables

        // Create transaction for insert multiple tables
        DB::beginTransaction();
        try {
            foreach ($himoResult['results']['rows'] as $row) {
                $base = [];
                $base = $this->format($row);
                $insertWorkId[] = $row['work_id'];
                $insertResult = $work->insert($base);
                foreach ($row['products'] as $product) {
                    if(
                        $product['service_id'] === 'tol'
                        && substr($product['item_cd'],0, 2) !== '01'
                    ) {
                        // インサートの実行
                        $productRepository->insert($row['work_id'], $product);
                        // Insert people
                        if ($people = array_get($product, 'people')) {
                            foreach ($people as $person) {
                                $peopleRepository->insert($product['id'], $person);
                            }
                        }
                    }
                }
                DB::commit();
                return $insertWorkId;
            }
        } catch (\Exception $exception) {
            \Log::error("Error while update work. Error message: {$exception->getMessage()} Line: {$exception->getLine()}");
            DB::rollback();
            throw new NoContentsException();
        }

    }


    // 一ヶ月前まではNewフラグ
    public function newLabel($saleStartDate)
    {
        $end = date('Y-m-d', strtotime('-1 month', time()));
        if ($end < $saleStartDate) {
            return true;
        }
        return false;
    }

    private function format($row)
    {
        $base = [];
        foreach ($row['ids'] as $idItem) {
            // HiMO作品ID
            if($idItem['id_type'] === '0103') {
                $base['ccc_work_cd'] = $idItem['id_value'];
            // URLコード
            } else if ($idItem['id_type'] === '0105') {
                $base['url_cd'] = $idItem['id_value'];
            }
        }

        // ベースのデータの整形
        $base['work_id'] = $row['work_id'];
        $base['work_type_id'] = $row['work_type_id'];
        $base['work_format_id'] = $row['work_format_id'];
        $base['work_format_name'] = $row['work_format_name'];
        $base['work_title'] = $row['work_title'];
        $base['work_title_orig'] = $row['work_title_orig'];
        $base['copyright'] = $row['work_copyright'];
        $base['jacket_l'] = $this->trimImageTag($row['jacket_l']);
        $base['sale_start_date'] = $row['sale_start_date'];
        $base['big_genre_id'] = $row['genres'][0]['big_genre_id'];
        $base['big_genre_name'] = $row['genres'][0]['big_genre_name'];
        $base['medium_genre_id'] = $row['genres'][0]['medium_genre_id'];
        $base['medium_genre_name'] = $row['genres'][0]['medium_genre_name'];
        $base['small_genre_id'] = $row['genres'][0]['small_genre_id'];
        $base['small_genre_name'] = $row['genres'][0]['small_genre_name'];
        $base['rating_id'] = $row['rating_id'];
        $base['rating_name'] = $row['rating_name'];
        $base['adult_flg'] = $row['adult_flg'];
        $base['created_year'] = $row['created_year'];
        $base['created_countries'] = $row['created_countries'];
        $base['book_series_name'] = $row['book_series_name'];
        // アイテム種別毎に整形フォーマットを変更できるように
        switch ($row['work_type_id']) {
            case self::WORK_TYPE_CD:
                $base['doc_text'] = $this->cdFormat($row);
                $base['itemType'] = 'cd';
                break;
            case self::WORK_TYPE_DVD:
                $base['doc_text'] = $this->dvdFormat($row);
                $base['itemType'] = 'dvd';
                break;
            case self::WORK_TYPE_BOOK:
                $base['doc_text'] = $this->bookFormat($row);
                $base['itemType'] = 'book';
                break;
            case self::WORK_TYPE_GAME:
                $base['doc_text'] = $this->gameFormat($row);
                $base['itemType'] = 'game';
                break;
        }
        return $base;
    }

    public function convertWorkTypeIdToStr($workTypeId)
    {
        switch ($workTypeId) {
            case self::WORK_TYPE_CD:
                $itemType = 'cd';
                break;
            case self::WORK_TYPE_DVD:
                $itemType = 'dvd';
                break;
            case self::WORK_TYPE_BOOK:
                $itemType = 'book';
                break;
            case self::WORK_TYPE_GAME:
                $itemType = 'game';
                break;
        }
        return $itemType;
    }
    public function trimImageTag($data)
    {
        $data = trim(preg_replace('/<.*>/', '', $data));
        $data = preg_replace('/^\/\//', 'https://cdn.', $data);
        return $data;
    }
    private function dvdFormat($row)
    {
        return $row['docs'][0]['doc_text'];
    }
    private function cdFormat($row)
    {
        return '';
    }
    private function bookFormat($row)
    {
        return '';
    }
    private function gameFormat($row)
    {
        return $row['docs'][0]['doc_text'];
    }

    public function checkAgeLimit($ratingId, $bigGenreId)
    {
        $map = [
            [
                'ageLimit' => 18,
                'ratingId' => 'EXT0000000YB',
                'bigGenreId' => 'EXT0000002G9',
                'mediumGenreId' => null,
                'smallGenreId' => null,
            ],
            [
                'ageLimit' => 15,
                'ratingId' => 'EXT0000001AV',
                'bigGenreId' => 'EXT0000002G9',
                'mediumGenreId' => null,
                'smallGenreId' => null
            ],
            [
                'ageLimit' => 18,
                'ratingId' => 'EXT0000000YB',
                'bigGenreId' => 'EXT0000000YC',
                'mediumGenreId' => null,
                'smallGenreId' => null
        ]];
        foreach($map as $item) {
            if($item['ratingId'] === $ratingId && $item['bigGenreId'] === $bigGenreId) {
                return true;
            }
        }
        return false;
    }
}