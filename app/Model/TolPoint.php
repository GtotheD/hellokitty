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
        'responseCode', // 1 会員種別
        'membershipType', // 1 会員種別
        'point', // 2 利用可能ポイント数
        'fixedPointTotal', // 3 期間固定情報：期間固定ポイント数合計
        'fixedPointMinLimitTime', // 期間固定情報：期間固定ポイント最短有効期限
        'fixedPointCount', // 期間固定情報：期間固定ポイント件数
    ];

    public function getDetail($shopCode)
    {
        // todo:　CSVで取得してきたものを変換して返却する
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
