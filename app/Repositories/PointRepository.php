<?php

namespace App\Repositories;

use App\Model\PointDetails;
use Illuminate\Support\Carbon;

/**
 * Class PointRepository
 * @package App\Repositories
 */
class PointRepository
{
<<<<<<< HEAD
    private $systemId;
    private $memId;
=======
    private $tlsc;
    private $st;
    private $memid;
>>>>>>> 暗号系と外部つなぎ込み部分以外をコミット
    private $membershipType;
    private $point;
    private $fixedPointTotal;
    private $fixedPointMinLimitTime;
    private $fixedPointCacheLimitMinute;
    private $updatedAt;

<<<<<<< HEAD
    // 3時間をデフォルトにする
    const DEFAULT_LIMIT_MINUTE = 180;

    // アプリから渡ってくるシステムID
    const SYSTEM_ID_TAP = 'TAP';
    const SYSTEM_ID_NT = 'NT';

    // TOL-APIにリクエストする時に渡すショップコード
    const SHOP_CODE_TAP = '8998';
    const SHOP_CODE_NT = '8999';

=======
    // 分指定
    const DEFAULT_LIMIT_MINUTE = 180;

>>>>>>> 暗号系と外部つなぎ込み部分以外をコミット
    /**
     * PointRepository constructor.
     * TLSCは必須の為コンストラクタで取得し、DBから取得する
     * @param $tlsc
     */
<<<<<<< HEAD
    public function __construct($systemId, $memId, $refreshFlg)
    {
        // envからキャッシュ有効期限を取得する。
        // 取得できなかった場合はデフォルトで180分を設定する。
        $this->fixedPointCacheLimitMinute = env('FIXED_POINT_CACHE_LIMIT_MINUTE', self::DEFAULT_LIMIT_MINUTE);
        $this->memId = $memId;
        $this->systemId = $systemId;
=======
    public function __construct($tlsc, $refreshFlg)
    {
        // envからキャッシュ有効期限を取得する。
        // 取得できなかった場合はデフォルトで6を設定する。
        $this->fixedPointCacheLimitMinute = env('FIXED_POINT_CACHE_LIMIT_MINUTE', self::DEFAULT_LIMIT_MINUTE);
        $this->tlsc = $tlsc;
        // TLSCを変換してSTの変数にセットする。
        $this->convertTlscToSt();
        // STを変換してMEMの変数にセットする。
        $this->convertStToMemid();
>>>>>>> 暗号系と外部つなぎ込み部分以外をコミット

        // STをもとにDBから値を取得してセットする。
        $isSet = $this->setPointDetail();

        // 初回でセット出来なった場合
        // リフレッシュフラグがtrueだった場合
        // 期限切れだった場合はリフレッシュ
        if ($isSet === false || $refreshFlg === true || $this->checkLimitTime()) {
            // 強制的にリフレッシュ
            $this->refresh();
            // 再セット
            $this->setPointDetail();
        }

    }

    /**
<<<<<<< HEAD
=======
     * ST内部管理番号取得
     * @return mixed
     */
    public function getSt()
    {
        return $this->st;
    }

    /**
>>>>>>> 暗号系と外部つなぎ込み部分以外をコミット
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
<<<<<<< HEAD
=======
     * TLSCからSTに変換する
     */
    public function convertTlscToSt()
    {
        // 変換ロジックを別で管理。
        // ここでは呼び出すだけ。
        $st = '0000000000000001';
        $this->st = $st;
    }

    /**
     * STからMEM_IDに変換する。
     */
    public function convertStToMemid()
    {
        // 変換ロジックを別で管理。
        // ここでは呼び出すだけ。
        $memid = '';
        $this->memid = $memid;
    }

    /**
>>>>>>> 暗号系と外部つなぎ込み部分以外をコミット
     * Private
     * DBから取得し書くパラメーターにセットする
     * @return mixed
     */
    private function setPointDetail()
    {
        $pointDetailsModel = new PointDetails();
<<<<<<< HEAD
        $result = $pointDetailsModel->setConditionBySt($this->memId)->getOne();
=======
        $result = $pointDetailsModel->setConditionBySt($this->st)->getOne();
>>>>>>> 暗号系と外部つなぎ込み部分以外をコミット
        if (empty($result)) {
            return false;
        }
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
<<<<<<< HEAD
        $pointDetail = $this->getPointDetails();
        $updateParam = [
                [
                    'mem_id' => $this->memId,
=======
        $pointDetail = $this->getPointDetailsFromMars();
        $updateParam = [
                [
                    'st' => $this->st,
>>>>>>> 暗号系と外部つなぎ込み部分以外をコミット
                    'membership_type' => $pointDetail['membershipType'],
                    'point' => $pointDetail['point'],
                    'fixed_point_total' => $pointDetail['fixedPointTotal'],
                    'fixed_point_min_limit_time' => $pointDetail['fixedPointMinLimitTime'],
                    'updated_at' => $pointDetail['updatedAt']
                ]
        ];
        $pointDetailsModel->insertBulk($updateParam);
    }

    /**
     * Private
     * Marsからポイント詳細情報を取得する
     */
<<<<<<< HEAD
    private function getPointDetails()
    {
        $shopCode = '';

        // memidを利用
        $this->memId;
        // NTだった場合のみ指定。それ以外はすべてTAPとして処理。
        if ($this->systemId === self::SYSTEM_ID_NT) {
            $shopCode = self::SHOP_CODE_NT;
        } else {
            $shopCode = self::SHOP_CODE_TAP;
        }

=======
    private function getPointDetailsFromMars()
    {
        // memidを利用
        $this->memid;
>>>>>>> 暗号系と外部つなぎ込み部分以外をコミット
        // todo スタブデータ
        return [
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
