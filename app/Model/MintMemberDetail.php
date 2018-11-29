<?php
/**
 * Created by PhpStorm.
 * User: usuda
 * Date: 2018/11/29
 * Time: 16:05
 */

namespace App\Model;

use League\Csv\Reader;

class MintMemberDetail
{

    // CSVを受け取ってコレクション配列を返す
    public function getClient()
    {
        $path = base_path('tests/Data/tol/');

        $csv = file_get_contents($path . 'mmc200.csv');
        dd($csv);

    }


    //
    public function getCollection($mintResponse)
    {
        $header = [
            // 伝文区分
            'messageClass',

            // 伝文Ver
            'messageVer',

            // 企業コード
            'companyCode',

            // 店番
            'storeNumber',

            // 端末番号
            'terminalNumber',

            // 扱者番号
            'handlerNumber',

            // 店舗計上日付
            'storeAccountingDate',

            // 店舗計上時間
            'recordedTimeAtStore',

            // 応答ステータス1
            'responseStatus1',

            // 応答ステータス2
            'responseStatus2',

            // 最終更新システム登録日時
            'mintLastUpdateDate',

            // 会員番号
            'membershipNumber',

            // SPEED会員ランク
            'speedMemberRank',

            // かな氏名
            'kanaFullName',

            // 漢字氏名
            'KanjiFirstName',

            // 漢字住所１
            'KanjiAddress1',

            // 漢字住所２
            'KanjiAddress2',

            // 郵便番号
            'postalCode',

            // 電話番号１
            'phoneNumber1',

            // 電話番号２
            'telephoneNumber2',

            // 生年月日
            'birthday',

            // 性別
            'sex',

            // 入会年月日
            'dateOfEnrollment',

            // 有効期限
            'expirationDate',

            // 削除フラグ
            'deleteFlag',

            // 旧会員番号
            'oldMembershipNumber',

            // 処理日付
            'processingDate',

            // 処理時刻
            'processingTime',

            // DM停止区分
            'dmStopClassification',
            // 会員種別
            'MemberType',

            // Wカードフラグ
            'wCardFlag',

            // レンタル付与店舗コード
            'rentalAddedStoreCode',

            // レンタル付与日付
            'rentalGrantDate',

            // 更新店舗コード
            'updateStoreCode',

            // 情報変更店コード
            'informationChangeStoreCode',

            // 情報変更日
            'informationChangeDate',

            // 更新店システム登録日時
            'updateShopRegistrationDate',

            // 最終更新システム登録日時
            'c8LastUpdateDate',

            // C会員情報セット件数
            'cMemberInformationSetNumber',

            // C会員区分
            'cMemberType',

            // 本部登録処理日付
            'headquartersRegistrationProcessingDate',

            // 本部登録処理時刻
            'headquartersRegistrationProcessingTime',

            // 申請店コード
            'applicantStoreCode',

            // 申請日時
            'applicationDatetime',

            // C会員備考1
            'cMemberRemarks1',

            // C会員備考2
            'cmemberRemarks2',

            // 無料レンタル登録制御フラグ
            'freeRentalRegistrationControlFlag',

            // オプトアウトフラグ
            'optoutFlag',

        ];
    }
}