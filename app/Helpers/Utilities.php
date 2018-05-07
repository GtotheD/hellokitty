<?php
/**
 * Utilities common function
 */

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