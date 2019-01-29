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
    private $isMaintenance = false;

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
            $this->log('TPOINT', ' Refresh.');
            // 強制的にリフレッシュ
            $refreshResult =  $this->refresh();
            if ($refreshResult === false) {
                $this->isMaintenance = true;
            }
            // 再セット
            $this->setPointDetail();
        } else {
            $this->log('TPOINT', 'Use Cache.');
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
     * @return bool
     */
    public function isMaintenance(): bool
    {
        return $this->isMaintenance;
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
        $this->log('TPOINT Sysytem ID', $this->systemId);

        $pointDetail = $this->getPointDetails();

        if ($pointDetail === false) {
            $this->log('TPOINT Request', 'Data acquisition error.');
            return false;
        }
        $this->log('TPOINT Request ResponseCode', $pointDetail['responseCode']);
        if (
            $pointDetail['responseCode'] !== '00' &&
            $pointDetail['responseCode'] !== '14'
        ) {
            $this->log('TPOINT', 'Is Maintenance.');
            return false;
        }
        $nowDateTime = Carbon::now();
        $this->log('TPOINT Response Membership Type', $pointDetail['membershipType']);
        $this->log('TPOINT Response T-Point', $pointDetail['point']);
        $this->log('TPOINT Response Fixed T-Point Total', $pointDetail['fixedPointTotal']);
        $this->log('TPOINT Response Fixed T-Point Min Limit Time', $pointDetail['fixedPointMinLimitTime']);
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
        $this->log('TPOINT Request Shop Code', $shopCode);
        $tolPointModel = new TolPoint($this->memId);
        $tolPointResponse = $tolPointModel->getDetail($shopCode);
        if (empty($tolPointResponse)) {
            return false;
        }
        $tolPointResponse = current($tolPointResponse->all());
        return [
            'responseCode' => $tolPointResponse['responseCode'],
            'membershipType' => $tolPointResponse['membershipType'],
            'point' => $tolPointResponse['point'],
            'fixedPointTotal' => $tolPointResponse['fixedPointTotal'],
            'fixedPointMinLimitTime' => date('Y-m-d H:i:s', strtotime($tolPointResponse['fixedPointMinLimitTime'])),
        ];
    }

    /**
     * 時間経過をチェックする
     */
    private function checkLimitTime()
    {
        $fromDb = new Carbon();
        $addHour = new Carbon($this->updatedAt);
        $this->log('TPOINT Check Cache Now Time', $fromDb);
        $this->log('TPOINT Check Cache Cache Last Update Time', $addHour);
        $addHour = $addHour->addMinutes($this->fixedPointCacheLimitMinute);
        $this->log('TPOINT Check Cache Cache Next Update Time', $addHour->addMinutes($this->fixedPointCacheLimitMinute));

        if ($fromDb->gte($addHour)) {
            $this->log('TPOINT Limit Time', 'TRUE');
            return true;
        }
        $this->log('TPOINT Limit Time', 'FALSE');
        return false;
    }

    private function log($title, $message)
    {
        Log::info("MEM_ID:" . $this->memId . " → " . $title . " : " . $message);
    }

}
