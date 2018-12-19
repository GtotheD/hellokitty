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
 * レンタル関連申請(参照)API(mre001) / xml形式
 * Class FlatRentalOperation
 * @package App\Model
 */
class TolRentalApplication extends TolBaseModel
{
    private $header = [
        'messageClass', // 伝文区分
        'messageVer', // 伝文Ver
        'responseType', // レスポンスタイプ
        'processingSequence', // 処理シーケンス
        'processingTimestamp', // 処理タイムスタンプ
        'reponseStatus1', // 応答ステータス1
        'reponseStatus2', // 応答ステータス2
        'message', // メッセージ
        'companyCode', // 企業コード
        'storeNumber', // 店番
        'terminalNumber', // 端末番号
        'handlerNumber', // 扱者番号
        'storeAccountingDate', // 店舗計上日付
        'recordedTimeAtStore', // 店舗計上時間
        'memberCardCode', // 会員カードコード
        'oldMemberCardCode', // 旧会員カードコード
        'stInternalControlNumber', // ST内部管理番号
        'tolMemberId', // TOL会員ID
        'rentalRegistrationApplicationStatus', // レンタル登録申請ステータス
        'rentalUpdateApplicationStatus', // レンタル更新申請ステータス
        'informationChangeApplicationStatus', // 情報変更申請ステータス
        'identificationConfirmationNecessityFlag', // 本人確認要否フラグ
        'personalIdentificationExecutionDate', // 本人確認実施日
        'processingCompanyCode', // 処理企業コード
        'processingStoreCode', // 処理店舗コード
        'aDisposalDay', // 処理日
        'KanjiFirstName', // 漢字氏名
        'kanaFullName', // かな氏名
        'postalCode', // 郵便番号
        'KanjiAddress1', // 漢字住所１
        'phoneNumber1', // 電話番号１
        'telephoneNumber2', // 電話番号２
        'birthday', // 生年月日
        'sex', // 性別
        'memberRank', // 会員ランク
        'dateOfEnrollment', // 入会年月日
        'expirationDate', // 有効期限
        'deleteFlag', // 削除フラグ
        'dmStopClassification', // DM停止区分
        'updatedTimestamp', // 更新タイムスタンプ
    ];

    public function getDetail() {
        $xml = $this->tolClient->getRentalApplication();
        $memberDetailXml = simplexml_load_string($xml);
        // レスポンスステータスが0でなかった場合はエラーとしてfalseを返却
        if (current($memberDetailXml->status) !== '0') {
            return false;
        }
        $csv = current($memberDetailXml->responseData);
        return $this->getCollectionFromCSV($this->header, $csv);
    }
}