<?php

namespace App\Repositories;

use App\Model\Promotion;
use App\Model\PromotionWork;
use App\Model\PromotionPrize;
use App\Model\PromotionQes;
use App\Model\PromotionAns;
use App\Model\Product;
use App\Exceptions\NoContentsException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PromotionRepository extends BaseRepository
{
    const COMMONCAUTION = '固定文言';

    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        parent::__construct($sort, $offset, $limit);
        $this->promotion = new Promotion();
    }

    /**
    * @return columns for API work/{workId}
    */
    public function selectColumns()
    {
        $columns = [
            'promotion_id',
            'outline',
            'title',
            'main_image',
            'thumb_image',
            'image',
            'caution',
            'promotion_start_date',
            'promotion_end_date'
        ];
        return $columns;
    }

    public function get($promotionId, $multiple = false, $selectColumns = null)
    {
        if (!$multiple) {
            $this->promotion->setConditionByPromotionId($promotionId);
            if (empty($selectColumns)) {
                $response = $this->promotion->toCamel(['created_at', 'updated_at'])->getOne();
            } else {
                $response = $this->promotion->selectCamel($selectColumns)->getOne();
            }
        } else {
            $this->promotion->setConditionByPromotionIds($promotionId);
            if (empty($selectColumns)) {
                $response = $this->promotion->toCamel(['created_at', 'updated_at'])->get()->toArray();
            } else {
                $response = $this->promotion->selectCamel($selectColumns)->get()->toArray();
            }
        }

        return $response;
    }

    /**
     * get promotion data for API /promotion/{promotion_id}
     * @param string $promotion_id
     * @return array
     */
    public function getPromotionData($promotion_id)
    {
        $promotion_work = new PromotionWork();
        $promotion_prize = new PromotionPrize();
        $promotion_qes = new PromotionQes();
        $promotion_ans = new PromotionAns();

        $response['promotion'] = $this->get($promotion_id);
        $arr = ['promotion_work', 'promotion_prize', 'promotion_qes', 'promotion_ans'];
        foreach ($arr as $table) {
            $response[$table] = ${$table}->setConditionPromotionId($promotion_id)
                                                         ->toCamel(['created_at', 'updated_at'])
                                                         ->get()
                                                         ->toArray();
        }

        return $this->formatOutputPromotion($response);
    }

    /**
     * format output promotion data of API /promotion/{promotion_id}
     * @param string $jan
     * @return array
     */
    public function formatOutputPromotion($data)
    {
        $now = Carbon::now();
        $result = [];
        $result['id'] = $data['promotion']->promotionId;
        $result['title'] = $data['promotion']->title;
        $result['mainImage'] = $data['promotion']->mainImage;
        $result['thumbImage'] = $data['promotion']->thumbImage;
        $result['outline'] = $data['promotion']->outline;
        $startDate = $data['promotion']->promotionStartDate;
        $endDate = $data['promotion']->promotionEndDate;
        $result['periodFlg'] = strtotime($startDate) <= strtotime($now) && strtotime($now) <= strtotime($endDate);
        $result['promotionDates'] = [
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
        $result['target'] = $data['promotion']->target;

        // get data for work
        $result['works'] = [];
        foreach ($data['promotion_work'] as $work) {
            $saleType = strlen($work->jan) == 13 ? 'sell' : (strlen($work->jan) == 9 ? 'rental' : null);
            $result['works'][] = [
                'workId' => $work->workId,
                'workTitle' => $work->workTitle,
                'saleType' => $saleType
            ];
        }

        $supplements = json_decode($data['promotion']->supplement);
        $result['cautions'] = [
            'caution' => $data['promotion']->caution,
            'commonCaution' => self::COMMONCAUTION,
            'supplements' => $supplements
        ];

        // list prize
        $prizes = [];
        foreach ($data['promotion_prize'] as $prize) {
            $prizes[] = [
                'sort' => $prize->sort,
                'text' => $prize->text
            ];
        }
        $result['prize'] = [
            'image' => $data['promotion']->image,
            'list' => $prizes
        ];

        // list question and answer
        $qes_ans = [];
        foreach ($data['promotion_qes'] as $qes) {
            $ans_arr = [];
            foreach ($data['promotion_ans'] as $ans) {
                if ($qes->sort == $ans->sortQes) {
                    $ans_arr[] = [
                        'sort' => $ans->sort,
                        'text' => $ans->text
                    ];
                }
            }
            $qes_ans[] = [
                'sort' => $qes->sort,
                'format' => $qes->format,
                'text' => $qes->text,
                'answer' => $ans_arr
            ];
        }
        $result['questionnaire'] = $qes_ans;
        
        return $result;
    }

    /**
     * get promotion data for API work/{workId}
     * @param string $workId, boolean $multiple, array $selectColumns
     * @return array
     */
    public function getPromotionDataForWork($workId = null, $saleType = null, $multiple = false, $selectColumns = null)
    {
        $result = [];
        if (isset($workId)) {
            $promotionWork = new PromotionWork();
            $list_prom_ids = $promotionWork->setConditionByWorkId($workId, $saleType)->get()->pluck('promotion_id')->toArray();
            $result = $this->get($list_prom_ids, $multiple, $this->selectColumns());
        }

        return $this->formatOutputPromotionForWorkDetail($result);
    }

    /**
     * format output promotion data of API work/{workId}
     * @param string $jan
     * @return array
     */
    public function formatOutputPromotionForWorkDetail($array)
    {
        $results = [];
        foreach ($array as $obj) {
            $now = Carbon::now();
            if (strtotime($obj->promotionEndDate) < strtotime($now) || strtotime($now) < strtotime($obj->promotionStartDate)) {
                continue;
            }
            $promotion = [];
            foreach ($obj as $key => $value) {
                if ($key == 'promotionStartDate') {
                    $key = 'startDate';
                }
                if ($key == 'promotionEndDate') {
                    $key = 'endDate';
                } 
                if ($key == 'promotionId') {
                    $key = 'id';
                }
                if ($key == 'image') {
                    $key = 'presentImage';
                }
                $promotion[$key] = $value;
            }
            $results[] = $promotion;
        }

        return $results;
    }
}
