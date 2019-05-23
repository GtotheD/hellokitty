<?php

namespace App\Repositories;

use App\Model\Product;
use DB;
use Illuminate\Support\Carbon;
use App\Model\HimoReleaseOrder;
use Illuminate\Support\Facades\File;
use App\Repositories\WorkRepository;

class ReleaseCalenderRepository extends BaseRepository
{

    protected $genreId;
    protected $month;
    protected $onlyReleased;
    protected $mediaFormat;
    protected $himoReleaseOrder;

    const HIMO_TAP_RECOMMEND_KEYWORD = 'riricaleinfo';
    const HIMO_TAP_RECOMMEND = 'recommendation';

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        parent::__construct($sort, $offset, $limit);
        $this->himoReleaseOrder = New HimoReleaseOrder();
    }

    /**
     * @param mixed $offset
     */
    public function setGenreId($genreId)
    {
        $this->genreId = $genreId;
    }


    /**
     * @param mixed $month
     */
    public function setMonth($month)
    {
        $this->month = $month;
    }

    /**
     * @param mixed $onlyReleased
     */
    public function setOnlyReleased($onlyReleased)
    {
        $this->onlyReleased = $onlyReleased;
    }

    /**
     * @param mixed $mediaFormat
     */
    public function setMediaFormat($mediaFormat)
    {
        $this->mediaFormat = $mediaFormat;
    }

    /**
     * @param HimoReleaseOrder $himoReleaseOrder
     */
    public function setHimoReleaseOrder(HimoReleaseOrder $himoReleaseOrder)
    {
        $this->himoReleaseOrder = $himoReleaseOrder;
    }

    public function get($isCreateCache = false)
    {
        // 音楽の場合single albumなどを指定する為。
        $mediaFormat = null;
        $workRepository = new WorkRepository();
        $productRepository = new ProductRepository();
        $himoReleaseOrder = new HimoReleaseOrder();
        $productModel = new Product();

        // パラメーターを取得する　未指定の場合は当月
        if ($this->month === 'last') {
            // 前後の日付を取得する際に、Carbonだと月初の取得で日付がおかしくなるため、Carbonではなく下記で対応
            $saleStartMonth = date('Y-m', mktime(0, 0, 0, date('n') - 1, 1, date('Y')));
            $saleStartDateFrom = Carbon::parse($saleStartMonth)->startOfMonth();
            $saleStartDateTo = Carbon::parse($saleStartMonth)->endOfMonth()->addMonth(6);
            $saleStartDateToForDB = Carbon::parse($saleStartMonth)->endOfMonth();

        } else if ($this->month === 'next') {
            // 前後の日付を取得する際に、Carbonだと月初の取得で日付がおかしくなるため、Carbonではなく下記で対応
            $saleStartMonth = date('Y-m', mktime(0, 0, 0, date('n') + 1, 1, date('Y')));
            $saleStartDateFrom = Carbon::parse($saleStartMonth)->startOfMonth();
            $saleStartDateTo = Carbon::parse($saleStartMonth)->endOfMonth()->addMonth(6);
            $saleStartDateToForDB = Carbon::parse($saleStartMonth)->endOfMonth();
        } else {
            $this->month = 'this';
            $saleStartMonth = date('Y-m');
            $saleStartDateFrom = Carbon::now()->startOfMonth();
            $saleStartDateTo = Carbon::now()->endOfMonth()->addMonth(6);
            $saleStartDateToForDB = Carbon::now()->endOfMonth();
        }
        // タグに仕様するフォーマット
        $tagSaleStartDateFrom = $saleStartDateFrom->format('Ym');
        $saleStartDateFrom = $saleStartDateFrom->format('Y-m-d');
        $saleStartDateTo = $saleStartDateTo->format('Y-m-d');
        $saleStartDateToForDB = $saleStartDateToForDB->format('Y-m-d');

        // Himo取得時のソートの指定
        $sortBy = 'auto:desc';

        $mappingData = $this->genreMapping($this->genreId);

        //　APIから呼ばれた場合はキャッシュロジックは通過させない。
        if ($isCreateCache) {
            // ジャンルIDをもとにキャッシュにデータがあるか確認しキャッシュがあればキャッシュからデータを取得する。
            $cacheDataCount = $this->himoReleaseOrder->setConditionByGenreIdAndMonth(
                $this->genreId,
                $saleStartMonth . '-01'
            )->count();
            if (empty($cacheDataCount)) {
                // キャッシュがなければデータを新規で取得する
                $himoRepository = new HimoRepository();
                $params = [
                    'api' => 'release',
                    'id' => $this->month . '_' . $this->genreId . '_0',
                    'genreId' => $this->genreId,
                    'saleStartDateFrom' => $saleStartDateFrom,
                    'saleStartDateTo' => $saleStartDateTo,
                    'productSellRentalFlg' => $mappingData['productSellRentalFlg'],
                    'adultFlg' => $mappingData['adultFlg'],
                    'msdbItem' => $mappingData['msdbItem'],
                    'onlyReleased' => $this->onlyReleased,
                    'sort' => $sortBy
                ];
                // TSUTAYA一押しの処理
                // 日付と種別でTSUTAYA一押し要のタグを生成する
                if ($mappingData['genres'] === self::HIMO_TAP_RECOMMEND) {
                    $worktTag = self::HIMO_TAP_RECOMMEND_KEYWORD .
                        $tagSaleStartDateFrom .
                        substr($productRepository->convertProductTypeToStr($mappingData['productSellRentalFlg']), 0, 1);
                    $params['workTags'] = $worktTag;
                    // TSUTAYA一押しでmsdbitemがcdの場合ミュージックdvdを含めない
                    if ($mappingData['msdbItem'][0] === 'video') {
                        $params['genre'] = implode(' || ', $workRepository::HIMO_SEARCH_VIDEO_GENRE_ID);
                        $params['genre'] = $params['genre'] . ':';
                        // msdbitemにaudioを追加
                        $params['msdbItem'] = ['video', 'audio'];
                    } else if ($mappingData['msdbItem'][0] === 'audio') {
                        // 除外に変換
                        $ignoreVideoGenres = [];
                        foreach ($workRepository::HIMO_SEARCH_VIDEO_GENRE_ID as $videoGenre) {
                            $ignoreVideoGenres[] = '-' . $videoGenre;
                        }
                        $params['genre'] = implode(' || ', $ignoreVideoGenres);
                        $params['genre'] = $params['genre'] . ':';
                    }
                } else {
                    $params['genre'] = $mappingData['genres'];
                }
                // 10件づつ処理
                $processLimit = 10;
                $himoRepository->setLimit($processLimit);
                // 検索
                $response = $himoRepository->searchCrossworksForRelease($params)->get();
                // 初回だけ全体件数の取得
                $totalCount = (int)$response['results']['total'];
                $pageNum = 1;
                $orderNum = 1;
                for ($processCount = 0; $processCount < $totalCount; $processCount = $processCount + $processLimit) {
                    $params['id'] = $this->month . '_' . $this->genreId . '_' . $processCount; // test data
                    $himoRepository->setOffset($processCount);
                    $response = $himoRepository->searchCrossworksForRelease($params)->get();
                    if (empty($response)) {
                        break;
                    }
                    $himoReleaseOrderData = [];
                    $insertWorkId = [];
                    foreach ($response['results']['rows'] as $row) {
                        $insertWorkId[] = $row['work_id'];
                        $himoReleaseOrderData[] = [
                            'work_id' => $row['work_id'],
                            'month' => $saleStartMonth . '-01',
                            'tap_genre_id' => $this->genreId,
                            'page_no' => $pageNum,
                            'sort' => $orderNum,
                        ];
                        $orderNum++;
                    }
                    // データを取得する際は、常にお薦めで取得し、順序をDBに登録する。
                    // ここのリリカレモデルは入れ替えられるようにメンバ変数で保持

                    $this->himoReleaseOrder->insertBulk($himoReleaseOrderData);
                    $workRepository->getWorkList($insertWorkId);
                    if ($orderNum > $totalCount) {
                        break;
                    }
                    $pageNum++;
                }
            }
            return true;
        }
        // 配列になっている為、変更
        if (in_array('audio', $mappingData['msdbItem'])) {
            if ($this->mediaFormat === 'album') {
                $mediaFormat = '1';
            } else if ($this->mediaFormat === 'single') {
                $mediaFormat = '2';
            }
        }
        if ($this->onlyReleased === 'true') {
            $saleStartDateFrom = date('Y-m-01 00:00:00');
            $saleStartDateToForDB = date('Y-m-d 00:00:00');
        }
        $this->himoReleaseOrder->setConditionGenreIdAndMonthAndProductTypeId(
            $this->genreId,
            $saleStartMonth . '-01',
            $mappingData['productSellRentalFlg'],
            $this->sort,
            $mediaFormat,
            $saleStartDateFrom,
            $saleStartDateToForDB
        );
        $this->totalCount = $this->himoReleaseOrder->count();
        // キャッシュしたデータから対象の作品及び商品情報を集約し取得する。
        $results = $this->himoReleaseOrder
            ->selectCamel($this->selectColumn())
            ->get(
                $this->limit,
                $this->offset,
                $mappingData['productSellRentalFlg']);
        if (count($results) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }
        // アダルト表示は常に許可しておく。
        $workRepository->setAgeLimitCheck($this->ageLimitCheck);
        foreach ($results as $result) {
            $workRepository->setSaleType($productRepository->convertProductTypeToStr($result->productTypeId));
            // SQL一発は困難な為、個別にとってくるように変更
            $product = (array)$productModel->setConditionByWorkIdNewestProductWithSaleStartDate(
                $result->workId,
                $result->cccFamilyCd,
                $result->productTypeId,
                $result->saleStartDate
            )->toCamel()->getOne();
            // 個別でとってきたものを商品情報にセット
            $tmpData = $workRepository->formatAddOtherData((array)$result, false, $product);

            // Change productName -> productTitle
            $formatedData[] = arrayChangeKey($tmpData, 'productName', 'productTitle');
        }

        if (empty($formatedData)) {
            return null;
        }
        return $formatedData;
    }


    public function getStatic()
    {
        $workRepository = new WorkRepository;
        $genreId = $this->genreId;
        // 対象月の抽出
        if ($this->month === 'last') {
            $carbon = Carbon::now()->startOfMonth()->subMonth();
        } else if ($this->month === 'next') {
            $carbon = Carbon::now()->startOfMonth()->addMonth();
        } else {
            $carbon = Carbon::now()->startOfMonth()->startOfMonth();
        }
        $month = $carbon->format('Ym');
        $fileName = $genreId . '_' . $month;
        $basePath = env('RELEASE_STATIC_DATA_FOLDER_PATH');
        $filePath = $basePath . DIRECTORY_SEPARATOR . $fileName;
        if(!file_exists($filePath)) {
            return false;
        }
        $json = File::get($filePath);
        $json = json_decode( $json, true);
        foreach ($json['rows'] as $product) {
            $work = $workRepository->get($product['jan'], null, '0205');
            $result[] = [
                'saleStartDate' => $product['saleStartDate'],
                'ssFlg' => $product['ssFlg'],
                'workTitle' => $product['workTitle'],
                'productName' => $product['workTitle']. '(' . $product['numberOfVolume'] . ')',
                'supplement' => $product['author'],
                'makerName' => $product['makerName'],
                'bookSeriesName' => $product['bookSeriesName'],
                'jacketL' => $work['jacketL'],
            ];
        }
        return $result;

    }

    public function genreMapping($genreId)
    {
        $productSellRentalFlg = '';
        $adultFlg = '2';
        if ($genreId >= 1 && $genreId <= 16) {
            $msdbItem = ['video'];
            if ($genreId == 10) {
                $msdbItem = ['audio'];
            }
            if ($genreId >= 1 && $genreId <= 8) {
                $productSellRentalFlg = '2';
            } else {
                $productSellRentalFlg = '1';
            }
        } else if ($genreId >= 17 && $genreId <= 27) {
            $msdbItem = ['audio'];
            if ($genreId >= 17 && $genreId <= 21) {
                $productSellRentalFlg = '2';
            } else {
                $productSellRentalFlg = '1';
            }
        } else if ($genreId >= 28 && $genreId <= 50) {
            $msdbItem = ['book'];
            if ($genreId >= 28 && $genreId <= 38) {
                $productSellRentalFlg = '2';
            } else {
                $productSellRentalFlg = '1';
            }
        } else if ($genreId >= 51 && $genreId <= 55) {
            $msdbItem = ['game'];
            $productSellRentalFlg = '1';
        } else if ($genreId >= 56 && $genreId <= 80) {
            $msdbItem = ['video'];
            $adultFlg = '1';
            $productSellRentalFlg = '2';
        } else if ($genreId >= 81 && $genreId <= 81) {
            $msdbItem = ['book'];
            $adultFlg = '1';
            $productSellRentalFlg = '1';
        } else if ($genreId >= 82 && $genreId <= 83) {
            $msdbItem = ['video'];
            $productSellRentalFlg = '2';
        }
        return [
            'productSellRentalFlg' => $productSellRentalFlg,
            'genres' => $this->genreMapToHimoParam($genreId),
            'adultFlg' => $adultFlg,
            'msdbItem' => $msdbItem
        ];
    }

    public function genreMapToHimoParam($genreId)
    {
        $listArray = config('release_genre_map');
        $listString = null;
        if ($genreId >= 51 && $genreId <= 55) {
            $listString = $listArray[$genreId][0];
        } else {
            if (count($listArray[$genreId]) == 1) {
                if ($listArray[$genreId][0] === self::HIMO_TAP_RECOMMEND) {
                    $listString = self::HIMO_TAP_RECOMMEND;
                } else {
                    $listString = $listArray[$genreId][0] . '::';
                }
            } else {
                $listString = implode(':: || ', $listArray[$genreId]) . '::';
            }
        }
        return $listString;
    }

    public function hasRecommend()
    {
        $himoReleaseOrder = new HimoReleaseOrder;
        $month['last'] = date('Y-m-01', mktime(0, 0, 0, date('n') - 1, 1, date('Y')));
        $month['this'] = date('Y-m-01');
        $month['next'] = date('Y-m-01', mktime(0, 0, 0, date('n') + 1, 1, date('Y')));
        $recommendList = [];
        $listArray = config('release_genre_map');
        foreach ($listArray as $key => $genre) {
            foreach ($genre as $value) {
                if ($value === 'recommendation') {
                    $recommendList[] = $key;
                }
            }
        }

        foreach ($month as $target => $targetMonth) {
            foreach ($recommendList as $genreId) {
                $result[$target][] = [
                    'genreId' => (string)$genreId,
                    'exist' => ($himoReleaseOrder->setConditionByGenreIdAndMonth($genreId, $targetMonth)->count() > 0) ? true : false,
                ];
            }
        }
        return $result;

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
            'final.work_id',
            'work_title',
            'work_type_id',
            'work_format_id',
            'final.ccc_family_cd',
            'scene_l', // 上映映画対応
            'rating_id',
            'big_genre_id',
            'medium_genre_id',
            'small_genre_id',
            'url_cd',
            'ccc_work_cd',
            'p4.jacket_l',
            'p4.sale_start_date',
            'p4.product_type_id',
            'p4.product_unique_id',
            'adult_flg',
            'product_name',
            'maker_name',
            'p4.msdb_item',
            'media_format_id',
            'game_model_name',
            'number_of_volume',
            'p4.item_cd',
            'maker_cd'
        ];
    }
}
