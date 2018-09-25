<?php

namespace App\Repositories;

use App\Model\People;
use App\Model\Product;
use App\Model\Work;
use App\Repositories\WorkRepository;

class RecommendTheaterRepository extends BaseRepository
{
    const ROLE_ID_ORIGINAL_AUTHOR = 'EXT0000000UQ';
    const ROLE_ID_PERFORMER = 'EXT0000000UM';
    const ROLE_ID_DIRECTOR = 'EXT0000000UH';

    const BIG_GENRE_ID_ANIME = 'EXT0000001CL';
    const BIG_GENRE_ID_ACTION = 'EXT00000018Q';
    const BIG_GENRE_ID_HORROR = 'EXT00000022S';
    const BIG_GENRE_ID_DRAMA = 'EXT0000001DO';
    const BIG_GENRE_ID_SF = 'EXT0000002GF';
    const BIG_GENRE_ID_JP_HORROR = 'EXT0000001DL';
    const BIG_GENRE_ID_JP_ACTION = 'EXT00000016A';
    const BIG_GENRE_ID_JP_DRAMA = 'EXT00000014Q';
    const BIG_GENRE_ID_JP_SF = 'EXT0000000ZQ';

    /*
     * メインメソッド
     * 該当のHimo作品IDのジャンルIDから、リコメンド情報を取得する。
     * ジャンルによってリコメンドする情報が異なる。
     */
    public function get($workId)
    {
        // レンタルのみ表示する。
        $this->saleType = 'rental';
        $workModel = new Work();

        $work = $workModel->setConditionByWorkId($workId)->selectCamel(['big_genre_id'])->getOne();
        // 該当のジャンルを取得
        switch ($work->bigGenreId) {
            case self::BIG_GENRE_ID_ANIME:
                return $this->getPeopleWorks($workId, [self::ROLE_ID_ORIGINAL_AUTHOR]);
                break;
            case self::BIG_GENRE_ID_ACTION:
                $genreId = self::BIG_GENRE_ID_ACTION;
                return $this->getGenreWorks($genreId);
                break;
            case self::BIG_GENRE_ID_HORROR:
                $genreId = self::BIG_GENRE_ID_HORROR;
                return $this->getGenreWorks($genreId);
                break;
            case self::BIG_GENRE_ID_DRAMA:
                $genreId = self::BIG_GENRE_ID_DRAMA;
                return $this->getGenreWorks($genreId);
                break;
            case self::BIG_GENRE_ID_SF:
                $genreId = self::BIG_GENRE_ID_SF;
                return $this->getGenreWorks($genreId);
                break;
            case self::BIG_GENRE_ID_JP_HORROR:
            case self::BIG_GENRE_ID_JP_ACTION:
            case self::BIG_GENRE_ID_JP_DRAMA:
            case self::BIG_GENRE_ID_JP_SF:
                return $this->getPeopleWorks($workId, [self::ROLE_ID_PERFORMER, self::ROLE_ID_DIRECTOR]);
                break;

        }
        return null;
    }

    /*
     * ロールID順にキャストスタップを取得する。
     */
    public function getPerson($roleIds, $productUniqueId)
    {
        $people = new People();
        $person = null;
        foreach ($roleIds as $roleId) {
            $person = $people->setConditionByRoleId($productUniqueId, $roleId)->toCamel()->getOne();
            if (!empty($person)) break;
        }
        return $person;
    }

    /*
     * ジャンルID別の作品一覧を取得する。
     */
    public function getGenreWorks($genreId)
    {
        $workRepository = new WorkRepository();
        $workRepository->setSaleType($this->saleType);
        $reponse = $workRepository->genre($genreId);
        $this->hasNext = $workRepository->getHasNext();
        $this->totalCount = $workRepository->getTotalCount();
        return $reponse;
    }

    /*
     * ジャンルID別の作品一覧を取得する。
     */
    public function getPeopleWorks($workId, Array $personIds)
    {
        $workRepository = new WorkRepository();
        $productModel = new Product();

        $product = $productModel->setConditionByWorkId($workId)->selectCamel(['product_unique_id'])->getOne();
        $person = $this->getPerson($personIds, $product->productUniqueId);
        $workRepository->setSaleType($this->saleType);
        $reponse = $workRepository->person($person->personId);
        $this->hasNext = $workRepository->getHasNext();
        $this->totalCount = $workRepository->getTotalCount();
        return $reponse;
    }

}
