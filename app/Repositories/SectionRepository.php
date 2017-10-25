<?php
namespace App\Repositories;

use App\Repositories\TWSRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/13
 * Time: 15:02
 */

class SectionRepository
{

    public function fixBanner() {
        $rows = [

        ];
        return $rows;
    }

    public function normal($goodsName, $typeName, $sectionName) {
        $rows = [
            'totalCount' => 10,
            'limit' => 10,
            'offset' => 0,
            'page' => 1,
            'hasNext' => true,
            'rows' => [
                [
                    'dispStartDate'=> '2017-01-01',
                    'dispEndDate'=> '2017-01-01',
                    'saleStartDate'=> '2017-01-01',
                    'rentalStartDate'=> '2017-01-01',
                    'imageUrl'=> 'https://tsutaya.jp/image/a.jpg',
                    'title' => 'ラ・ラ・ランド',
                    'supplement' => 'エマ・ストーン', // アーティスト名、著者、機種等
                    'code' => 'JAN_CODE',
                    'urlCode' => 'url code',
                    'rate' => 2
                ],
                [
                    'dispStartDate'=> '2017-01-01',
                    'dispEndDate'=> '2017-01-01',
                    'saleStartDate'=> '2017-01-01',
                    'rentalStartDate'=> '2017-01-01',
                    'imageUrl'=> 'https://tsutaya.jp/image/a.jpg',
                    'title' => 'ワイルド・スピード　ＩＣＥ　ＢＲＥＡＫ',
                    'supplement' => 'ヴィン・ディーゼル', // アーティスト名、著者、機種等
                    'code' => 'JAN_CODE',
                    'urlCode' => 'url code',
                    'rate' => 3
                ],
                [
                    'dispStartDate'=> '2017-01-01',
                    'dispEndDate'=> '2017-01-01',
                    'saleStartDate'=> '2017-01-01',
                    'rentalStartDate'=> '2017-01-01',
                    'imageUrl'=> 'https://tsutaya.jp/image/a.jpg',
                    'title' => '美女と野獣',
                    'supplement' => 'エマ・ワトソン', // アーティスト名、著者、機種等
                    'code' => 'JAN_CODE',
                    'urlCode' => 'url code',
                    'rate' => 4
                ],
            ]
        ];
        return $rows;
    }

    public function banner($goodsName, $typeName, $sectionName) {
        $rows = [

        ];
        return $rows;
    }

    public function ranking($genreCode) {
        $genreMap = config('genre_map');
        if (key_exists($genreCode, $genreMap)) {
            $rankingConcentrationCd = $genreMap[$genreCode];
        } else {
            throw new NotFoundHttpException();
        }
        $tws = new TWSRepository;
        return $tws->ranking($rankingConcentrationCd)->get();
    }
}