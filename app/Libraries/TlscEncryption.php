<?php

namespace App\Libraries;

/**
 * Tlsc用暗号化ユーティリティ
 *
 * @package CosmoCommons\Security\Tlsc
 */
trait TlscEncryption
{
    /**
     * @var array
     */
    protected $convertKeys;

    /**
     * @var array
     */
    protected $checkDegitWeight;

    // ヘッダー
    protected $tlscHeader = "TLSC";

    /**
     * TLSC番号に暗号化処理
     *
     * @param string $target 暗号化対象文字列
     * @return string TLSC番号
     */
    public function encrypt($target)
    {
        $result = null;

        if (!$this->isValid($target, 16)) {
            return $result;
        }

        //変換対象番号文字列16桁目
        $convertKeyParam = substr($target, -1);
        //変換キー取得
        $convertKey = $this->getConvertKey($convertKeyParam);
        if (!is_array($convertKey) || count($convertKey) !== 15) {
            return $result;
        }

        //変換後番号15桁目迄計算
        for ($i = 0; $i < count($convertKey); $i++) {
            $targetChar = substr($target, $i, 1);
            $convertKey[$i] += intval($targetChar);
            //変換対象文字列と変換キーの和が10以上の場合、10減算
            if (9 < $convertKey[$i]) {
                $convertKey[$i] -= 10;
            }
        }
        // 変換対象文字列、16桁目を連結
        $encryptData = implode($convertKey) . $convertKeyParam;
        //チェックデジット取得
        $checkDegit = $this->getCheckDegit($encryptData);
        //チェックデジット存在する場合
        if (!is_null($checkDegit)) {
            $result = $this->tlscHeader . $encryptData . $checkDegit;
        }

        return $result;
    }

    /**
     * TLSC番号を復号処理
     *
     * @param string $target 復号対象TLSC番号
     * @return string 復号文字列
     */
    public function decrypt($target)
    {
        if (!$this->isDecryptValid($target)) {
            return null;
        }
        // 復元対象番号文字列型数値部取得
        $targetArray = str_split(substr($target, 4, 21));
        // 変換キー取得用文字列
        $convertKeyParam = $targetArray[15];
        //変換キー取得
        $convertKey = $this->getConvertKey($convertKeyParam);
        if (!is_array($convertKey) || count($convertKey) !== 15) {
            return null;
        }

        //変換後番号15桁目迄計算
        for ($i = 0; $i < count($convertKey); $i++) {
            $convertKey[$i] = intval($targetArray[$i]) - $convertKey[$i];
            //変換対象文字列と変換キーの和が10以上の場合、10減算
            if ($convertKey[$i] < 0) {
                $convertKey[$i] += 10;
            }
        }
        // 文字列連結
        return implode($convertKey) . $convertKeyParam;
    }

    /**
     * 引数の文字列に対する変換キーを算出する。
     *
     * @param string $target 変換キー取得用文字列
     * @return array 変換キー
     */
    private function getConvertKey($target)
    {
        $result = [];

        if ($this->isValid($target) && isset($this->convertKeys[$target])) {
            $result = $this->convertKeys[$target];
        }

        return $result;
    }

    /**
     * 引数の文字列に対するチェックデジットを算出する。
     *
     * @param string $target チェックデジット算出用文字列
     * @return string チェックデジット
     */
    private function getCheckDegit($target)
    {
        $result = null;
        $workIntDegit = 0;

        if (!$this->isValid($target, 16)) {
            return $result;
        }

        //引数数値部加算
        for ($i = 0; $i < strlen($target); $i++) {
            $targetChar = substr($target, $i, 1);
            if (!is_numeric($targetChar)) {
                return $result;
            }
            $workIntDegit += $this->checkDegitWeight[$i] * intval($targetChar);
        }

        //チェックデジット算出
        $intDegit = 9 - ($workIntDegit % 9);
        //チェックデジット返却
        return strval($intDegit);
    }

    /**
     * 復号対象TLSC番号入力チェック
     * @param string $target 復号対象TLSC番号
     * @return boolean
     */
    private function isDecryptValid($target)
    {
        // 引数必須入力チェック
        if (is_null($target) || $target === '') {
            return false;
        }
        // 引数文字列長チェック
        if (strlen($target) !== 21) {
            return false;
        }

        // 引数ヘッダー固定値チェック
        if ($this->tlscHeader !== substr($target, 0, 4)) {
            return false;
        }

        //引数数値型チェック（5～21桁目）
        if (!is_numeric(substr($target, 4))) {
            return false;
        }

        //チェックデジット取得
        $checkDegit = $this->getCheckDegit(substr($target, 4, 16));
        //チェックデジット存在する場合
        if (is_null($checkDegit) || substr($target, -1) !== $checkDegit) {
            return false;
        }

        return true;
    }

    /**
     * 変換共通入力チェック
     * @param string $target 暗号化対象文字列
     * @param int $strLen 文字列数
     * @return boolean
     */
    private function isValid($target, $strLen = 1)
    {
        //引数必須入力チェック
        if (is_null($target) || $target === '') {
            return false;
        }
        //引数文字列長チェック
        if (strlen($target) !== $strLen) {
            return false;
        }
        //引数数値型チェック
        if (!is_numeric($target)) {
            return false;
        }
        return true;
    }
}
