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
 * Class StatusPremiumRepository
 * @package App\Repositories
 */
class StatusPremiumRepository extends BaseRepository
{
    private $tolId;
    private $memId;
    private $key;
    use Security;

    const EXCLUDE_STORE_CD = array(
        '0129',
        '2012',
        '2129',
        '4137',
        '4531',
        '5216',
        '5222',
        '5224',
        '5227',
        '7024',
        '4720',
        '5123',
        '5616',
        '5618',
        '5802',
        '5841',
        '5847',
        '5201',
        '7428',
        '5230',
        '0310',
        '0307',
        '3127',
        '7638',
        '2212',
        '7637',
        '7650',
        '0111',
        '4107',
        '9945'
    );

    const EXCLUDE_PLAN_CD = array(
        '520',
        '511',
        '513'
    );

    const TERMS_CODE = [
        '00002' => 'プレミアム',
        '00003' => 'コミック',
        '00004' => '音楽',
        '00005' => 'プレミアム＋コミック',
        '00006' => 'プレミアム＋音楽',
        '00007' => 'コミック＋音楽',
        '00008' => 'プレミアム＋音楽＋コミック',
        '00009' => 'プレミアム2000',
        '00010' => 'プレミアム2000＋コミック',
        '00011' => 'プレミアム2000＋音楽',
        '00012' => 'プレミアム2000＋音楽＋コミック'
    ];

    const TERMS_CODE_PREMIUM = ['00002', '00005'];
    const TERMS_CODE_COMIC = ['00003', '00005'];

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
     * プレミアム判定
     * @return array
     * @throws \Exception
     */
    public function get()
    {
        $result = [];
        Log::info('Member Premium Status API tolId : ' . $this->tolId);
        $this->memId = $this->decodeMemid($this->key, $this->tolId);
        Log::info('convert tolId : ' . $this->tolId . ' -> ' . $this->memId);

        // 定額レンタル操作 mfr001
        $tolFlatRentalOperationModel = new TolFlatRentalOperation($this->memId);
        $tolFlatRentalOperationCollection = $tolFlatRentalOperationModel->getDetail();
        if (empty($tolFlatRentalOperationCollection)) {
            return $result;
        }
        $tolFlatRentalOperation = current($tolFlatRentalOperationCollection->all());

        // Check premium
        $result['premium'] = in_array($tolFlatRentalOperation['termsCode'], self::TERMS_CODE_PREMIUM, true);

        // Check comic
        $result['comic'] = in_array($tolFlatRentalOperation['termsCode'], self::TERMS_CODE_COMIC, true);

        // Set plan
        $termsCode = self::TERMS_CODE;
        $result['plan'] = array_key_exists($tolFlatRentalOperation['termsCode'], $termsCode) ?
            $termsCode[$tolFlatRentalOperation['termsCode']] : '';

        //特定店舗・申し込みコードの人はプレミアムと判定しない
        $storeCd = self::EXCLUDE_STORE_CD;
        $planCd = self::EXCLUDE_PLAN_CD;
        if (in_array($tolFlatRentalOperation['storeCode'], $storeCd, true)) {
            if(in_array($tolFlatRentalOperation['flatPlanNumber'], $planCd, true)) {
                return false;
            }
        }
        
        return $result;
    }

    /**
     * プレミアム判定
     * @return bool
     * @throws \Exception
     */
    public function pre_get()
    {
        Log::info('Member Premium Status API tolId : ' . $this->tolId);
        $this->memId = $this->decodeMemid($this->key, $this->tolId);
        Log::info('convert tolId : ' . $this->tolId . ' -> ' . $this->memId);

        // 定額レンタル操作 mfr001
        $tolFlatRentalOperationModel = new TolFlatRentalOperation($this->memId);
        $tolFlatRentalOperationCollection = $tolFlatRentalOperationModel->getDetail();
        if (empty($tolFlatRentalOperationCollection)) {
            return false;
        }
        $tolFlatRentalOperation = current($tolFlatRentalOperationCollection->all());

        //T内部管理番号が取れない場合はfalse
        if (empty($tolFlatRentalOperation['tInternalControlNumber'])) {
            return false;
        }

        $tNaibu = $tolFlatRentalOperation['tInternalControlNumber'];


        return $tNaibu;
    }

}
