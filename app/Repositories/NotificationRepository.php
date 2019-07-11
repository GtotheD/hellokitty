<?php

namespace App\Repositories;

use App\Exceptions\NoContentsException;
use App\Libraries\Security;
use App\Model\TolFlatRentalOperation;
use App\Model\TolNotification;

class NotificationRepository extends ApiRequesterRepository
{
    private $tolId;
    use Security;

    /**
     * TOL会員状態取得
     * NotificationRepository constructor.
     * @param $tolId
     */
    public function __construct($tolId = '')
    {
        parent::__construct();
        $this->tolId = $tolId;
    }

    /**
     * TOL会員状態取得
     * @return \SimpleXMLElement
     * @throws NoContentsException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getNotificationStatus()
    {
        $notification = new TolNotification($this->tolId);
        $result = $notification->getStatus();

        return $result;
    }

    /**
     * @param int $chkReservation
     * @return mixed|string|null
     * @throws NoContentsException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateNotification($chkReservation = 0) {
        $notification = new TolNotification($this->tolId);
        $result = $notification->updateStatus($chkReservation);

        return $result;
    }

}
