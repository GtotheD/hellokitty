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
            Log::info('tol membership status can\'t get　MemId：' . $this->memId);
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
            Log::info('mmc200 can\'t get　MemId：' . $this->memId);
            return false;
        }
        $tolMemberDetail = current($tolMemberDetailCollection->all());
        // 正常終了でなかった場合は、NoContentsにする為にfalseリターンする。
        if ($tolMemberDetail['responseStatus1'] !== '00') {
            return false;
        }

        // 定額レンタル操作 mfr001
        $tolFlatRentalOperationModel = new TolFlatRentalOperation($this->memId);
        $tolFlatRentalOperationCollection = $tolFlatRentalOperationModel->getDetail();
        if (empty($tolFlatRentalOperationCollection)) {
            Log::info('mfr001 can\'t get　MemId：' . $this->memId);
            return false;
        }
        $tolFlatRentalOperation = current($tolFlatRentalOperationCollection->all());
        if ($tolMemberDetail['responseStatus1'] !== '00' &&
            $tolMemberDetail['responseStatus1'] !== '01') {
            return false;
        }

        // レンタル関連申請API mre001
        $tolRentalApplicationModel = new TolRentalApplication($this->memId);
        $tolRentalApplication = $tolRentalApplicationModel->getDetail();
        if (empty($tolRentalApplication)) {
            Log::info('mre001 can\'t get　MemId：' . $this->memId);
            return false;
        }
        if ($tolMemberDetail['responseStatus1'] !== '00') {
            return false;
        }

        // 当日
        $nowDatetime = Carbon::now()->format('Ymd');

        // 有効期限満了日の前月1日
        $prevMonthCarbon = new Carbon($tolMemberDetail['expirationDate']);
        // 31の場合31ない月でバグるので、startofMonthで1日にしてから前月を取得する
        $prevMonthCarbon->startofMonth()->subMonth();
        $prevMonth1st = $prevMonthCarbon->format('Ym01');

        Log::info("mem_id:" . $this->memId . "\t現在時刻: ".$nowDatetime);
        Log::info("mem_id:" . $this->memId . "\tMMC200 削除済みフラグ: ".$tolMemberDetail['deleteFlag']);
        Log::info("mem_id:" . $this->memId . "\tMMC200 有効期限: ".$tolMemberDetail['expirationDate']);
        Log::info("mem_id:" . $this->memId . "\tMMC200 有効期限一ヶ月前の1日: ".$prevMonth1st);
        Log::info("mem_id:" . $this->memId . "\tMMC200 会員種別: ".$tolMemberDetail['memberType']);
        Log::info("mem_id:" . $this->memId . "\tMMC200 Wカードフラグ: ".$tolMemberDetail['wCardFlag']);
        Log::info("mem_id:" . $this->memId . "\tMMC200 C会員: ". (string)$isCMember);
        Log::info("mem_id:" . $this->memId . "\tMFR001 プレミアム会員: ".$tolFlatRentalOperation['responseStatus1']);
        Log::info("mem_id:" . $this->memId . "\tMRE001 レンタル登録申請: ".$tolRentalApplication['rentalRegistrationApplicationStatus']);
        Log::info("mem_id:" . $this->memId . "\tMRE001 レンタル更新申請: ".$tolRentalApplication['rentalUpdateApplicationStatus']);
        Log::info("mem_id:" . $this->memId . "\tMRE001 本人確認フラグ: ".$tolRentalApplication['identificationConfirmationNecessityFlag']);

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
         * レンタル
         */
        if ($tolMemberDetail['memberType'] === '1') {
            // まだ更新期間に入ってない(レンタル利用可)
            if ($prevMonth1st > $nowDatetime) {
                // 本人確認不要(49)-9
                if ($tolRentalApplication['identificationConfirmationNecessityFlag'] === '0') {
                    return [
                        'itemNumber' => 9,
                        'rentalExpirationDate' => $tolMemberDetail['expirationDate']
                    ];
                    // 本人確認必要(55)-10
                } else {
                    return [
                        'itemNumber' => 10,
                        'rentalExpirationDate' => $tolMemberDetail['expirationDate']
                    ];
                }
            // 更新期間に入っている(レンタル利用可)
            } elseif ($prevMonth1st <= $nowDatetime && $nowDatetime <= $tolMemberDetail['expirationDate']) {
                // 「レンタル登録済み」：Wカード or プレミアム会員
                if ($tolMemberDetail['wCardFlag'] !== '00' || $tolFlatRentalOperation['responseStatus1'] === '00') {
                    // 本人確認不要(61-2,3,4)-11
                    if ($tolRentalApplication['identificationConfirmationNecessityFlag'] === '0') {
                        return [
                            'itemNumber' => 11,
                            'rentalExpirationDate' => $tolMemberDetail['expirationDate']
                        ];
                        // 本人確認必要(67-2,3,4)-15
                    } else {
                        return [
                            'itemNumber' => 15,
                            'rentalExpirationDate' => $tolMemberDetail['expirationDate']
                        ];
                    }
                    // レンタル更新処理中
                } elseif ($tolRentalApplication['rentalUpdateApplicationStatus'] === '1') {
                    // 本人確認不要(64)-13
                    if ($tolRentalApplication['identificationConfirmationNecessityFlag'] === '0') {
                        return [
                            'itemNumber' => 13,
                            'rentalExpirationDate' => $tolMemberDetail['expirationDate']
                        ];
                        // 本人確認必要(65)-14
                    } else {
                        return [
                            'itemNumber' => 14,
                            'rentalExpirationDate' => $tolMemberDetail['expirationDate']
                        ];
                    }
                    // 非Wカード＆非プレミアム会員
                } else {
                    // 本人確認不要(61-5)-12
                    if ($tolRentalApplication['identificationConfirmationNecessityFlag'] === '0') {
                        return [
                            'itemNumber' => 12,
                            'rentalExpirationDate' => $tolMemberDetail['expirationDate']
                        ];
                        // 本人確認必要(67-5)-16
                    } else {
                        return [
                            'itemNumber' => 16,
                            'rentalExpirationDate' => $tolMemberDetail['expirationDate']
                        ];
                    }
                }

            }
        }
        /**
         * 物販
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
                // 本人確認不要(4)(16)-3
                if ($tolRentalApplication['identificationConfirmationNecessityFlag'] === '0') {
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
}
