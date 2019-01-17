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

    /**
     * @param $key
     * @param $target
     * @return string
     */
    public function encrypt($key, $target)
    {
        $randNumber = '';
        for ($i = 0; $i < 6; $i++) {
            $randNumber .= mt_rand(0, 9);
        }
        $strings = sprintf('%010s%s', $target, $randNumber);
        return openssl_encrypt($strings, 'aes-128-ecb', $key, OPENSSL_RAW_DATA);
    }


}