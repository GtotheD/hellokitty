<?php

use Illuminate\Database\Seeder;

class PointDetailsTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table(\App\Model\PointDetails::TABLE)->truncate();

        $insertData = [
            [
                'mem_id' => '1',
                'membership_type' => 1,
                'point' => 100,
                'fixed_point_total' => 100,
                'fixed_point_min_limit_time' => '2018-11-01 00:00:00',
                'created_at' => '2018-10-15 05:00:00',
                'updated_at' => '2018-11-21 10:04:00'
            ],
            [
                'mem_id' => '2',
                'membership_type' => 2,
                'point' => 200,
                'fixed_point_total' => 200,
                'fixed_point_min_limit_time' => '2018-11-01 00:00:00',
                'created_at' => '2018-10-15 05:00:00',
                'updated_at' => '2018-10-15 06:00:00'
            ],
            [
                'mem_id' => '3',
                'membership_type' => 1,
                'point' => 0,
                'fixed_point_total' => 0,
                'fixed_point_min_limit_time' => '2018-11-01 00:00:00',
                'created_at' => '2018-10-15 05:00:00',
                'updated_at' => '2018-10-15 07:00:00'
            ],
        ];

        DB::table(\App\Model\PointDetails::TABLE)->insert($insertData);
    }
}
