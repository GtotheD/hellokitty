<?php

namespace App\Repositories;

use App\Exceptions\NoContentsException;
use App\Model\RelatedPeople;

class SectionPremiumRecommend extends BaseRepository
{
    public function __construct($sort = 'asc', $offset = 0, $limit = 10)
    {
        parent::__construct($sort, $offset, $limit);
    }

    /**
     * @param $ignoreUrlCd
     * @return $this|bool
     */
    public function getWorks($ignoreUrlCd)
    {
        $himoRepository = new HimoRepository('asc', $this->offset, $this->limit);
        $workRepository = new WorkRepository();

        $data = $himoRepository->premiumRentalVideoRecommend($ignoreUrlCd)->get();
        // レスポンスがなかった場合または、ステータスが200以外（正常にとれなかった場合）
        if (empty($data['status']) && $data['status'] !== '200') {
            return false;
        }

        // ページネーションがある場合、次のページがあるかどうか
        if (count($data['results']['rows']) + $this->offset < $data['results']['total']) {
            $this->hasNext = true;
        } else {
            $this->hasNext = false;
        }
        $this->totalCount = $data['results']['total'];
        if ($data['results']['total'] === 0) {
            return false;
        }

        $displayImage = true;
        foreach ($data['results']['rows'] as $row) {
            $base = $workRepository->format($row);
            $itemType = $workRepository->convertWorkTypeIdToStr($base['work_type_id']);
            if ($base['work_format_id'] == $workRepository::WORK_FORMAT_ID_MUSICVIDEO) {
                $itemType = 'dvd';
            }
            $saleTypeHas = $workRepository->parseFromArray($row['products'], $itemType);
            $displayImage = true;
            $displayImage = checkAgeLimit(
                $this->ageLimitCheck,
                $base['rating_id'],
                $base['adult_flg'],
                $base['big_genre_id'],
                $base['medium_genre_id'],
                $base['small_genre_id'],
                $saleTypeHas['pickupProduct']['maker_cd']);
            // アダルト判定
            $isAdult = isAdult(
                $base['rating_id'],
                $base['big_genre_id'],
                $base['medium_genre_id'],
                $base['small_genre_id'],
                $saleTypeHas['pickupProduct']['maker_cd']
            );
            $result[] = [
                'workId' => $base['work_id'],
                'urlCd' => $base['url_cd'],
                'cccWorkCd' => $base['ccc_work_cd'],
                'workTitle' => $base['work_title'],
                // 最新巻及び最新日のものを取得する。
                'jacketL' => ($displayImage) ? trimImageTag($saleTypeHas['pickupProduct']['jacket_l']) : '',
                'newFlg' => newFlg($base['sale_start_date']),
                'adultFlg' => ($base['adult_flg'] === 1) ? true : $isAdult,
                'itemType' => $itemType,
                // レンタルで問い合わせている為、レンタルに固定
                'saleType' => 'rental',
                // todo:プレミアムフラグができたら渡す
                'is_premium' => true,
                // DVDの場合は空にする。
                'supplement' => ($itemType === 'dvd') ? '' : $saleTypeHas['supplement'],
                'saleStartDate' => ($saleTypeHas['pickupProduct']['sale_start_date']) ? date('Y-m-d 00:00:00', strtotime($saleTypeHas['pickupProduct']['sale_start_date'])) : '',
                'saleStartDateSell' => ($row['sale_start_date_sell']) ? date('Y-m-d 00:00:00', strtotime($row['sale_start_date_sell'])) : '',
                'saleStartDateRental' => ($row['sale_start_date_rental']) ? date('Y-m-d 00:00:00', strtotime($row['sale_start_date_rental'])) : '',
            ];
        }
        $this->rows = $result;
        return $this;
    }
}
