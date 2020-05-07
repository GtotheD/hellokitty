<?php
/**
 * Created by PhpStorm.
 * User: usuda
 * Date: 2018/11/29
 * Time: 16:05
 */

namespace App\Model;
/**
 * 定額レンタル操作(mfr001)  / xml形式
 * Class FlatRentalOperation
 * @package App\Model
 */
class TolNotification extends TolBaseModel
{
    private $header = [
        'messageClass', // 伝文区分
        'messageVer', // 伝文Ver
        'companyCode', // 企業コード
        'storeNumber', // 店番
        'terminalNumber', //端末番号
        'handlerNumber', //扱者番号
        'storeAccountingDate', //店舗計上日付
        'storeRecognitionTime', //店舗計上時間
        'responseStatus1', //応答ステータス1
        'responseStatus2', //応答ステータス2
        'membershipNumber', //会員番号
        'tInternalControlNumber', //T内部管理番号
        'storeCode',//店舗コード
        'flatPlanNumber', //定額プラン番号
        'flatPlanRegistrationDate', //定額プラン登録日
        'flatPlanTerminationDate', //定額プラン解約日
        'terminationFlag', //解約フラグ
        'updateTimestamp', //更新タイムスタンプ
        'detailedStatus', //詳細ステータス
        'internetUsageStatus', //ネット利用ステータス
        'internetUseDate', //ネット利用日		ネット利用日
        'freeTrialUseClass', //フリートライアル利用区分
        'freeTrialEndDate', //フリートライアル終了日
        'registrationRouteId', //登録経路ID
    ];

    public function getDetail() {
        $xml = $this->tolClient->getFlatRentalOperation();
        $memberDetailXml = simplexml_load_string($xml);
        // レスポンスステータスが0でなかった場合はエラーとしてfalseを返却
        if ($memberDetailXml === false || current($memberDetailXml->status) !== '0') {
            return false;
        }
        $csv = current($memberDetailXml->responseData);
        return $this->getCollectionFromCSV($this->header, $csv);
    }

    /**
     * @throws \App\Exceptions\NoContentsException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getStatus() {
        $xml = $this->tolClient->getNotification();
        return simplexml_load_string($xml);
    }

    /**
     * @param int $chkReservation
     * @return \SimpleXMLElement
     * @throws \App\Exceptions\NoContentsException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateStatus($chkReservation = 0) {
        $xml = $this->tolClient->updateNotification($chkReservation);
        return simplexml_load_string($xml);
    }

    /**
     * @throws \App\Exceptions\NoContentsException
     */
    public function getPromotionStatus($promotionId)
    {
        $xml = $this->tolClient->getPromotionStatus($promotionId);
        return simplexml_load_string($xml);
    }

    /**
     * @throws \App\Exceptions\NoContentsException
     */
    public function registPromotion($params)
    {
        $xml = $this->tolClient->registPromotion($params);
        return simplexml_load_string($xml);
    }

    /**
     * @throws \App\Exceptions\NoContentsException
     */
    public function getPushNotification()
    {
        $xml = $this->tolClient->getPushNotification();
        return simplexml_load_string($xml);
    }

    /**
     * @throws \App\Exceptions\NoContentsException
     */
    public function registPushNotification($params)
    {
        $xml = $this->tolClient->registPushNotification($params);
        return simplexml_load_string($xml);
    }
}
