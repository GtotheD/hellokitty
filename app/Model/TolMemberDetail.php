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
 * ヘッダの数が変動する。
 * Class MintMemberDetail
 * @package App\Model
 *
 */
class TolMemberDetail extends TolBaseModel
{
    private $baseHeader = [
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
        'speedMemberRank', // 13 SPEED会員ランク
        'kanaFullName', // 14 かな氏名
        'KanjiFirstName', // 15 漢字氏名
        'KanjiAddress1', // 16 漢字住所１
        'KanjiAddress2', // 17 漢字住所２
        'postalCode', // 18 郵便番号
        'phoneNumber1', // 19 電話番号１
        'telephoneNumber2', // 20 電話番号２
        'birthday', // 21 生年月日
        'sex', // 22 性別
        'dateOfEnrollment', // 23 入会年月日
        'expirationDate', // 23 有効期限
        'deleteFlag', // 25 削除フラグ
        'oldMembershipNumber', // 26 旧会員番号
        'processingDate', // 27 処理日付
        'processingTime', // 28 処理時刻
        'dmStopClassification', // 29 DM停止区分
        'memberType', // 30 会員種別
        'wCardFlag', // 31 Wカードフラグ
        'rentalAddedStoreCode', // 32 レンタル付与店舗コード
        'rentalGrantDate', // 33 レンタル付与日付
        'updateStoreCode', // 34 更新店舗コード
        'informationChangeStoreCode', // 35 情報変更店コード
        'informationChangeDate', // 36 情報変更日
        'updateShopRegistrationDate', // 37 更新店システム登録日時
        'c8LastUpdateDate', // 38 最終更新システム登録日時
        'cMemberInformationSetNumber', // 39 C会員情報セット件数
    ];
    private $repeatHeader = [
        'cMemberType', // 40 C会員区分
        'headquartersRegistrationProcessingDate', // 41 本部登録処理日付
        'headquartersRegistrationProcessingTime', // 42 本部登録処理時刻
        'applicantStoreCode', // 43 申請店コード
        'applicationDatetime', // 44 申請日時z
        'cMemberRemarks1', // 45 C会員備考1
        'cmemberRemarks2', // 46 C会員備考2
    ];
    private $lastHeader = [
        'freeRentalRegistrationControlFlag', // 47 無料レンタル登録制御フラグ
        'optoutFlag', // 48 オプトアウトフラグ
    ];

    /**
     * @return bool|\Illuminate\Support\Collection
     * @throws \App\Exceptions\NoContentsException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDetail()
    {
        $xml = $this->tolClient->getMemberDetail();
        $memberDetailXml = simplexml_load_string($xml);
        // レスポンスステータスが0でなかった場合はエラーとしてfalseを返却
        if ($memberDetailXml === false || current($memberDetailXml->status) !== '0') {
            return false;
        }
        $csv = current($memberDetailXml->responseData);
//        dd(mb_convert_encoding(urldecode($csv), "UTF-8", "SJIS"));

        // C会員情報セット件数を取得してヘッダの数を変更する
        $csvObj = current($this->getCollectionFromCSV($this->baseHeader, $csv)->all());
        // 1以上だった場合は、数分$repeatHeaderを付与する
        $recordCount = (int)$csvObj['cMemberInformationSetNumber'];
        // 1件以上だった場合は数分生成
        if ($recordCount >= 1) {
            for($i = 1; $i <= (int)$csvObj['cMemberInformationSetNumber']; $i++) {
                foreach($this->repeatHeader as $columnName) {
                    $editColumnName[] = $columnName . (string)$i;
                }
            }
        } else {
        // 0件だった場合は1のみ生成
            foreach($this->repeatHeader as $columnName) {
                $editColumnName[] = $columnName . '1';
            }
        }
        // ヘッダの結合
        $header = array_merge($this->baseHeader, $editColumnName, $this->lastHeader);
        // 再取得
        return $this->getCollectionFromCSV($header, $csv);
    }

    public function getRepeatHeader()
    {
        return $this->repeatHeader;
    }
}