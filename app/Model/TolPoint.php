<?php
/**
 * Created by PhpStorm.
 * User: usuda
 * Date: 2018/11/29
 * Time: 16:05
 */

namespace App\Model;

/**
 * Tポイント取得API
 * @package App\Model
 */
class TolPoint extends TolBaseModel
{
    private $header = [
        'responseCode', // 1 レスポンスコード
        'membershipType', // 2 会員種別
        'point', // 3 利用可能ポイント数
        'fixedPointTotal', // 4 期間固定情報：期間固定ポイント数合計
        'fixedPointMinLimitTime', // 5 期間固定情報：期間固定ポイント最短有効期限
        'fixedPointCount', // 6 期間固定情報：期間固定ポイント件数
    ];

    public function getDetail($shopCode)
    {
        $xml = $this->tolClient->getPoint($shopCode);
        $pointXml = simplexml_load_string($xml);
        // レスポンスステータスが0でなかった場合はエラーとしてfalseを返却
        if ($pointXml === false || current($pointXml->status) !== '0') {
            return false;
        }
        $csv = current($pointXml->responseData);

        return $this->getCollectionFromCSV($this->header, $csv);
    }
}
