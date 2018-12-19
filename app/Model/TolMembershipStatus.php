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
class TolMembershipStatus extends TolBaseModel
{

    public function getDetail() {
        $xml = $this->tolClient->getMembershipStatus();
        $memberDetailXml = simplexml_load_string($xml);
        // レスポンスステータスが0でなかった場合はエラーとしてfalseを返却
        if (current($memberDetailXml->status) !== 'SUCCESS') {
            return false;
        }
        return collect(json_decode(json_encode($memberDetailXml), true));
    }
}