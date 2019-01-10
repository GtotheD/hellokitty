<?php
namespace App\Libraries;

use Exception;

/**
 * Trait Security
 * 暗号復号化トレイトクラス
 * @package App\Libraries
 */
trait Security
{
    /**
     * @param $key
     * @param $tolid
     * @return int
     * @throws Exception
     */
    public function decodeMemid($key, $tolid)
    {
        $encodedMemId = $this->decryptFromBase64String($key, urldecode($tolid));
        if (empty($encodedMemId)) {
            throw new Exception('Can\'t Convert TolID to MemID');
        }

        return intval(substr($encodedMemId, 0, 10));
    }

    /**
     * @param $key
     * @param $target
     * @return string
     */
    public function decryptFromBase64String($key, $target)
    {
        return openssl_decrypt(base64_decode($target), 'aes-128-ecb', $key, OPENSSL_RAW_DATA);
    }
}