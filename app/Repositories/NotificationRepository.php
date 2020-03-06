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

    /**
     * TOLプッシュ通知パーミッション取得
     * @return \SimpleXMLElement
     * @throws NoContentsException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPushNotification($tolId)
    {
        $memId = $this->decodeMemid(env('TOL_ENCRYPT_KEY'), $tolId);
        $notification = new TolNotification($memId);
        $result = $notification->getPushNotification();

        return $result;
    }

    /**
     * TOLプッシュ通知パーミッション登録・取得
     * @return \SimpleXMLElement
     * @throws NoContentsException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function registPushNotification($tolId, $data)
    {
        $memId = $this->decodeMemid(env('TOL_ENCRYPT_KEY'), $tolId);
        $notification = new TolNotification($memId);
        $params = $this->convertParams($data);
        $result = $notification->registPushNotification($params);

        return $result;
    }

    /**
     * 変換パラメータ
     * @return array
     */
    private function convertParams($data)
    {
        $params = [];
        foreach ($data as $d) {
            if (isset($d['status'])) {
                $d['registerStatus'] = $d['status'];
                unset($d['status']);
            }
            $params[] = $d;
        }
        return $params;
    }

    /**
     * プッシュ通知パーミッションAPIのレスポンスのフォーマット
     * @return array
     */
    public function formatOutputPushNotification($data)
    {
        $result = [];
        $result['status'] = current($data->status);
        foreach ($data->array as $obj) {
            $result['data'][] = $obj;
        }

        return $result;
    }
}
