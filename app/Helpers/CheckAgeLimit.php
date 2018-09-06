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
    // イメージアイドルジャンル
    $imageIdolGenre = 'EXT0000002G9';
    $map = config('age_limit_map');
    $makerMap = config('age_limit_maker_map');
    foreach ($map as $item) {
        $result = [];
        // nullじゃなかった場合に比較対象とする。
        foreach ($item as $key => $value) {
            if ($key === 'ageLimit') {
                continue;
            }

            if ($item[$key] !== null) {
                if($item[$key] === $$key) {
                    $result[] = true;
                } else {
                    $result[] = false;
                }
            }
            // big genreがイメージアイドルだった場合はメーカーリストから検索し
            // 該当する場合は、アダルト判定とする。
            if ($item['bigGenreId'] === $imageIdolGenre) {
                if(in_array($makerCd, $makerMap)) {
                    $result[] = true;
                }
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
