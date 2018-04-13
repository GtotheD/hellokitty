<?php
namespace App\Repositories;

class RecommendArtistRepository extends ApiRequesterRepository
{

    protected $sort;
    protected $offset;
    protected $limit;
    protected $hasNext;
    protected $totalCount;

    const ROLE_ID_ARTIST = 'EXT00000000D';

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        parent::__construct();
        $this->sort = $sort;
        $this->offset = $offset;
        $this->limit = $limit;
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

    public function getArtist($workId)
    {
        $productRepository = new ProductRepository();
        $peopleReposiroty = new PeopleRepository();
        $newestPeople = $peopleReposiroty->getNewsPeople($workId, null, self::ROLE_ID_ARTIST)->getOne();
        dd($newestPeople);

        $product = new Product();
        $people = new People();
        $himo = new HimoRepository();
        $productResult = $product->setConditionByWorkIdNewestProduct($workId)->getOne();
        $people = $people->setConditionByProduct($productResult->product_unique_id)->getOne();
        $this->totalCount = $this->peopleRelatedWork->setConditionById($people->person_id)->count();
        $result = $this->peopleRelatedWork->toCamel(['id', 'person_id'])->get($this->limit, $this->offset);
        if (empty(count($result))) {
            $himoResult = $himo->searchPeople([$people->person_id], '0301', ['book'])->get();
            foreach ($himoResult['results']['rows'] as $row) {
                foreach ($row['works'] as $work) {
                    $insertData[] = $this->format($people->person_id, $work);
                }
            }
            $this->peopleRelatedWork->insertBulk($insertData);
            $result = $this->peopleRelatedWork->setConditionById($people->person_id)->toCamel(['id', 'person_id'])->get($this->limit, $this->offset);
        }
        if (count($result) + $this->offset < $this->totalCount) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }
        return $result;
    }

    public function format($personId, $row)
    {
        $workRepository = new WorkRepository();
        $base = $workRepository->format($row, true);
        $base['person_id'] = $personId;
        return $base;
    }
}