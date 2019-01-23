<?php

namespace App\Repositories;

use App\Model\TolFlatRentalOperation;
use App\Model\TolMemberDetail;
use App\Model\TolCMemberDetail;
use App\Model\TolRentalApplication;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use \App\Libraries\Security;

/**
 * Class RentalUseRegistrationRepository
 * @package App\Repositories
 */
class RentalUseRegistrationRepository extends BaseRepository
{
    private $tolId;
    private $memId;
    private $key;
    use Security;

    /**
     * RentalUseRegistrationRepository constructor.
     * @param $tolId
     * @param string $sort
     * @param int $offset
     * @param int $limit
     */
    public function __construct($tolId, string $sort = 'asc', int $offset = 0, int $limit = 10)
    {
        $this->tolId = $tolId;
        parent::__construct($sort, $offset, $limit);
        $this->key = env('TOL_ENCRYPT_KEY');
    }

    /**
     * レンタル利用登録項番取得
     * アプリ利用登録概要書_201801217.xlsx アプリ上におけるボタンを表示判定 の表を参照
     * @return array|bool
     * @throws \App\Exceptions\NoContentsException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get()
    {
        Log::info('rental use registration tolId : ' . $this->tolId);
        $this->memId = $this->decodeMemid($this->key, $this->tolId);
        Log::info('convert tolId : ' . $this->tolId . ' -> ' . $this->memId );

        // TOL会員状態取得
        $tapRepository = new TAPRepository;
        $tolMembershipStatus = $tapRepository->getMemberStatus($this->tolId);
        if (empty($tolMembershipStatus)) {
            $this->log('Request TAP-API MemberStatus', 'Data acquisition error.');
            return false;
        }
        // stetusのスペルが違うのはレスポンスがタイポされている為
        $tolMembershipStatus = $tolMembershipStatus['entry']['memberStetus'];

        /**
         * 非表示の項番・その他情報は返さない
         */
        // ネットT会員(91)-17
        if ($tolMembershipStatus['tmflg'] !== '2') {
            return [
                'itemNumber' => 17,
                'rentalExpirationDate' => ''
            ];
        }

        /*
         * いずれか取得できなかった場合は処理を継続しない
         */
        // 会員照会API mmc200
        $tolMemberDetailModel = new TolMemberDetail($this->memId);
        $tolMemberDetailCollection = $tolMemberDetailModel->getDetail();
        if (empty($tolMemberDetailCollection)) {
            $this->log('Request TOL-API MMC200',
                'Data acquisition error.'
            );
            return false;
        }
        $tolMemberDetail = current($tolMemberDetailCollection->all());
        // 正常終了でなかった場合は、NoContentsにする為にfalseリターンする。
        if ($tolMemberDetail['responseStatus1'] !== '00') {
            $this->log('Request TOL-API MMC200',
                'Error Response (' . $tolMemberDetail['responseStatus1'] . ')'
            );
            return false;
        }

        // 定額レンタル操作 mfr001
        $tolFlatRentalOperationModel = new TolFlatRentalOperation($this->memId);
        $tolFlatRentalOperationCollection = $tolFlatRentalOperationModel->getDetail();
        if (empty($tolFlatRentalOperationCollection)) {
            $this->log('Request TOL-API MFR001',
                'Data acquisition error.'
            );
            return false;
        }
        $tolFlatRentalOperation = current($tolFlatRentalOperationCollection->all());
        if ($tolFlatRentalOperation['responseStatus1'] !== '00' &&
            $tolFlatRentalOperation['responseStatus1'] !== '01') {
            $this->log('Request TOL-API MFR001',
                'Error Response (' . $tolFlatRentalOperation['responseStatus1'] . ')'
            );
            return false;
        }

        // レンタル関連申請API mre001
        $tolRentalApplicationModel = new TolRentalApplication($this->memId);
        $tolRentalApplication = $tolRentalApplicationModel->getDetail();
        if (empty($tolRentalApplication)) {
            $this->log('Request TOL-API MRE001',
                'Data acquisition error.'
            );
            return false;
        }
        // リターンコードを確認して、正常または、対象レコードなし以外の場合は処理を継続しない
        if ($tolRentalApplication['returnCd'] !== 'C1001' &&
            $tolRentalApplication['returnCd'] !== 'C2003'
        ) {
            $this->log('Request TOL-API MRE001',
                'Error Response (' . $tolRentalApplication['returnCd'] . ')'
            );
            return false;
        }

        // 当日
        $nowDatetime = Carbon::now()->format('Ymd');

        // 有効期限満了日の前月1日
        $prevMonthCarbon = new Carbon($tolMemberDetail['expirationDate']);
        // 31の場合31ない月でバグるので、startofMonthで1日にしてから前月を取得する
        $prevMonthCarbon->startofMonth()->subMonth();
        $prevMonth1st = $prevMonthCarbon->format('Ym01');
        $this->log('Run Time', $nowDatetime);
        $this->log('Response MMC200 Deleted flag', $tolMemberDetail['deleteFlag']);
        $this->log('Response MMC200 Expiration date', $tolMemberDetail['expirationDate']);
        $this->log('Response MMC200 One day before expiration date', $prevMonth1st);
        $this->log('Response MMC200 Member Type', $tolMemberDetail['memberType']);
        $this->log('Response MMC200 W-Card flag', $tolMemberDetail['wCardFlag']);
        $this->log('Response MMC200 C Member Count', $tolMemberDetail['cMemberInformationSetNumber']);
        $this->log('Response MFR001 Premium Member Status', $tolFlatRentalOperation['responseStatus1']);
        $this->log('Response MRE001 Rental registration status', $tolRentalApplication['rentalRegistrationApplicationStatus']);
        $this->log('Response MRE001 Rental update status', $tolRentalApplication['rentalUpdateApplicationStatus']);
        $this->log('Response MRE001 Rental update status', $tolRentalApplication['rentalUpdateApplicationStatus']);
        $this->log('Response MRE001 Identification flag', $tolRentalApplication['identificationConfirmationNecessityFlag']);

        // 削除済み会員(37~39,43~45,79~81)-8
        if ($tolMemberDetail['deleteFlag'] === '1') {
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
        // なし（C会員ではない） or W2（クレカ機能のみ止まっている）
        // (W1だった場合は、Tカード機能とクレジットを停止している会員の為レンタルさせない
        // Cメンバー状態を取得
        $cMemberCount = (int)$tolMemberDetail['cMemberInformationSetNumber'];
        if ($cMemberCount > 0) {
            for($i = 1; $i <= $cMemberCount; $i++) {
                $cMemberType = $tolMemberDetail['cMemberType'. (string)$i];
                if ($cMemberType !== 'W2' && $cMemberType !== '') {
                    return [
                        'itemNumber' => 6,
                        'rentalExpirationDate' => ''
                    ];
                }
            }
        }

        /**
         * レンタル会員
         */
        if ($tolMemberDetail['memberType'] === '1') {
            // まだ更新期間に入ってない(レンタル利用可)
            if ($prevMonth1st > $nowDatetime) {
                // 本人確認必要(55)-10
                if ($tolRentalApplication['identificationConfirmationNecessityFlag'] === '1') {
                    return [
                        'itemNumber' => 10,
                        'rentalExpirationDate' =>  $this->dateFormat($tolMemberDetail['expirationDate'])
                    ];
                // 本人確認不要(49)-9
                } else {
                    return [
                        'itemNumber' => 9,
                        'rentalExpirationDate' => $this->dateFormat($tolMemberDetail['expirationDate'])
                    ];
                }
            // 更新期間に入っている(レンタル利用可)
            } elseif ($prevMonth1st <= $nowDatetime && $nowDatetime <= $tolMemberDetail['expirationDate']) {
                // 「レンタル登録済み」：Wカード or プレミアム会員
                if ($tolMemberDetail['wCardFlag'] !== '00' || $tolFlatRentalOperation['responseStatus1'] === '00') {
                    // 本人確認必要(67-2,3,4)-15
                    if ($tolRentalApplication['identificationConfirmationNecessityFlag'] === '1') {
                        return [
                            'itemNumber' => 15,
                            'rentalExpirationDate' => $this->dateFormat($tolMemberDetail['expirationDate'])
                        ];
                    // 本人確認不要(61-2,3,4)-11
                    } else {
                        return [
                            'itemNumber' => 11,
                            'rentalExpirationDate' => $this->dateFormat($tolMemberDetail['expirationDate'])
                        ];
                    }
                    // レンタル更新処理中
                } elseif ($tolRentalApplication['rentalUpdateApplicationStatus'] === '1') {
                    // 本人確認必要(65)-14
                    if ($tolRentalApplication['identificationConfirmationNecessityFlag'] === '1') {
                        return [
                            'itemNumber' => 14,
                            'rentalExpirationDate' => $this->dateFormat($tolMemberDetail['expirationDate'])
                        ];
                    // 本人確認不要(64)-13
                    } else {
                        return [
                            'itemNumber' => 13,
                            'rentalExpirationDate' => $this->dateFormat($tolMemberDetail['expirationDate'])
                        ];
                    }
                    // 非Wカード＆非プレミアム会員
                } else {
                    // 本人確認必要(67-5)-16
                    if ($tolRentalApplication['identificationConfirmationNecessityFlag'] === '1') {
                        return [
                            'itemNumber' => 16,
                            'rentalExpirationDate' => $this->dateFormat($tolMemberDetail['expirationDate'])
                        ];
                    // 本人確認不要(61-5)-12
                    } else {
                        return [
                            'itemNumber' => 12,
                            'rentalExpirationDate' => $this->dateFormat($tolMemberDetail['expirationDate'])
                        ];
                    }
                }

            }
        }
        /**
         * 物販会員
         */
        if (($prevMonth1st > $nowDatetime) ||
            ($prevMonth1st <= $nowDatetime && $nowDatetime <= $tolMemberDetail['expirationDate'])) {
             // レンタル登録申請：処理中
            if ($tolRentalApplication['rentalRegistrationApplicationStatus'] === '1') {
                // 本人確認必要(3)(15)-2
                if ($tolRentalApplication['identificationConfirmationNecessityFlag'] === '1') {
                    return [
                        'itemNumber' => 2,
                        'rentalExpirationDate' => ''
                    ];
                }
            // レンタル更新申請：処理中
            } elseif ($tolRentalApplication['rentalUpdateApplicationStatus'] === '1') {
                // 本人確認必要(5)(17)-4
                if ($tolRentalApplication['identificationConfirmationNecessityFlag'] === '1') {
                    return [
                        'itemNumber' => 4,
                        'rentalExpirationDate' => ''];
                // 本人確認不要(4)(16)-3
                } else {
                    return [
                        'itemNumber' => 3,
                        'rentalExpirationDate' => ''
                    ];
                }
            }
            // 本人確認必要(7)(19)(物販落ちのケース)-5
            if ($tolRentalApplication['identificationConfirmationNecessityFlag'] === '1') {
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
        // 上記すべてが対応しない場合は、204で返却する為にfalseリターンする。
        return false;
    }

    private function log($title, $message)
    {
        Log::info("MEM_ID:" . $this->memId . " → " . $title . " : " . $message);
    }

    private function dateFormat($date)
    {
        return  date('Y-m-d H:i:s' ,strtotime($date));
    }
}
