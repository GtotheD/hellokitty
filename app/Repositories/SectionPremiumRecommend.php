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
    public function getWorks($ignoreUrlCd, $genre = null)
    {
        $himoRepository = new HimoRepository('asc', $this->offset, $this->limit);
        $workRepository = new WorkRepository();

        $data = $himoRepository->premiumRentalVideoRecommend($ignoreUrlCd, $genre)->get();
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

            $saleTypeHas = $workRepository->parseFromArrayPremium($row['products'], $itemType);
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
                'title' => $base['work_title'],
                // 最新巻及び最新日のものを取得する。
                //'imageUrl' => ($displayImage) ? trimImageTag($saleTypeHas['pickupProduct']['jacket_l']) : '',
                'imageUrl' => trimImageTag($saleTypeHas['pickupProduct']['jacket_l']),
                'newFlg' => newFlg($base['sale_start_date']),
                'adultFlg' => ($base['adult_flg'] === 1) ? true : $isAdult,
                'itemType' => $itemType,
                // レンタルで問い合わせている為、レンタルに固定
                'saleType' => 'rental',
                // todo:プレミアムフラグができたら渡す
                'isPremium' => true,
                //topレベルなので、1/0まででOKとする
                'premiumNetStatus' => $base['is_premium_net'],
                // DVDの場合は空にする。
                'supplement' => ($itemType === 'dvd') ? '' : $saleTypeHas['supplement'],
                'saleStartDate' => null,
                'saleStartDateSell' => ($row['sale_start_date_sell']) ? date('Y-m-d', strtotime($row['sale_start_date_sell'])) : '',
                'saleStartDateRental' => ($row['sale_start_date_rental']) ? date('Y-m-d', strtotime($row['sale_start_date_rental'])) : '',
            ];
        }
        $this->rows = $result;
        return $this;
    }

    /**
     * @param $ignoreUrlCd
     * @return $this|bool
     */
    public function getNetWorks()
    {
        //discasのAWSAPIから表示対象のttv_contents_cdを取得する
        $discasRepository = new DiscasRepository();
        $ttvContents = $discasRepository->ttvRecommendList()->get();

        $ttvCd = [];

        //oremium_recommend_listを取得する（手動編成なので、ないことも有る）
        if (!empty($ttvContents['items'])) {
            foreach($ttvContents['items'][0] as $key => $row) {
                if ($key === 'premium_recommend_list') {
                    foreach($row as $value) {
                        $ttvCd[] = $value['content_title_id'];
                    }
                }
            }
        }

        //ttvCdが取得できた
        if (count($ttvCd) > 0) {
            $himoRepository = new HimoRepository('asc', $this->offset, $this->limit);
            $data = $himoRepository->crosswork($ttvCd, '0106')->get();
            $workRepository = new WorkRepository();

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

                $saleTypeHas = $workRepository->parseFromArrayPremiumNet($row['products'], $itemType);

                $netStatus = 0;
                if ($saleTypeHas['vod'] === false) {
                    continue;
                }
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
                    'title' => $base['work_title'],
                    // 最新巻及び最新日のものを取得する。
                    //'imageUrl' => ($displayImage) ? trimImageTag($saleTypeHas['pickupProduct']['jacket_l']) : '',
                    'imageUrl' => trimImageTag($saleTypeHas['pickupProduct']['jacket_l']),
                    'newFlg' => newFlg($base['sale_start_date']),
                    'adultFlg' => ($base['adult_flg'] === 1) ? true : $isAdult,
                    'itemType' => $itemType,
                    // レンタルで問い合わせている為、レンタルに固定
                    'saleType' => 'rental',
                    'isPremium' => ($base['is_premium_shop'] === 1) ? true:false,
                    // todo:プレミアムフラグができたら渡す
                    'premiumNetStatus' => 1,
                    // DVDの場合は空にする。
                    'supplement' => ($itemType === 'dvd') ? '' : $saleTypeHas['supplement'],
                    'saleStartDate' => null,
                    'saleStartDateSell' => ($row['sale_start_date_sell']) ? date('Y-m-d', strtotime($row['sale_start_date_sell'])) : '',
                    'saleStartDateRental' => ($row['sale_start_date_rental']) ? date('Y-m-d', strtotime($row['sale_start_date_rental'])) : '',
                 ];
            }
            $this->rows = $result;
            return $this;
        }
    }
}

