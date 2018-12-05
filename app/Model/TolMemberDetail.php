<?php
/**
 * Created by PhpStorm.
 * User: usuda
 * Date: 2018/11/29
 * Time: 16:05
 */

namespace App\Model;

use App\Clients\TolClient;

/**
 * MMC200
 * Class MintMemberDetail
 * @package App\Model
 *
 */
class TolMemberDetail extends BaseCsvModel
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
        'speedMemberRank', // SPEED会員ランク
        'kanaFullName', // かな氏名
        'KanjiFirstName', // 漢字氏名
        'KanjiAddress1', // 漢字住所１
        'KanjiAddress2', // 漢字住所２
        'postalCode', // 郵便番号
        'phoneNumber1', // 電話番号１
        'telephoneNumber2', // 電話番号２
        'birthday', // 生年月日
        'sex', // 性別
        'dateOfEnrollment', // 入会年月日
        'expirationDate', // 有効期限
        'deleteFlag', // 削除フラグ
        'oldMembershipNumber', // 旧会員番号
        'processingDate', // 処理日付
        'processingTime', // 処理時刻
        'dmStopClassification', // DM停止区分
        'memberType', // 会員種別
        'wCardFlag', // Wカードフラグ
        'rentalAddedStoreCode', // レンタル付与店舗コード
        'rentalGrantDate', // レンタル付与日付
        'updateStoreCode', // 更新店舗コード
        'informationChangeStoreCode', // 情報変更店コード
        'informationChangeDate', // 情報変更日
        'updateShopRegistrationDate', // 更新店システム登録日時
        'c8LastUpdateDate', // 最終更新システム登録日時
        'cMemberInformationSetNumber', // C会員情報セット件数
        'cMemberType', // C会員区分
        'headquartersRegistrationProcessingDate', // 本部登録処理日付
        'headquartersRegistrationProcessingTime', // 本部登録処理時刻
        'applicantStoreCode', // 申請店コード
        'applicationDatetime', // 申請日時z
        'cMemberRemarks1', // C会員備考1
        'cmemberRemarks2', // C会員備考2
        'freeRentalRegistrationControlFlag', // 無料レンタル登録制御フラグ
        'optoutFlag', // オプトアウトフラグ
    ];

    public function getDetail() {
        $mintClient = new TolClient();
        $xml = $mintClient->getMemberDetail();
        $memberDetailXml = simplexml_load_string($xml);
        // レスポンスステータスが0でなかった場合はエラーとしてfalseを返却
        if (current($memberDetailXml->status) !== '0') {
            return false;
        }
        $csv = current($memberDetailXml->responseData);
        return $this->getCollectionFromCSV($this->header, $csv);
    }
}