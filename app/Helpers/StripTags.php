<?php
/**
 * brを改行コードに変換する。
 *
 *
 * @param string $contents
 *
 * @return string
 */
function StripTags($contents)
{
    $contents = preg_replace('/<br.?>/i', "\n", $contents);
    $contents = strip_tags($contents);
    return $contents;
}
