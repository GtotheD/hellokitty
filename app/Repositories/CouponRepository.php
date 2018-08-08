<?php

namespace App\Repositories;

use GuzzleHttp\Exception\ClientException;
use App\Model\OneTimeCoupon;
use App\Exceptions\NoContentsException;
use Illuminate\Support\Carbon;
use Log;

/**
 * Class CouponRepository
 * @package App\Repositories
 */
class CouponRepository
{

    protected $totalCount;
    protected $hasNext;
    private $storeCds;
    private $oneTimeCoupon;

    public function __construct()
    {
        $this->oneTimeCoupon = new OneTimeCoupon();
    }

    /**
     * @return mixed
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @return mixed
     */
    public function getHasNext()
    {
        return $this->hasNext;
    }

    /**
     * Set store_cd
     * @param type $rowsData
     * @return string
     */
    public function setStoreCds($rowsData)
    {
        $storeCds = [];
        foreach ($rowsData as $row) {
            array_push($storeCds, $row);
        }
        $this->storeCds = $storeCds;
    }

    /**
     * @return array
     */
    public function get()
    {
        $rows = [];

        $tapRepository = new TAPRepository();
        foreach($this->storeCds as $storeCd) {

            // 店舗番号から有効なクーポン情報を取得
            $this->oneTimeCoupon->setConditionByStoreCdAndDeliveryDt($storeCd);
            if ($this->oneTimeCoupon->count() > 0) {

                $data = $this->oneTimeCoupon->getAll();
                foreach($data as $row) {

                    Log::info('store_cd['.$storeCd."] tokuban[".$row->tokuban."]");

                    try {
                        $response = $tapRepository->getCoupon(
                            $storeCd,
                            sprintf('%03d', $row->tokuban),
                            $row->delivery_id,
                            Carbon::parse($row->delivery_start_date)->format('YmdHi'),
                            Carbon::parse($row->delivery_end_date)->format('YmdHi'));

                        // クーポン画像の生成エラー判定
                        if ($response['result'] == 1) {
                            // $response['error']['message']
                            // $response['error']['status']
                            // $response['error']['code']
                            Log::warn("error code[".$response['error']['code']."] status[".$response['error']['status']."] message:".$response['error']['message']);
                            continue;
                        }

                        $coupons[] = [
                            'tokuban' => $row->tokuban,
                            'deliveryStartDate' => Carbon::parse($row->delivery_start_date)->format('Y-m-d H:i:s'),
                            'deliveryEndDate' => Carbon::parse($row->delivery_end_date)->format('Y-m-d H:i:s'),
                            'image' => $response['entry']['qrimg']
                        ];

                    } catch (NoContentsException $e) {
                        Log::warn('status[404] Exception:'.$e->getMessage());
                    } catch (ClientException $e) {
                        // 403 APIKey指定エラー
                        // 400 パラメータ不正
                        // 処理を終了して500を返却する
                        Log::error('Exception:'.$e->getMessage());
                    }
                }

                if (!empty($coupons)) {
                    $rowData = [
                        'storeCd' => $storeCd,
                        'coupons' => $coupons
                    ];
                }
                $coupons = [];
            }

            if (!empty($rowData)) {
                $rows[] = $rowData;
            }
            $rowData = [];
        }

        return $rows;
    }
}
