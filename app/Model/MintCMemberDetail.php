<?php
/**
 * Created by PhpStorm.
 * User: usuda
 * Date: 2018/11/29
 * Time: 16:05
 */

namespace App\Model;

use App\Client\MintClient;

class MintCMemberDetail extends BaseCsvModel
{
    private $header = [
        'messageClass', // 伝文区分
        'messageVer', // 伝文Ver
        'companyCode', // 企業コード
        'storeNumber', // 店番
        'terminalNumber', // 端末番号
        'handlerNumber', // 扱者番号
        'storeAccountingDate', // 店舗計上日付
        'recordedTimeAtStore', // 店舗計上時間
        'responseStatus1', // 応答ステータス1
        'responseStatus2', // 応答ステータス2
        'mintLastUpdateDate', // 最終更新システム登録日時
        'membershipNumber', // 会員番号
        'cMemberType', // C会員区分
        'headquartersRegistrationProcessingDate', // 本部登録処理日付
        'headquartersRegistrationProcessingTime', // 本部登録処理時刻
        'applicantStoreCode', // 申請店コード
        'applicationDatetime', // 申請日時
        'cMemberRemarks1', // C会員備考1
        'cmemberRemarks2', // C会員備考2
        'crLf', // CR/LF 複数レコードの場合のみレコード区切りとして設定。
    ];

    public function getDetail() {
        $mintClient = new MintClient();
        $csv = $mintClient->getCMemberList();
        // todo XMLから抽出する処理を記述
        return $this->getCollection($this->header, $csv);
    }
}