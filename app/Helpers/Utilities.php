<?php
/**
 * Utilities common function
 */

const DOC_TYPE_ID_COMMENT = '01';
const DOC_TYPE_ID_SUMMARY = '02';

const DOC_TABLE_MOVIE = [
    'tol' => ['08', '04', '03', '07', '06', '01'],
];

const DOC_TABLE_MUSIC = [
    'tol' => ['08', '03', '01'],
];

const DOC_TABLE_BOOK = [
    'tol' => ['08', '02', '01'],
];

const DOC_TABLE_GAME = [
    'tol' => ['08', '01'],
];

/**
 * Reformat contents data.
 * Sample: ① Disc.1 1.飛燕 2.LOSER -> 1.飛燕 2.LOSER; ②【Disc-1】1.飛燕 2.LOSER -> 1.飛燕 2.LOSER
 *
 * @param string $contents
 *
 * @return string
 */
function contentsFormat($contents)
{
    $isDiscBeforeReg = "/^Disc.1|^【Disc-1】/";
    $isMutilDisc = "/Disc.[1-9]|【Disc-[1-9]】/";
    preg_match($isDiscBeforeReg, $contents, $m);
    preg_match_all($isMutilDisc, $contents, $m2);

    // If exists [Disc.1] in contents and no has any more Disc.1 or Disc.2 -> Remove Disc.1
    if (!empty($m[0]) && count($m2[0]) === 1) {
        return trim(str_replace(['【Disc-1】', 'Disc.1'],  '', $contents));
    }
    return $contents;
}

/**
 * Return baseMonth value format Y年m月
 *
 * @param $month
 *
 * @return false|string
 */
function getBaseMonth($month)
{
    $saleStartMonth = date('Y-m');
    if ($month === 'last') {
        $saleStartMonth = date('Y-m', strtotime('-1 months'));
    } else if ($month === 'next') {
        $saleStartMonth = date('Y-m', strtotime('+1 months'));
    }
    return $saleStartMonth;
}

/**
 * Change array key from $oldKey -> $newKey
 *
 * @param $array
 *
 * @param $oldKey
 * @param $newKey
 *
 * @return array
 */
function arrayChangeKey($array, $oldKey, $newKey)
{
    if (!array_key_exists($oldKey, $array))
        return $array;

    $keys = array_keys($array);
    $keys[array_search($oldKey, $keys)] = $newKey;

    return array_combine($keys, $array);
}

/**
 * あらすじ→コメントの順番で文書を取得する
 *
 * @param array $docTable 文書テーブル
 * @param array $docs 文書情報
 * @return array 付加情報
 */
function getSummaryComment(array $docTable, array $docs)
{
    $outline = '';
    $summaryOutline = '';
    $commentOutline = '';
    $summaryIndex = -1;
    $commentIndex = -1;

    for ($i = 0; $i < count($docTable); $i++) {
        foreach ($docs as $doc) {
            if ($doc['doc_type_id'] === DOC_TYPE_ID_SUMMARY &&
                $docTable[$i] === $doc['doc_source_id'] &&
                !empty($doc['doc_text'])
            ) {
                $summaryOutline = $doc['doc_text'];
                $summaryIndex = $i;
                break 2;
            }
        }
    }

    for ($i = 0; $i < count($docTable); $i++) {
        foreach ($docs as $doc) {
            if ($doc['doc_type_id'] === DOC_TYPE_ID_COMMENT &&
                $docTable[$i] === $doc['doc_source_id'] &&
                !empty($doc['doc_text'])
            ) {
                $commentOutline = $doc['doc_text'];
                $commentIndex = $i;
                break 2;
            }
        }
    }

    if (!empty($summaryOutline)) {
        if (!empty($commentOutline)) {
            if ($summaryIndex > $commentIndex) {
                $outline = $commentOutline;
            } else if ($summaryIndex < $commentIndex) {
                $outline = $summaryOutline;
            } else {
                $outline = $summaryOutline . "\n" . $commentOutline;
            }
        } else {
            $outline = $summaryOutline;
        }
    } else {
        $outline = $commentOutline;
    }

    return $outline;
}

