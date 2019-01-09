<?php

namespace App\Repositories;

use App\Model\PointDetails;
use App\Model\TolPoint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Libraries\Security;
use Exception;

/**
 * Class PointRepository
 * @package App\Repositories
 */
class PointRepository
{
    private $systemId;
    private $tolId;
    private $memId;
    private $key;
    private $responseCode;
    private $membershipType;
    private $point;
    private $fixedPointTotal;
    private $fixedPointMinLimitTime;
    private $fixedPointCacheLimitMinute;
    private $updatedAt;

    // 3時間をデフォルトにする
    const DEFAULT_LIMIT_MINUTE = 180;

    // アプリから渡ってくるシステムID
    const SYSTEM_ID_TAP = 'TAP';
    const SYSTEM_ID_NT = 'NT';

    // TOL-APIにリクエストする時に渡すショップコード
    const SHOP_CODE_TAP = '8998';
    const SHOP_CODE_NT = '8999';

    // Trait
    use Security;

    /**
     * PointRepository constructor.
     * @param $systemId
     * @param $tolId
     * @param $refreshFlg
     * @throws Exception
     */
    public function __construct($systemId, $tolId, $refreshFlg)
    {
        // envからキャッシュ有効期限を取得する。
        // 取得できなかった場合はデフォルトで180分を設定する。
        $this->fixedPointCacheLimitMinute = env('FIXED_POINT_CACHE_LIMIT_MINUTE', self::DEFAULT_LIMIT_MINUTE);
        $this->tolId = $tolId;
        $this->systemId = $systemId;

        // TolID→MemID変換用キー
        $this->key = env('TOL_ENCRYPT_KEY');
        Log::info('Fixed Point API tolId : ' . $this->tolId);
        $this->memId = $this->decodeMemid($this->key, $this->tolId);
        Log::info('Fixed Point API convert tolId : ' . $this->tolId . ' -> ' . $this->memId );

        // MemIdをもとにDBから値を取得してセットする。
        $isSet = $this->setPointDetail();

        // 初回でセット出来なった場合
        // リフレッシュフラグがtrueだった場合
        // 期限切れだった場合はリフレッシュ
        if ($isSet === false || $refreshFlg === true || $this->checkLimitTime()) {
            // 強制的にリフレッシュ
            $refreshResult =  $this->refresh();
            if ($refreshResult === false) {
                return false;
            }
            // 再セット
            $this->setPointDetail();
        }

    }

    /**
     * @return mixed
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * 会員種別取得
     * @return mixed
     */
    public function getMembershipType()
    {
        return $this->membershipType;
    }

    /**
     * 利用可能ポイント取得
     * @return mixed
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * 期間固定ポイント数合計取得
     * @return mixed
     */
    public function getFixedPointTotal()
    {
        return $this->fixedPointTotal;
    }

    /**
     * 期間固定ポイント最短有効期限取得
     * @return mixed
     */
    public function getFixedPointMinLimitTime()
    {
        return $this->fixedPointMinLimitTime;
    }

    /**
     * リフレッシュ（強制更新）
     * @param mixed $refreshFlg
     */
    public function setRefreshFlg($refreshFlg)
    {
        $this->refreshFlg = $refreshFlg;
    }

    /**
     * Private
     * DBから取得し書くパラメーターにセットする
     * @return mixed
     */
    private function setPointDetail()
    {
        $pointDetailsModel = new PointDetails();
        $result = $pointDetailsModel->setConditionBySt($this->memId)->getOne();
        if (empty($result)) {
            return false;
        }
        $this->responseCode = $result->response_code;
        $this->membershipType = $result->membership_type;
        $this->point = $result->point;
        $this->fixedPointTotal = $result->fixed_point_total;
        $this->fixedPointMinLimitTime = $result->fixed_point_min_limit_time;
        $this->updatedAt = $result->updated_at;
        return true;
    }

    /**
     * Private
     * TOLからデータを取得して書き換える
     * @param $tlsc
     */
    private function refresh()
    {
        $pointDetailsModel = new PointDetails();
        // Marsからポイント詳細情報を取得する
        $pointDetail = $this->getPointDetails();
        if (
            $pointDetail['responseStatus1'] !== '00' ||
            $pointDetail['responseStatus1'] !== '14'
        ) {
            return false;
        }
        $nowDateTime = Carbon::now();
        $updateParam = [
                [
                    'mem_id' => $this->memId,
                    'response_code' => $pointDetail['responseCode'],
                    'membership_type' => $pointDetail['membershipType'],
                    'point' => $pointDetail['point'],
                    'fixed_point_total' => $pointDetail['fixedPointTotal'],
                    'fixed_point_min_limit_time' => $pointDetail['fixedPointMinLimitTime'],
                    'updated_at' => $nowDateTime
                ]
        ];
        $pointDetailsModel->insertBulk($updateParam);
        return true;
    }

    /**
     * Private
     * Marsからポイント詳細情報を取得する
     */
    private function getPointDetails()
    {
        // NTだった場合のみ指定。それ以外はすべてTAPとして処理。
        if ($this->systemId === self::SYSTEM_ID_NT) {
            $shopCode = self::SHOP_CODE_NT;
        } else {
            $shopCode = self::SHOP_CODE_TAP;
        }
        // todo: HOSOL実装完了後に適用
//        $tolPointModel = new TolPoint($this->memId);
//        $tolPointResponse = $tolPointModel->getDetail($shopCode);
//        $tolPointResponse = current($tolPointResponse->all());
//        return [
//            'responseCode' => $tolPointResponse['responseCode'],
//            'membershipType' => $tolPointResponse['membershipType'],
//            'point' => $tolPointResponse['point'],
//            'fixedPointTotal' => $tolPointResponse['fixedPointTotal'],
//            'fixedPointMinLimitTime' => date('Y-m-d H:i:s', strtotime($tolPointResponse['fixedPointMinLimitTime'])),
//        ];
        return [
            'responseCode' => '00',
            'membershipType' => 1,
            'point' => rand(1, 9999),
            'fixedPointTotal' => rand(1, 999),
            'fixedPointMinLimitTime' => '2018-12-01 00:00:00',
            'updatedAt' => carbon::now(),
        ];
    }

    /**
     * 時間経過をチェックする
     */
    private function checkLimitTime()
    {
        $fromDb = new Carbon();
        $addHour = new Carbon($this->updatedAt);
        if ($fromDb->gte($addHour->addMinutes($this->fixedPointCacheLimitMinute))) {
            return true;
        }
        return false;
    }

}
