<?php

namespace App\Repositories;

use App\Model\People;
use App\Model\Product;

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

    // ランキング出力時のTWS集約ランキングID
    const RANKING_FOR_ANIME = 'D049';
    const RANKING_FOR_JP_MOVIE = 'D047';
    const RANKING_FOR_OTHER = 'D045';

    /*
     * メインメソッド
     * 該当のHimo作品IDのジャンルIDから、リコメンド情報を取得する。
     * ジャンルによってリコメンドする情報が異なる。
     */
    public function get($workId)
    {
        $workRepository = new WorkRepository();
        // レンタルのみ表示する。
        $this->saleType = 'rental';
        $work = $workRepository->get($workId);
        if (empty($work)) {
            return null;
        }
        // 該当のジャンルを取得
        switch ($work['bigGenreId']) {
            case self::BIG_GENRE_ID_ANIME:
                // これだけソートがnew
                $peopleWork = $this->getPeopleWorks($workId, [self::ROLE_ID_ORIGINAL_AUTHOR], 'new');
                // 上記でとれなかった場合は、ランキングを返却
                if (!empty($peopleWork)) {
                    return $peopleWork;
                } else {
                    // アニメのデイリーランキングを返却
                    return $this->getRanking(self::RANKING_FOR_ANIME);
                }
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
                // キャスト・スタッフ検索：出演者で取得、出演者がとれなかった場合は監督で取得
                $peopleWork = $this->getPeopleWorks($workId, [self::ROLE_ID_PERFORMER, self::ROLE_ID_DIRECTOR]);
                // 上記でとれなかった場合は、ランキングを返却
                if (!empty($peopleWork)) {
                    return $peopleWork;
                } else {
                    // 邦画のデイリーランキングを返却
                    return $this->getRanking(self::RANKING_FOR_JP_MOVIE);
                }
                break;
            default:
                // 全ジャンルのデイリーランキングを返却
                return $this->getRanking(self::RANKING_FOR_OTHER);
                break;
        }
    }

    public function getRanking($twsAggregationId)
    {
        $sectionRepository = new SectionRepository();
        $sectionRepository->setSort($this->sort);
        $sectionRepository->setLimit($this->limit);
        $sectionRepository->ranking('agg', $twsAggregationId, null);
        $this->hasNext = $sectionRepository->getHasNext();
        $this->totalCount = $sectionRepository->getTotalCount();
        return $sectionRepository->getRows();
    }

    /*
     * ジャンルID別の作品一覧を取得する。
     */
    public function getGenreWorks($genreId)
    {
        $workRepository = new WorkRepository();
        $workRepository->setSaleType($this->saleType);
        // お薦めにする為ソートをnull設定
        $workRepository->setSort(null);
        $workRepository->setLimit($this->limit);
        $response = $workRepository->genre($genreId, ['tol']);
        $this->hasNext = $workRepository->getHasNext();
        $this->totalCount = $workRepository->getTotalCount();
        return $response;
    }

    /*
     * 人物別の作品一覧を取得する。
     */
    public function getPeopleWorks($workId, Array $personIds, $sort = null)
    {
        $workRepository = new WorkRepository();
        $productModel = new Product();
        $product = $productModel->setConditionByWorkId($workId)->selectCamel(['product_unique_id'])->getOne();
        $person = $this->getPerson($personIds, $product->productUniqueId);
        if(empty($person)) {
            return null;
        }
        $workRepository->setSaleType('rental');
        $workRepository->setLimit($this->limit);
        $workRepository->setOffset($this->offset);
        // ソート：お薦め（nullを設定）、アイテム：DVD
        $response = $workRepository->person($person->personId, $sort, 'dvd' , ['tol']);
        $this->hasNext = $workRepository->getHasNext();
        $this->totalCount = $workRepository->getTotalCount();
        return $response;
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
}
