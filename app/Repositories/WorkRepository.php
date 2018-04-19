<?php

namespace App\Repositories;

use App\Model\People;
use App\Model\Work;
use App\Model\Product;
use App\Exceptions\NoContentsException;
use DB;
use App\Repositories\WorkRepository;

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
        $columns = [
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

    public function get($workId, $selectColumns = null, $idType = '0102')
    {
        $product = new Product;
        $response = [];
        $productResult = null;
        switch ($idType) {
            case '0105':
                $productResult = (array)$this->work->setConditionByUrlCd($workId)->getOne();
                break;
            case '0205':
                $productResult = (array)$product->setConditionByJan($workId)->getOne();
                break;
            case '0206':
                $productResult = (array)$product->setConditionByRentalProductCd($workId)->getOne();
                break;
        }
        if ($productResult) {
            $workId = $productResult['work_id'];
        }
        $this->work->setConditionByWorkId($workId);
        if ($this->work->count() == 0) {
            $himo = new HimoRepository();
            $himoResult = $himo->crosswork([$workId], $idType)->get(true, 'POST');
            // recheck

            if (empty($himoResult['results']['rows'])) {
                throw new NoContentsException();
            }
            $workId = $himoResult['results']['rows'][0]['work_id'];
            $this->work->setConditionByWorkId($workId);
            if ($this->work->count() == 0) {
                $this->insertWorkRData($himoResult, $this->work);
            }
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
        if (!empty($product)) {
            $response['makerName'] = $product['maker_name'];
        } else {
            $response['makerName'] = '';
        }
        $response['newFlg'] = newFlg($response['saleStartDate']);
        $response['adultFlg'] = ($response['adultFlg'] === '1') ? true : false;
        $response['itemType'] = $this->convertWorkTypeIdToStr($response['workTypeId']);
        $response['saleType'] = $this->saleType;
        $response['saleTypeHas'] = [
            'sell' => ($productModel->setConditionByWorkIdSaleType($workId, 'sell')->count() > 0) ?: true,
            'rental' => ($productModel->setConditionByWorkIdSaleType($workId, 'rental')->count() > 0) ?: true
        ];

        return $response;
    }

    /**
     * Insert work data and related work data: Product, People
     *
     * @param $himoResult
     * @param $work
     * @return array
     *
     * @throws NoContentsException
     */
    public function insertWorkRData($himoResult, $work)
    {

        $productRepository = new ProductRepository();
        $peopleRepository = new PeopleRepository();

        // Create transaction for insert multiple tables
        DB::beginTransaction();
        try {
            $workData = [];
            $productData = [];
            $peopleData = [];
            foreach ($himoResult['results']['rows'] as $row) {
                $workData[] = $this->format($row);
                $insertWorkId[] = $row['work_id'];
                //$insertResult = $work->insert($base);

                foreach ($row['products'] as $product) {
                    if ($product['service_id'] === 'tol') {
                        // インサートの実行
                        $productData[] = $productRepository->format($row['work_id'], $product);
                        // Insert people
                        if ($people = array_get($product, 'people')) {
                            foreach ($people as $person) {
                                $peopleData[] = $peopleRepository->format($product['id'], $person);
                            }
                        }
                    }
                }
            }

            $productModel = new Product();
            $peopleModel = new People();

            $work->insertBulk($workData);
            $productModel->insertBulk($productData);
            $peopleModel->insertBulk($peopleData);

            DB::commit();
            return $insertWorkId;
        } catch (\Exception $exception) {
            \Log::error("Error while update work. Error message:{$exception->getMessage()} Line: {$exception->getLine()}");
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
            case 'jan':
                $idCode = '0205';
                break;
            case 'rentalProductId':
                $idCode = '0206';
                break;
        }
        $himoRepository = new HimoRepository();
        $workRepository = new WorkRepository();
        $himoResult = $himoRepository->crosswork([$id], $idCode, '1')->get();
        $result['workId'] = $himoResult['results']['rows'][0]['work_id'];
        $result['itemType'] = $workRepository->convertWorkTypeIdToStr($himoResult['results']['rows'][0]['work_type_id']);
        return $result;
    }

    public function format($row, $isNarrow = false)
    {
        $base = [];
        foreach ($row['ids'] as $idItem) {
            // HiMO作品ID
            if ($idItem['id_type'] === '0103') {
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
        if ($isNarrow === false) {
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
        }
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
        if (count($row['docs']) > 0) {
            return $row['docs'][0]['doc_text'];
        }
        return null;
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
        if (count($row['docs']) > 0) {
            return $row['docs'][0]['doc_text'];
        }
        return null;
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
        foreach ($map as $item) {
            if ($item['ratingId'] === $ratingId && $item['bigGenreId'] === $bigGenreId) {
                return true;
            }
        }
        return false;
    }

    /**
     *
     *
     * @param $workIds
     * @return null
     *
     * @throws NoContentsException
     */
    public function getWorkList($workIds)
    {
        $work = new Work();

        // Get data by list workIds and return
        $himo = new HimoRepository();
        $himoResult = $himo->crosswork($workIds)->get();

        if (!$himoResult['results']['rows']) {
            //throw new NoContentsException();
            //Not throw no contents exception, Because the calling side of API calls will be difficult to check for
            return null;
        }
        // インサートしたものを取得するため条件を再設定
        $workIdInserted = $this->insertWorkRData($himoResult, $work);

        $work->getWorkIdsIn($workIdInserted);
        $response['total'] = $work->count();
        if (!$response['total']) {
            //throw new NoContentsException();
            //Not throw no contents exception, Because the calling side of API calls will be difficult to check for
            return null;
        }

        if (empty($selectColumns)) {
            $workArray = $work->toCamel(['id'])->get();
        } else {
            $workArray = $work->selectCamel($selectColumns)->get();
        }

        // productsからとってくるが、仮データ
        foreach ($workArray as $workItem) {
            $row = (array)$workItem;

            $productModel = new Product();
            $product = (array)$productModel->setConditionByWorkIdNewestProduct($workItem->workId, $this->saleType)->getOne();
            // TODO: peopleができてから実装する。
            $row['supplement'] = '（仮）監督・著者・アーティスト・機種';
            $row['makerName'] = $product['maker_name'];
            $row['newFlg'] = newFlg($workItem->saleStartDate);
            $row['adultFlg'] = ($workItem->adultFlg === '1') ? true : false;
            $row['itemType'] = $this->convertWorkTypeIdToStr($workItem->workTypeId);
            $row['saleType'] = $this->saleType;
            $row['saleTypeHas'] = [
                'sell' => ($productModel->setConditionByWorkIdSaleType($workItem->workId, 'sell')->count() > 0) ?: true,
                'rental' => ($productModel->setConditionByWorkIdSaleType($workItem->workId, 'rental')->count() > 0) ?: true
            ];
            $response['rows'][] = $row;
        }
        return $response;
    }

}
