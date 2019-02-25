<?php

namespace App\Repositories;

use App\Exceptions\NoContentsException;
use App\Model\RelatedPeople;

class RecommendArtistRepository extends BaseRepository
{
    protected $relatedPeople;
    const ROLE_ID_ARTIST = 'EXT00000000D';
    const HIMO_DEFAULT_LIMIT = 50;
    const HIMO_DEFAULT_OFFSET = 0;
    const RELATION_TYPE_ID = array('EXT0000776ES','EXT0000776ET','EXT0000776EI','EXT0000776FV','EXT0000776FY','EXT0000776EK','EXT0000776EM');

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        parent::__construct($sort, $offset, $limit);
        $this->relatedPeople = new RelatedPeople();
    }

    /**
     * GET: /work/{workId}/recommend/artist
     *
     * @param $workId
     * @return array
     *
     * @throws NoContentsException
     */
    public function getArtist($workId)
    {
        $peopleReposiroty = new PeopleRepository();
        $skipColumns = [
            'id',
            'people_id',
            'created_at',
            'updated_at',
        ];

        // STEP 1: Get newest people from workId in system
        $newestPeople = $peopleReposiroty->getNewsPeople($workId, null, self::ROLE_ID_ARTIST);
        if (!$newestPeople) {
            throw new NoContentsException();
        }

        // STEP 2: Set condition for related_people and get related artist in system.
        $relatedPeople = $this->relatedPeople->setConditionByPeople($newestPeople->person_id);
        if ($relatedPeople->count() == 0) {
            // STEP 2.1: In case related artist empty, call HIMO API and get data
            $himoRepositroy = new HimoRepository($this->sort, self::HIMO_DEFAULT_OFFSET, self::HIMO_DEFAULT_LIMIT);
            $relatedPeopleHimo = $himoRepositroy->searchRelatedPeople($newestPeople->person_id)->get();
            if (empty($relatedPeopleHimo['results']['total'])) {
                throw new NoContentsException();
            }

            // 関連人物の表示対象を絞る
            foreach (self::RELATION_TYPE_ID as $typeId) {
                $relatedPeopleInsert = [];
                foreach ($relatedPeopleHimo['results']['rows'] as $key => $row) {
                    if ($typeId === $row['relation_type_id']) {
                        $relatedPeopleInsert[] = $this->format($newestPeople->person_id, $row);
                    }
                }
                // STEP 2.2: Insert to related_people TBL
                $relatedPeople->insertBulk($relatedPeopleInsert);
            }
        }

        // STEP 3: Get related_people from system and return response
        $this->totalCount = $relatedPeople->count();
        if ($this->totalCount === 0) {
            throw new NoContentsException();
        }
        $result = $relatedPeople->toCamel($skipColumns)->limit($this->limit)->offset($this->offset)->orderBy('id', 'asc')->get();

        if (count($result) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }

        $response = [
            'total' => $this->totalCount,
            'hasNext' => $this->hasNext,
            'rows' => $result
        ];
        return $response;
    }

    public function format($peopleId, $row)
    {
        return ['people_id' => $peopleId,
            'person_id' => $row['person']['person_id'],
            'person_name' => $row['person']['person_name']
        ];

    }
}
