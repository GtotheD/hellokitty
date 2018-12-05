<?php

namespace App\Repositories;

use App\Model\TolFlatRentalOperation;
use App\Model\TolMemberDetail;
use App\Model\TolCMemberDetail;
use App\Model\TolMembershipStatus;
use App\Model\TolRentalApplication;
use Illuminate\Support\Carbon;

class RentalUseRegistrationRepository extends BaseRepository
{

    public function get()
    {
        // 会員照会API mmc200
        $tolMemberDetailModel = new TolMemberDetail();
        $tolMemberDetailCollection = $tolMemberDetailModel->getDetail();
        $tolMemberDetail = current($tolMemberDetailCollection->all());


        // C会員リスト検索 mmc208
        $tolCMemberDetailModel = new TolCMemberDetail();
        $tolCMemberDetailCollection = $tolCMemberDetailModel->getDetail();
        $tolCMemberDetail = current($tolCMemberDetailCollection);

        // 定額レンタル操作 mfr001
        $tolFlatRentalOperationModel = new TolFlatRentalOperation();
        $tolFlatRentalOperationCollection = $tolFlatRentalOperationModel->getDetail();
        $tolFlatRentalOperation = current($tolFlatRentalOperationCollection);

        // レンタル関連申請API mre001
        $tolRentalApplicationModel = new TolRentalApplication();
        $tolRentalApplicationCollection = $tolRentalApplicationModel->getDetail();
        $tolRentalApplication = current($tolRentalApplicationCollection);

        // TOL会員状態取得
        $tolMembershipStatusModel = new TolMembershipStatus();
        $tolMembershipStatusCollection = $tolMembershipStatusModel->getDetail();
        $tolMembershipStatus = current($tolMembershipStatusCollection);

        // 当日
        $nowDatetime = Carbon::now();

        /**
         * 非表示の項番・その他情報は返さない
         */
        // 削除済み会員(37~39,43~45,79~81)-8
        if ($tolMemberDetail['deleteFlag'] === 1) {
            return [
                'itemNumber' => 8,
                'rentalExpirationDate' => ''
            ];
        }

        // 有効期限切れ(31~33)-7
        if ($nowDatetime > $tolMemberDetail['expirationDate']) {
            return [
                'itemNumber' => 7,
                'rentalExpirationDate' => ''
            ];
        }

        // C会員リストにいる(25~27,73~75)-6
        if ($tolCMemberDetail['cMemberType'] != 'w2') {
            return [
                'itemNumber' => 6,
                'rentalExpirationDate' => ''
            ];
        }
        // ネットT会員(91)-17
        if ($tolMembershipStatus['tmflg'] === 2) {
            return [
                'itemNumber' => 17,
                'rentalExpirationDate' => ''
            ];
        }

        // 有効期限満了日の前月1日
        $prevMonthCarbon = new Carbon($tolMemberDetail['expirationDate']);
        // 31の場合31ない月でバグるので、startofMonthで1日にしてから前月を取得する
        $prevMonthCarbon->startofMonth()->subMonth();
        $prevMonth1st = $prevMonthCarbon->format('Ym01');

        /**
         * レンタル会員
         */
        if ($tolMemberDetail['memberType'] == 2) {
            // まだ更新期間に入ってない(レンタル利用可)
            if ($prevMonth1st > $nowDatetime) {
                // 本人確認不要(49)-9
                if ($tolRentalApplication['identificationConfirmationNecessityFlag'] === 0) {
                    return [
                        'itemNumber' => 9,
                        'rentalExpirationDate' => $tolRentalApplication['expirationDate']
                    ];
                    // 本人確認必要(55)-10
                } else {
                    return [
                        'itemNumber' => 10,
                        'rentalExpirationDate' => $tolRentalApplication['expirationDate']
                    ];
                }
                // 更新期間に入っている(レンタル利用可)
            } elseif ($prevMonth1st <= $nowDatetime && $nowDatetime <= $tolMemberDetail['expirationDate']) {
                // 「レンタル登録済み」：Wカード or プレミアム会員
                if (($tolMemberDetail['wCardFlag'] != '00') || ($tolFlatRentalOperation['responseStatus1'] == '00')) {
                    // 本人確認不要(61-2,3,4)-11
                    if ($tolRentalApplication['identificationConfirmationNecessityFlag'] === 0) {
                        return [
                            'itemNumber' => 11,
                            'rentalExpirationDate' => $tolRentalApplication['expirationDate']
                        ];
                        // 本人確認必要(67-2,3,4)-15
                    } else {
                        return [
                            'itemNumber' => 15,
                            'rentalExpirationDate' => $tolRentalApplication['expirationDate']
                        ];
                    }
                    // 非Wカード＆非プレミアム会員
                } else {
                    // 本人確認不要(61-5)-12
                    if ($tolRentalApplication['identificationConfirmationNecessityFlag'] === 0) {
                        return [
                            'itemNumber' => 12,
                            'rentalExpirationDate' => $tolRentalApplication['expirationDate']
                        ];
                        // 本人確認必要(67-5)-16
                    } else {
                        return [
                            'itemNumber' => 16,
                            'rentalExpirationDate' => $tolRentalApplication['expirationDate']
                        ];
                    }
                }

                // レンタル更新処理中
                if ($tolRentalApplication['rentalUpdateApplicationStatus'] === 1) {
                    // 本人確認不要(64)-13
                    if ($tolRentalApplication['identificationConfirmationNecessityFlag'] === 0) {
                        return [
                            'itemNumber' => 13,
                            'rentalExpirationDate' => $tolRentalApplication['expirationDate']
                        ];
                        // 本人確認必要(65)-14
                    } else {
                        return [
                            'itemNumber' => 14,
                            'rentalExpirationDate' => $tolRentalApplication['expirationDate']
                        ];
                    }
                }
            }
            // レンタル会員だけどどこにも入らなかった場合は空でOK?
            return $statusDeails;
        }

        /**
         * レンタル会員
         */
        if (($prevMonth1st > $nowDatetime) ||
            ($prevMonth1st <= $nowDatetime && $nowDatetime <= $tolMemberDetail['expirationDate'])) {
            // レンタル登録申請：処理中
            if ($tolRentalApplication['rentalRegistrationApplicationStatus'] === 1) {
                // 本人確認必要(3)(15)-2
                if ($tolRentalApplication['identificationConfirmationNecessityFlag'] === 1) {
                    return [
                        'itemNumber' => 2,
                        'rentalExpirationDate' => ''
                    ];
                }
                // レンタル更新申請：処理中
            } elseif ($tolRentalApplication['rentalUpdateApplicationStatus'] === 1) {
                // 本人確認不要(4)(16)-3
                if ($tolRentalApplication['identificationConfirmationNecessityFlag'] === 0) {
                    return [
                        'itemNumber' => 3,
                        'rentalExpirationDate' => ''
                    ];
                    // 本人確認必要(5)(17)-4
                } else {
                    return [
                        'itemNumber' => 4,
                        'rentalExpirationDate' => ''];
                }
                // その他
            } else {
                // 本人確認必要(7)(19)(物販落ちのケース)-5
                if ($tolRentalApplication['identificationConfirmationNecessityFlag'] === 1) {
                    return [
                        'itemNumber' => 5,
                        'rentalExpirationDate' => ''
                    ];
                }
                // その他(1)(13)-1
                return [
                    'itemNumber' => 1,
                    'rentalExpirationDate' => ''
                ];
            }
        }
    }
}
