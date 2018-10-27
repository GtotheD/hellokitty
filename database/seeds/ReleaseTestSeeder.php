<?php

use Illuminate\Database\Seeder;
use App\Model\TopReleaseNewest;
use App\Model\TopReleaseLastest;
use Illuminate\Support\Carbon;

class ReleaseTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(TopReleaseNewest::TABLE)->truncate();
        $insertData = [
            [
                'month' => '2018-10-01',
                'tap_genre_id' => '1', // レンタルDVD
                'sort' => '1',
                'work_id' => 'PTA0000U874W',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'month' => '2018-10-01',
                'tap_genre_id' => '9', // 販売DVD
                'sort' => '1',
                'work_id' => 'PTA0000RUU8P',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'month' => '2018-10-01',
                'tap_genre_id' => '17', //  レンタルCD
                'sort' => '1',
                'work_id' => 'PTA0000WHPCK',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'month' => '2018-10-01',
                'tap_genre_id' => '22', // 販売CD
                'sort' => '1',
                'work_id' => 'PTA0000V6DXR',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'month' => '2018-10-01',
                'tap_genre_id' => '28', // レンタル本
                'sort' => '1',
                'work_id' => 'PTATESTBK02',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'month' => '2018-10-01',
                'tap_genre_id' => '39', // 販売本
                'sort' => '1',
                'work_id' => 'PTATESTBK02',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'month' => '2018-10-01',
                'tap_genre_id' => '51', // 販売ゲーム
                'sort' => '1',
                'work_id' => 'PTA0000WB5VA',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        DB::table(TopReleaseNewest::TABLE)->insert($insertData);
    }
}
