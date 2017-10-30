<?php
namespace App\Repositories;

use App\Model\Structure;

/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/13
 * Time: 15:01
 */
class StructureRepository
{
    const DVD = '1';
    const BOOK = '2';
    const CD = '3';
    const GAME = '4';

    const RENTAL = '1';
    const SELL = '2';

    protected $structure;

    public function __construct()
    {
        $this->structure = New Structure;
    }

    public function get($goodsType, $saleType) {
        $goodsType = $this->convertGoodsTypeToId($goodsType);
        $saleType = $this->convertSaleTypeToId($saleType);
        $structures = $this->structure->getStructure($goodsType, $saleType);
        $rows = [];
        foreach ($structures as $structure) {
            $apiUrl = null;
            if (!empty($structure->section_file_name)) {
                $apiUrl = $structure->api_url . '/' . $structure->section_file_name;
            } else {
                $apiUrl = $structure->api_url;
            }
            $rows[] =
                [
                    'areaType' => $structure->section_type,
                    'title' => $structure->title,
                    'apiUrl' => $apiUrl,
                    'linkUrl' => '',
                    'isTapOn' => $structure->is_tap_on,
                    'isRanking' => $structure->is_ranking,
                ];
        }
        return $rows;
    }

    private function convertGoodsTypeToId($goodsType) {
        switch ($goodsType) {
            case 'dvd':
                return self::DVD;
            case 'book':
                return self::BOOK;
            case 'cd':
                return self::CD;
            case 'game':
                return self::GAME;
            default:
                return false;
        }
    }

    private function convertSaleTypeToId($saleType) {
        switch ($saleType) {
            case 'rental':
                return self::RENTAL;
            case 'sell':
                return self::SELL;
            default:
                return false;
        }
    }

}