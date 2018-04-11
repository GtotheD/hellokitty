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
    private $work;

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

        $this->work = new Work();
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
    public function getNarrowColumns($workId)
    {
        $columns =[
            'work_id',
            'work_type_id',
            'rating_id',
            'big_genre_id',
            'url_cd',
            'ccc_work_cd',
            'jacket_l',
            'sale_start_date',
            'adult_flg'
        ];
        return $this->get($workId, $columns);
    }

    public function get($workId, $selectColumns = null)
    {
        // check workId is array
        // Get data by list workIds and return
        if(is_array($workId)) {
            $himo = new HimoRepository();
            $himoResult = $himo->crosswork($workId)->get();
            if(!$himoResult['results']['rows']) {
                throw new NoContentsException();
            }
            // インサートしたものを取得するため条件を再設定
            return $this->insert($himoResult);
        }

        // Get data and return response for GET: work/{workId}
        $this->work->setConditionByWorkId($workId);
        if ($this->work->count() == 0) {
            $himo = new HimoRepository();
            $himoResult = $himo->crosswork($workId)->get();
            if(!$himoResult['results']['rows']) {
                throw new NoContentsException();
            }
            // インサートしたものを取得するため条件を再設定
            $this->insert($himoResult);
            $this->work->setConditionByWorkId($workId);
        }
        if (empty($selectColumns)) {
            $response = (array)$this->work->toCamel(['id'])->getOne();
        } else {
            $response = (array)$this->work->selectCamel($selectColumns)->getOne();
        }

        // productsからとってくるが、仮データ
        $productModel = new Product();
        $product = (array)$productModel->setConditionByWorkIdNewestProduct($workId, $this->saleType)->getOne();
        // TODO: peopleができてから実装する。
        $response['supplement'] = '（仮）監督・著者・アーティスト・機種';
        $response['makerName'] = $product['maker_name'];
        $response['newFlg'] = newFlg($response['saleStartDate']);
        $response['adultFlg'] = ($response['adultFlg'] === '1')? true: false ;
        $response['itemType'] = $this->convertWorkTypeIdToStr($response['workTypeId']);
        $response['saleType'] = $this->saleType;
        $response['saleTypeHas'] = [
            'sell' => ($productModel->setConditionByWorkIdSaleType($workId, 'sell')->count() > 0)?: true,
            'rental' => ($productModel->setConditionByWorkIdSaleType($workId, 'rental')->count() > 0)?: true
        ];

        return $response;
    }

    public function insert($himoResult) {

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
                $insertResult = $this->work->insert($base);
                foreach ($row['products'] as $product) {
                    if($product['service_id'] === 'tol') {
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

    public function searchKeyword($keyword, $sort = null, $itemType = null, $periodType = null, $adultFlg = null)
    {
        $himoRepository = new HimoRepository('asc', $this->offset, $this->limit);

        $params = [
            'keyword' => $keyword,
            'itemType' => $itemType,
            'periodType' => $periodType,
            'adultFlg' => $adultFlg,
            'api' => 'search',//dummy data
            'id' => 'aaa' //dummy data
        ];

        $data = $himoRepository->searchCrossworks($params, $sort)->get();

        if (!empty($data['status']) && $data['status'] == '200') {
            if (count($data['results']['rows']) + $this->offset < $data['results']['total']) {
                $this->hasNext = true;
            } else {
                $this->hasNext = false;
            }

            $result = [
                'hasNext' => $this->hasNext,
                'totalCount' => $data['results']['total'],
                'counts' => [
                    'dvd' => 0,
                    'cd' => 0,
                    'book' => 0,
                    'game' => 0
                ],
                'rows' => []
            ];

            if (!empty($data['results']['facets']['msdb_item'])) {
                foreach ($data['results']['facets']['msdb_item'] as $value) {
                    switch ($value['key']) {
                        case 'video':
                            $result['counts']['dvd'] = $value['count'];
                            break;
                        case 'audio':
                            $result['counts']['cd'] = $value['count'];
                            break;
                        case 'book':
                            $result['counts']['book'] = $value['count'];
                            break;
                        case 'game':
                            $result['counts']['game'] = $value['count'];
                            break;
                    }

                }
            }

            foreach ($data['results']['rows'] as $row) {
                $this->setSaleType('rental');
                $base = $this->get($row['work_id']);

                $result['rows'][] = [
                    'workId' => $base['workId'],
                    'urlCd' => $base['urlCd'],
                    'cccWorkCd' => $base['cccWorkCd'],
                    'workTitle' => $base['workTitle'],
                    'newFlg' => $base['newFlg'],
                    'jacketL' => $base['jacketL'],
                    'supplement' => $base['supplement'],
                    'saleType' => $base['saleType'],
                    'itemType' => $base['itemType'],
                    'saleTypeHas' => [
                        'sell' => $base['saleTypeHas']['sell'],
                        'rental' => $base['saleTypeHas']['rental'],
                    ],
                    'adultFlg' => $base['adultFlg'],
                ];
            }

            if (count($result['rows']) > 0) {
                return $result;
            }
        }

        return null;
    }

    public function genre($genreId, $sort = null, $saleType = null)
    {
        $himoRepository = new HimoRepository('asc', $this->offset, $this->limit);

        $params = [
            'genreId' => $genreId,
            'saleType' => $saleType,
            'api' => 'genre',//dummy data
            'id' => $genreId //dummy data
        ];

        $data = $himoRepository->searchCrossworks($params, $sort)->get();

        if (!empty($data['status']) && $data['status'] == '200') {
            if (count($data['results']['rows']) + $this->offset < $data['results']['total']) {
                $this->hasNext = true;
            } else {
                $this->hasNext = false;
            }

            $result = [
                'hasNext' => $this->hasNext,
                'totalCount' => $data['results']['total'],
                'rows' => []
            ];


            foreach ($data['results']['rows'] as $row) {
                $this->setSaleType('rental');
                $base = $this->get($row['work_id']);

                $result['rows'][] = [
                    'workId' => $base['workId'],
                    'urlCd' => $base['urlCd'],
                    'cccWorkCd' => $base['cccWorkCd'],
                    'workTitle' => $base['workTitle'],
                    'newFlg' => $base['newFlg'],
                    'jacketL' => $base['jacketL'],
                    'supplement' => $base['supplement'],
                    'saleType' => $base['saleType'],
                    'itemType' => $base['itemType'],
                    'adultFlg' => $base['adultFlg'],
                ];
            }

            if (count($result['rows']) > 0) {
                return $result;
            }
        }

        return null;
    }

    public function convert($idType, $id)
    {
        $idCode = null;
        switch ($idType) {
            case 'workId':
                $idCode = '0102';
                break;
            case 'cccWorkCd':
                $idCode = '0103';
                break;
            case 'urlCd':
                $idCode = '0105';
                break;
            case 'rentalProductId':
                $idCode = '0206';
                break;
        }

        $himoRepository = new HimoRepository();
        return  $himoRepository->crosswork([$id], $idCode, '1');
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
        $base['jacket_l'] = trimImageTag($row['jacket_l']);
        $base['scene_l'] = $this->sceneFormat($row['scene_l']);
        $base['sale_start_date'] = $row['sale_start_date'];
        $base['big_genre_id'] = $row['genres'][0]['big_genre_id'];
        $base['big_genre_name'] = $row['genres'][0]['big_genre_name'];
        $base['medium_genre_id'] = $row['genres'][0]['medium_genre_id'];
        $base['medium_genre_name'] = $row['genres'][0]['medium_genre_name'];
        $base['small_genre_id'] = $row['genres'][0]['small_genre_id'];
        $base['small_genre_name'] = $row['genres'][0]['small_genre_name'];
        $base['filmarks_id'] = $this->filmarksIdFormat($row);
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

    private function sceneFormat($data)
    {
        $result = [];
        foreach ($data as $image) {
            $result[] = trimImageTag($image['url']);
        }
        return json_encode($result, JSON_UNESCAPED_SLASHES);
    }

    private function filmarksIdFormat($row)
    {
        if (!empty($row['filmarks_id'][0])) {
            return $row['filmarks_id'][0];
        }
        return null;
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