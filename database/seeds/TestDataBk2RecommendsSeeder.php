<?php

use Illuminate\Database\Seeder;
use League\Csv\Reader;
use App\Model\Structure;

class TestDataBk2RecommendsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bk2Recommend = 'ts_bk2_recommends1';
        DB::table($bk2Recommend)->truncate();
        $insertData = [
            'work_id' => 'PTA0000G4CSA',
            'list_work_id' => 'PTA0000G66F0,PTA0000RWJMK,PTA0000GD16P',
            'created_at' => date('Y-m-d h:m:s'),
            'updated_at' => date('Y-m-d h:m:s')
        ];
        DB::table($bk2Recommend)->insert($insertData);

    }
}
