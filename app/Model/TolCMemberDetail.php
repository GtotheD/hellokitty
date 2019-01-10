<?php
/**
 * Created by PhpStorm.
 * User: usuda
 * Date: 2018/11/29
 * Time: 16:05
 */

namespace App\Model;

class TolCMemberDetail extends TolBaseModel
{
    private $header = [
        'messageClass', // 1 伝文区分
        'messageVer', // 2 伝文Ver
        'companyCode', // 3 企業コード
        'storeNumber', // 4 店番
        'terminalNumber', // 5 端末番号
        'handlerNumber', // 6 扱者番号
        'storeAccountingDate', // 7 店舗計上日付
        'recordedTimeAtStore', // 8 店舗計上時間
        'responseStatus1', // 9 応答ステータス1
        'responseStatus2', // 10 応答ステータス2
        'mintLastUpdateDate', // 11 最終更新システム登録日時
        'membershipNumber', // 12 会員番号
        'cMemberType', // 13 C会員区分
        'headquartersRegistrationProcessingDate', // 14 本部登録処理日付
        'headquartersRegistrationProcessingTime', // 15 本部登録処理時刻
        'applicantStoreCode', // 16 申請店コード
        'applicationDatetime', // 17 申請日時
        'cMemberRemarks1', // 18 C会員備考1
        'cmemberRemarks2', // 19 C会員備考2
        'crLf', // 20 CR/LF 複数レコードの場合のみレコード区切りとして設定。
    ];

    public function getDetail() {
        $xml = $this->tolClient->getCMemberList();
        if (empty($xml)) {
            return false;
        }
        $memberDetailXml = simplexml_load_string($xml);
        // レスポンスステータスが0でなかった場合はエラーとしてfalseを返却
        if (current($memberDetailXml->status) !== '0') {
            return false;
        }
        $csv = current($memberDetailXml->responseData);
        return $this->getCollectionFromCSV($this->header, $csv);
    }
}