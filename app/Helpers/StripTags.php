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
    $contents = preg_replace('/<br.?>/', "\n", $contents);
    $contents = strip_tags($contents);
    return $contents;
}
