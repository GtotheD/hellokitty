<?php

namespace App\Repositories;

use App\Exceptions\NoContentsException;
use App\Model\RelatedPeople;

class RecommendArtistRepository extends ApiRequesterRepository
{

    protected $sort;
    protected $offset;
    protected $limit;
    protected $hasNext;
    protected $totalCount;
    protected $relatedPeople;
    const ROLE_ID_ARTIST = 'EXT00000000D';
    const HIMO_DEFAULT_LIMIT = 50;
    const HIMO_DEFAULT_OFFSET = 0;

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        parent::__construct();
        $this->sort = $sort;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->relatedPeople = new RelatedPeople();
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @param mixed $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return mixed
     */
    public function getHasNext()
    {
        return $this->hasNext;
    }

    /**
     * @return mixed
     */
    public function getTotalCount()
    {
        return $this->totalCount;
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
        $newestPeople = $peopleReposiroty->getNewsPeople($workId, null, self::ROLE_ID_ARTIST)->getOne();
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
            $relatedPeopleInsert = [];
            foreach ($relatedPeopleHimo['results']['rows'] as $key => $row) {
                $relatedPeopleInsert[] = $this->format($newestPeople->person_id, $row);
            }
            // STEP 2.2: Insert to related_people TBL
            $relatedPeople->insertBulk($relatedPeopleInsert);
        }

        // STEP 3: Get related_people from system and return response
        $this->totalCount = $relatedPeople->count();
        $result = $relatedPeople->toCamel($skipColumns)->limit($this->limit)->offset($this->offset)->get();

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
