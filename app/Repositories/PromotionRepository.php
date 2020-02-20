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
    const COMMONCAUTION = '・応募完了時にログインIDに登録されているTカード番号を取得させていただきます。<br>'.
                          '・ログインIDに登録しているTカード番号と当該作品レンタル時にご提示いただいTカード番号が異なる場合は、抽選の対象外となります。<br>'.
                          '&nbsp;&nbsp;&nbsp;ログインIDに登録されているTカード番号の確認は<a href=\'https://tsite.jp/tm/pc/accounts/STKIp0401001.do\'>こちら</a><br>'.
                          '・当選した場合、Tポイントの付与はログインIDに登録しているTカード番号になります。<br>'.
                          '・当選した場合、賞品の発送先はログインIDに登録されている住所（氏名宛）になります。<br>'.
                          '&nbsp;&nbsp;&nbsp;ログインIDに登録されている住所・氏名の確認は<a href=\'https://tsite.jp/tm/pc/accounts/STKIp0401001.do\'>こちら</a><br>'.
                          '・以下のいずれかに該当する場合、当選が無効となります。<br>'.
                          '&nbsp;&nbsp;&nbsp;&nbsp;- 当選賞品が進呈される前にTカード番号の変更、ログインIDの削除などを行った場合<br>'.
                          '&nbsp;&nbsp;&nbsp;&nbsp;- Tカードの有効期限切れや紛失等によりTカードが無効となった場合<br>'.
                          '<br>'.
                          '・今回のご入力を通じてお客様からご提供いただきました個人情報は、以下の企業が取得いたします。<br>'.
                          '【株式会社TSUTAYA（以下TSUTAYAといいます）が主催するキャンペーンまたはTSUTAYAへのご意見・ご要望及びTポイントの付与】<br>'.
                          '&nbsp;&nbsp;&nbsp;&nbsp;取得企業：TSUTAYA及びカルチュア・コンビニエンス・クラブ株式会社（以下CCCといいます）<br>'.
                          '【カルチュア・エンタテインメント株式会社（以下CEといいます）が主催するキャンペーンまたはCE へのご意見・ご要望及びTポイントの付与】<br>'.
                          '&nbsp;&nbsp;&nbsp;&nbsp;取得企業：CE及びCCC<br>'.
                          '<br>'.
                          '・今回ご入力いただいた個人情報は、ご意見・ご要望の収集、キャンペーン当選者へのご連絡及びTポイントの付与のみに利用いたします。ただし、個人情報と切り離して利用することを原則に、'.
                          '個人を特定することのできない各種情報を、各種マーケティングデータを作成するために使用することがあります。<br>'.
                          '<br>'.
                          '・今回ご入力いただいた個人情報は、お客様のご了承をいただかない限り、第三者に提供することはありません。ただし、法令等により開示を求められた場合、'.
                          '人の生命および身体または財産などの重大な利益を保護するために緊急を要する場合には、'.
                          'お客様にお断りすることなく当該情報を情報開示することがあります。'.
                          'また、お客様からのご依頼事項に対応するために必要な範囲で、事前に秘密保持契約を締結している当社の関連会社または業務委託先会社などに対して、当該情報を開示することがあります。<br>'.
                          '<br>'.
                          '・個人情報の取り扱いに関しては、個人情報保護方針もあわせてご覧ください。<br>'.
                          '&nbsp;&nbsp;&nbsp;&nbsp;-CCCの個人情報保護方針は<a href=\'https://www.ccc.co.jp/customer_management/privacy/index.html\'>こちら</a><br>'.
                          '&nbsp;&nbsp;&nbsp;&nbsp;-「TSUTAYAの個人情報保護方針」「TSUTAYAネットカンパニーの個人情報保護方針」は<a href=\'https://www.tsutaya-ltd.co.jp/privacy/index.html\'>こちら</a><br>'.
                          '&nbsp;&nbsp;&nbsp;&nbsp;-CEの個人情報保護方針は<a href=\'https://www.culture-ent.co.jp/pdf/privacyStatement.pdf\'>こちら</a><br>';    

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
        $result['work'] = [];
        foreach ($data['promotion_work'] as $work) {
            $saleType = strlen($work->jan) == 13 ? 'sell' : (strlen($work->jan) == 9 ? 'rental' : null);
            $result['work'][] = [
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
