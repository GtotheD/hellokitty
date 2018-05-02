<?php

namespace App\Repositories;

use App\Exceptions\AgeLimitException;
use App\Model\People;
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

    const HIMO_REQUEAST_MAX = 200;
    const HIMO_REQUEAST_PER_ONCE = 20;

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
            'work_title',
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
            $himoResult = $himo->crosswork([$workId], $idType)->get(true);
            if (empty($himoResult['results']['rows'])) {
                throw new NoContentsException();
            }
            // インサートしたものを取得するため条件を再設定
            $workId = $himoResult['results']['rows'][0]['work_id'];
            $this->work->setConditionByWorkId($workId);
            if ($this->work->count() == 0) {
                $this->insertWorkData($himoResult, $this->work);
            }
        }

        if (empty($selectColumns)) {
            $response = (array)$this->work->toCamel(['id'])->getOne();
        } else {
            $response = (array)$this->work->selectCamel($selectColumns)->getOne();
        }
        $response = $this->formatAddOtherData($response);

        if ($response['adultFlg']) {
            throw new AgeLimitException();
        }

        return $response;
    }

    /**
     *
     *
     * @param $workIds
     * @return null
     *
     * @throws NoContentsException
     */
    public function getWorkList($workIds, $selectColumns = null)
    {
        $himo = new HimoRepository();
        $workIdsExistedArray = [];
        $workIdsExisted = $this->work->getWorkIdsIn($workIds)->select('work_id')->get();
        foreach ($workIdsExisted as $workIdsExistedItem) {
            $workIdsExistedArray[] = $workIdsExistedItem->work_id;
        }

        // STEP 3: IDが取得出来なかった場合は全てHimoから新規で詳細情報を取得するためのリストを作成。
        if (!$workIdsExistedArray) {
            $workIdsNew = $workIds;
        } else {
            $workIdsNew = array_values(array_diff($workIds, $workIdsExistedArray));
        }
        // STEP 4: 既存データから取ってこれなかったものをHimoから取得し格納する。
        // Get data by list workIds and return
        if ($workIdsNew) {
            $max = self::HIMO_REQUEAST_MAX;
            $limitOnceMax = self::HIMO_REQUEAST_PER_ONCE;
            $loopCount = 0;
            $limitOnce = 0;
            $mergeWorks = [];
            // 10件ずつ問い合わせ。アプリ上で何件だすかで制御を変更する。
            foreach ($workIdsNew as $workId) {
                $loopCount++;
                $limitOnce++;
                $getList[] = $workId;
                if ($limitOnce >= $limitOnceMax ||
                    (count($workIdsNew) - $loopCount) === 0 ||
                    $loopCount == $max
                ) {
                    $himoResult = $himo->crosswork($getList)->get();
                    // Himoから取得できなかった場合はスキップする
                    if (!empty($himoResult)) {
                        $insertResult = $this->insertWorkData($himoResult);
                    }
                    // リセットをかける
                    $limitOnce = 0;
                    $getList = [];
                    if($loopCount == $max) {
                        break;
                    }
                }
            }

        }

        // STEP 5: 条件をセット
        $this->work->getWorkIdsIn($workIds);
        $this->totalCount = $this->work->count();
        if (!$this->totalCount) {
            return null;
        }

        if (empty($selectColumns)) {
            $workArray = $this->work->toCamel(['id'])->get();
        } else {
            $workArray = $this->work->selectCamel($selectColumns)->get();
        }

        // productsからとってくるが、仮データ
        foreach ($workArray as $workItem) {
            $row = (array)$workItem;
            $response['rows'][] = $this->formatAddOtherData($row);
        }
        return $response;
    }

    public function formatAddOtherData($response, $addSaleTypeHas = true, $product = null)
    {
        // productsからとってくるが、仮データ
        $productModel = new Product();
        $productRepository = new  ProductRepository();

        $people = new People;
        $roleId = '';
        $response['supplement'] = '';
        if (empty($product)) {
            $product = (array)$productModel->setConditionByWorkIdNewestProduct($response['workId'], $this->saleType)->toCamel()->getOne();
        }
        if (!empty($product)) {
            // add docs
            // get First Docs
            if(array_key_exists('docs', $product)) {
                $docs = json_decode($product['docs'], true);
                if(!empty($docs)) {
                    foreach ($docs as $doc) {
                        $response['docText'] = $doc['doc_text'];
                    }
                }
            }
            // add supplement
            if ($product['msdbItem'] === 'game') {
                $response['supplement'] = $product['gameModelName'];
            } else {
                if ($product['msdbItem'] === 'video') {
                    $roleId = 'EXT0000000UH';
                } elseif ($product['msdbItem'] === 'book') {
                    $roleId = 'EXT00000BWU9';
                } elseif ($product['msdbItem'] === 'audio') {
                    $roleId = 'EXT00000000D';
                }
                $person = $people->setConditionByRoleId($product['productUniqueId'], $roleId)->getOne();
                if (!empty($person)) {
                    $response['supplement'] = $person->person_name;
                }
            }
            if (!empty($product)) {
                $response['makerName'] = $product['makerName'];
            } else {
                $response['makerName'] = '';
            }
            $response['saleType'] = $productRepository->convertProductTypeToStr($product['productTypeId']);
        }
        $response['newFlg'] = newFlg($response['saleStartDate']);
        $response['adultFlg'] = ($response['adultFlg'] === '1') ? true : false;
        $response['itemType'] = $this->convertWorkTypeIdToStr($response['workTypeId']);
        if ($addSaleTypeHas) {
            $response['saleTypeHas'] = [
                'sell' => ($productModel->setConditionByWorkIdSaleType($response['workId'], 'sell')->count() > 0) ? true: false,
                'rental' => ($productModel->setConditionByWorkIdSaleType($response['workId'], 'rental')->count() > 0) ? true: false
            ];
        }
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
    public function insertWorkData($himoResult)
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

            $this->work->insertBulk($workData, $insertWorkId);
            $productModel->insertBulk($productData);
            $peopleModel->insertBulk($peopleData);

            DB::commit();
            return $insertWorkId;
        } catch (\Exception $exception) {
            \Log::error("Error while update work. Error message:{$exception->getMessage()} Line: {$exception->getLine()}");
            DB::rollback();
            throw new $exception;
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
            'id' => $keyword //dummy data
        ];

        $result = [
            'hasNext' => false,
            'totalCount' => 0,
            'counts' => [
                'dvd' => 0,
                'cd' => 0,
                'book' => 0,
                'game' => 0
            ],
            'rows' => []
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

            //check counts of all itemType
            $ItemTypesCheck = ['cd', 'dvd', 'book', 'game'];
            $dataCounts = $data;
            if (in_array(strtolower($itemType), $ItemTypesCheck)) {
                $params['itemType'] = 'all';
                $params['responseLevel'] = '1';
                $himoRepository->setLimit(1);
                $himoRepository->setOffset(0);

                $dataCounts = $himoRepository->searchCrossworks($params, $sort)->get();

            }
            if (!empty($dataCounts['results']['facets']['msdb_item'])) {
                foreach ($dataCounts['results']['facets']['msdb_item'] as $value) {
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
                $base = $this->format($row);
                $itemType = $this->convertWorkTypeIdToStr($base['work_type_id']);
                $saleTypeHas = $this->parseFromArray($row['products'], $itemType);
                $result['rows'][] = [
                    'workId' => $base['work_id'],
                    'urlCd' => $base['url_cd'],
                    'cccWorkCd' => $base['ccc_work_cd'],
                    'workTitle' => $base['work_title'],
                    'jacketL' => $base['jacket_l'],
                    'newFlg' => newFlg($base['sale_start_date']),
                    'adultFlg' => ($base['adult_flg'] === '1') ? true : false,
                    'itemType' => $itemType,
                    'saleType' => !empty($base['saleType']) ? $base['saleType'] : '',
                    'supplement' => $saleTypeHas['supplement'],
                    'saleStartDate' => $row['sale_start_date'],
                    'saleStartDateSell' => $row['sale_start_date_sell'],
                    'saleStartDateRental' => $row['sale_start_date_sell'],
                    'saleTypeHas' => [
                        'sell' => $saleTypeHas['sell'],
                        'rental' => $saleTypeHas['rental'],
                    ]
                ];
            }

        }

        return $result;
    }

    public function parseFromArray($products, $itemType)
    {
        $sell = false;
        $rental = true;
        $supplement = '';
        foreach ($products as $product) {

            if($product['service_id'] === 'tol') {
                if($product['product_type_id'] === '1') {
                    $sell = true;
                } else if($product['product_type_id'] === '2') {
                    $rental = true;
                }
                if ($itemType === 'game') {
                    $supplement = $product['game_model_name'];
                } else {
                    if ($itemType === 'dvd') {
                        $roleId = 'EXT0000000UH';
                    } elseif ($itemType === 'book') {
                        $roleId = 'EXT00000BWU9';
                    } elseif ($itemType === 'cd') {
                        $roleId = 'EXT00000000D';
                    }
                    $supplement = $this->parseSupplement($product['people'], $roleId);
                }

            }
        }
        return [
            'sell' => $sell,
            'rental' => $rental,
            'supplement' => $supplement
        ];

    }

    public function parseSupplement($people, $roleId)
    {
        foreach ($people as $person) {
            if($person['role_id'] === $roleId) {
                return $person['person_name'];
            }
        }
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
                    'workId' => isset($base['workId']) ? $base['workId'] : '',
                    'urlCd' => isset($base['urlCd']) ? $base['urlCd'] : '',
                    'cccWorkCd' => isset($base['cccWorkCd']) ? $base['cccWorkCd'] : '',
                    'workTitle' => isset($base['workTitle']) ? $base['workTitle'] : '',
                    'newFlg' => isset($base['newFlg']) ? $base['newFlg'] : false,
                    'jacketL' => isset($base['jacketL']) ? $base['jacketL'] : '',
                    'supplement' => isset($base['supplement']) ? $base['supplement'] : '',
                    'saleType' => isset($base['saleType']) ? $base['saleType'] : '',
                    'itemType' => isset($base['itemType']) ? $base['itemType'] : '',
                    'adultFlg' => isset($base['adultFlg']) ? $base['adultFlg'] : false,
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
        if (empty($himoResult['results']['rows'])) {
            return null;

        }
        $result['workId'] = $himoResult['results']['rows'][0]['work_id'];
        $result['itemType'] = $workRepository->convertWorkTypeIdToStr($himoResult['results']['rows'][0]['work_type_id']);
        return $result;
    }

    public function format($row, $isNarrow = false)
    {
        // Initial key value.
        $base = [
            'ccc_work_cd' => '',
            'url_cd' => '',
        ];
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
                $base['itemType'] = 'cd';
                break;
            case self::WORK_TYPE_DVD:
                $base['itemType'] = 'dvd';
                break;
            case self::WORK_TYPE_BOOK:
                $base['itemType'] = 'book';
                break;
            case self::WORK_TYPE_GAME:
                $base['itemType'] = 'game';
                break;
        }
        return $base;
    }

    public function convertWorkTypeIdToStr($workTypeId)
    {
        $itemType = null;
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
     * API GET: /people/{personId}
     *
     * @param $personId
     * @param null $sort
     * @param null $saleType
     *
     * @return array|null
     *
     * @throws NoContentsException
     */
    public function person($personId, $sort = null, $itemType = null)
    {
        $himoRepository = new HimoRepository();

        $params = [
            'personId' => $personId,
            'saleType' => $this->saleType,
            'itemType' => $itemType,
            'responseLevel' => 1,
            'limit' => 100,
            'id' => $personId,//dummy data
            'api' => 'crossworks',//dummy data
        ];
        $himoRepository->setLimit(100);
        $data = $himoRepository->searchCrossworks($params, $sort)->get();
        if (empty($data['status']) || $data['status'] != '200' || empty($data['results']['total'])) {
            throw new NoContentsException();
        }
        foreach ($data['results']['rows'] as $row) {
            $workList[] = $row['work_id'];
        }

        $hoge = $this->getWorkList($workList);
        $this->work->getWorkWithProductIdsIn($workList, $this->saleType);
        $this->totalCount = $this->work->count();
        $works = $this->work->selectCamel($this->selectColumn())->get($this->limit, $this->offset);
        if (count($works) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }

        // STEP 7:フォーマットを変更して返却
        $workItems = [];
        foreach ($works as $workItem) {
            $workItem = (array)$workItem;
            $formatedItem = $this->formatAddOtherData($workItem, false, $workItem   );
            foreach ($formatedItem as $key => $value) {
                if (in_array($key,$this->outputColumn())) {
                    $formatedItemSelectColumn[$key] = $value;
                }
            }
            $workItems[] = $formatedItemSelectColumn;
        }


        return $workItems;
    }
    private function outputColumn()
    {
        return [
            'workId',
            'urlCd',
            'cccWorkCd',
            'workTitle',
            'newFlg',
            'jacketL',
            'supplement',
            'saleType',
            'itemType',
            'adultFlg'
        ];
    }

    private function selectColumn()
    {
        return [
            't1.work_id',
            'work_type_id',
            'work_title',
            'rating_id',
            'big_genre_id',
            'url_cd',
            'ccc_work_cd',
            't1.jacket_l',
            't2.sale_start_date',
            't2.product_type_id',
            'product_unique_id',
            'product_name',
            'maker_name',
            'game_model_name',
            'adult_flg',
            'msdb_item'
        ];
    }
}
