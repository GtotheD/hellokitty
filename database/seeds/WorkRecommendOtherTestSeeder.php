<?php

use Illuminate\Database\Seeder;
use App\Model\Recommend;

class WorkRecommendOtherTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $replaceViewQuery = sprintf("CREATE OR REPLACE VIEW ts_bk2_recommends AS SELECT * FROM %s",
            Recommend::TABLE . '1');
        DB::connection()->getPdo()->exec($replaceViewQuery);

        DB::table(Recommend::TABLE. '1')->truncate();
        $insertData = [
            [
                'work_id' => 'PTA00007Z7HS',
                'list_work_id' => 'PTATESTVI01',
            ],
            [
                'work_id' => 'PTA0000TCHXG',
                'list_work_id' => 'PTA00007XPBZ',
            ],
            [
                'work_id' => 'PTA0000THJL4',
                'list_work_id' => 'PTA000092WMF',
            ],
            [
                'work_id' => 'PTA000080QW6',
                'list_work_id' => 'PTA0000U62N9',
            ],
            [
                'work_id' => 'PTA0000QQV3A',
                'list_work_id' => 'PTATESTBK01',
            ],
            [
                'work_id' => 'PTA0000RWJMK',
                'list_work_id' => 'PTA0000G66F0',
            ],
            [
                'work_id' => 'PTA00007XQY7',
                'list_work_id' => 'PTATESTVI01',
            ],
            [
                'work_id' => 'PTA00007YLMH',
                'list_work_id' => 'PTA00007XPBZ',
            ],
            [
                'work_id' => 'PTA0000SQEHA',
                'list_work_id' => 'PTA000092WMF',
            ],
            [
                'work_id' => 'PTA0000VIYXA',
                'list_work_id' => 'PTA0000U62N9',
            ],
            [
                'work_id' => 'PTA0000G8MVQ',
                'list_work_id' => 'PTATESTBK01',
            ],
            [
                'work_id' => 'PTA0000H4C7V',
                'list_work_id' => 'PTA0000G66F0',
            ],
            [
                'work_id' => 'PTA0000FDFM9',
                'list_work_id' => 'PTA0000MZ9RR',
            ],
            [
                'work_id' => 'PTA0000R0IQC',
                'list_work_id' => 'PTA0000U8W8U',
            ],
        ];

        DB::table(Recommend::TABLE. '1')->insert($insertData);
    }
}
