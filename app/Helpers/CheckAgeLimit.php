<?php

/*
 * 年齢認証有無をチェックする。
 * @return bool true=年齢認証なしもしくは許可済み, false=要年齢認証で許可なし
 */
function checkAgeLimit($ageLimitCheck, $ratingId, $adultFlg, $bigGenreId, $mediumGenreId, $smallGenreId, $makerCd)
{
    // 年齢認証済みの場合は常に許可
    if ($ageLimitCheck == 'true') {
        return true;
    }
    // 認証済みでなかったら処理を実施
    // アダルトフラグがたっている場合は非表示
    if ($adultFlg == 1) {
        return false;
    }

    // リストにヒットしていた場合はfalseで返却
    if (isAdult($ratingId, $bigGenreId, $mediumGenreId, $smallGenreId, $makerCd)
        === true) {
        return false;
    }
    return true;
}

function isAdult($ratingId, $bigGenreId, $mediumGenreId, $smallGenreId, $makerCd)
{
    $imageIdolGenre = 'EXT0000002G9';

    $map = config('age_limit_map');
    $makerMap = config('age_limit_maker_map');
    foreach ($map as $item) {
        $result = [];
        // nullじゃなかった場合に比較対象とする。
        if ($item['ratingId'] !== null) {
            if($item['ratingId'] === $ratingId) {
                $result[] = true;
            }
        }
        if ($item['bigGenreId'] !== null) {
            if($item['bigGenreId'] === $bigGenreId) {
                $result[] = true;
            }
        }
        if ($item['mediumGenreId'] !== null) {
            if($item['ratmediumGenreIdingId'] === $mediumGenreId) {
                $result[] = true;
            }
        }
        if ($item['smallGenreId'] !== null) {
            if($item['smallGenreId'] === $smallGenreId) {
                $result[] = true;
            }
        }
        // big genreがイメージアイドルだった場合はメーカーで検索する
        if ($item['bigGenreId'] === $imageIdolGenre) {
            if(in_array($makerCd, $makerMap)) {
                $result[] = true;
            }
        }
        // falseが見つからなかった場合は全て一致いしている為にアダルト判定とする。
        // 一度でもヒットした場合は後続を動かす必要がないので、trueを返却する。
        if(!in_array(false, $result)) {
            return true;
        }
    }
    return false;
}
