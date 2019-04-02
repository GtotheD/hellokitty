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
     * @return bool
     * @throws \Exception
     */
    public function get()
    {
        Log::info('Member Premium Status API tolId : ' . $this->tolId);
        $this->memId = $this->decodeMemid($this->key, $this->tolId);
        Log::info('convert tolId : ' . $this->tolId . ' -> ' . $this->memId );

        // 定額レンタル操作 mfr001
        $tolFlatRentalOperationModel = new TolFlatRentalOperation($this->memId);
        $tolFlatRentalOperationCollection = $tolFlatRentalOperationModel->getDetail();
        if (empty($tolFlatRentalOperationCollection)) {
            return false;
        }
        $tolFlatRentalOperation = current($tolFlatRentalOperationCollection->all());
        if ( $tolFlatRentalOperation['responseStatus1'] !== '00') {
            return false;
        }
        return true;
    }
}
