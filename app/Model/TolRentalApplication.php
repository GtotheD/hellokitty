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
class TolRentalApplication extends TolBaseModel
{
    public function getDetail()
    {
        $xml = $this->tolClient->getRentalApplication();
        $memberDetailXml = simplexml_load_string($xml);
        // レスポンスステータスが0でなかった場合はエラーとしてfalseを返却
        if ($memberDetailXml === false || current($memberDetailXml->status) !== '0') {
            return false;
        }
        $json = json_decode(urldecode($memberDetailXml->responseData));
        $common = $json->response->common;
        $detail = $json->response->detail;
        return [
            'returnCd' => $common->return_cd,
            'rentalRegistrationApplicationStatus' => $detail->rentaltorokushinseistatus,
            'rentalUpdateApplicationStatus' => $detail->rentalkoshinshinseistatus,
            'identificationConfirmationNecessityFlag' => $detail->honninkakuninyohi
        ];
    }
}
